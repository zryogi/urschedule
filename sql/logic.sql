-- This file contains the logic for the SQL queries used in the application.
-- It includes the necessary triggers, functions, and procedures to handle data manipulation and retrieval.

-- #1: Function and trigger that validates teacher's weekly workload limit is not exceeded
CREATE OR REPLACE FUNCTION f_validate_professor_workload()
RETURNS TRIGGER AS $$
DECLARE
	v_start_datetime     TIMESTAMPTZ;
    	v_end_datetime       TIMESTAMPTZ;
	v_duration_hours     NUMERIC;
	v_week_start         DATE;
	v_week_end           DATE;
	v_total_hours        NUMERIC;
	v_is_recurring       BOOLEAN;
	v_occurrence_id      BIGINT;
	v_days_array         TEXT[];
	v_weekly_occurrences INT := 1;  -- At least 1 occurrence by default
	v_weekly_limit       NUMERIC := 5.0; -- This hours per week limit can be set with small numbers for testing
BEGIN
    -- Retrieve basic event information (event dates and recurrence info)
    SELECT e.start_datetime, e.end_datetime, e.is_recurring, e.occurrence_id
    INTO v_start_datetime, v_end_datetime, v_is_recurring, v_occurrence_id
    FROM events e
    WHERE e.id = NEW.event_id;

    -- Calculate the base event duration in hours
    v_duration_hours := EXTRACT(EPOCH FROM (v_end_datetime - v_start_datetime)) / 3600.0;

    -- Calculate week start and end of the event's week (Sunday to Saturday)
    v_week_start := date_trunc('week', v_start_datetime)::DATE;
    v_week_end := v_week_start + INTERVAL '6 days';

    -- If recurring, calculate recurrence days and count ocurrences during the week
    IF v_is_recurring AND v_occurrence_id IS NOT NULL THEN
        -- Get recurrence days as array
        SELECT string_to_array(days, ',')
        INTO v_days_array
        FROM event_recurrence_rule
        WHERE id = v_occurrence_id;

        -- Ensure recurrence configuration is valid
        IF v_days_array IS NULL OR array_length(v_days_array, 1) = 0 THEN
            RAISE EXCEPTION 'Recurring event has invalid configuration: recurrence days are empty or null (Rule ID: %)', v_occurrence_id;
        END IF;

        -- Count how many of those days occur within the current week
        SELECT COUNT(*)
        INTO v_weekly_occurrences
        FROM generate_series(
            (SELECT start_date FROM event_recurrence_rule WHERE id = v_occurrence_id),
            (SELECT end_date FROM event_recurrence_rule WHERE id = v_occurrence_id),
            INTERVAL '1 day'
        ) gs
        WHERE to_char(gs, 'DY') IN (SELECT UPPER(TRIM(d)) FROM unnest(v_days_array) AS d)
          AND gs::DATE BETWEEN v_week_start AND v_week_end;
    END IF;

    -- Calculate the professor’s already assigned workload on that week (exclude current event on UPDATE)
    SELECT COALESCE(SUM(EXTRACT(EPOCH FROM (ev.end_datetime - ev.start_datetime)) / 3600), 0)
    INTO v_total_hours
    FROM user_events ue
    JOIN events ev ON ue.event_id = ev.id
    WHERE ue.user_id = NEW.user_id
      AND ev.start_datetime::DATE BETWEEN v_week_start AND v_week_end
      AND (TG_OP = 'INSERT' OR (TG_OP = 'UPDATE' AND (ue.event_id != OLD.event_id OR ue.user_id != OLD.user_id)));

    -- Add new event's total hours (considering recurrence)
    v_total_hours := v_total_hours + (v_duration_hours * v_weekly_occurrences);

    -- Validate against weekly limit
    IF v_total_hours > v_weekly_limit THEN
        RAISE EXCEPTION 
            'Cannot assign event ID % (%). The professor''s weekly workload (% hrs) exceeds the allowed maximum of % hrs for the week % to %',
            NEW.event_id,
            (SELECT title FROM events WHERE id = NEW.event_id),
            TRUNC(v_total_hours)::TEXT,
            v_weekly_limit,
            v_week_start,
            v_week_end;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;


DROP TRIGGER IF EXISTS tri_validate_professor_workload ON user_events;
CREATE TRIGGER tri_validate_professor_workload
BEFORE INSERT OR UPDATE ON user_events
FOR EACH ROW
EXECUTE FUNCTION f_validate_professor_workload();


-- Tests
-- 1. Create recurrence rule (example: Monday, Tuesday, and Wednesday)
INSERT INTO event_recurrence_rule (id, start_date, end_date, start_time, end_time, number_occurrences, days)
VALUES (52, '2025-05-26', '2025-06-30', '09:00:00', '11:00:00', 15, 'MON,TUE,WED'); -- 2 hours per day

-- 2. Insert recurring event referencing that recurrence rule
INSERT INTO events (id, title, start_datetime, end_datetime, event_type_id, created_by_user_id, is_recurring, occurrence_id)
VALUES (501, 'Recurring Algebra Class', '2025-05-26 09:00:00', '2025-05-26 11:00:00', 1, 1, TRUE, 52);

-- 3. Insert a non-recurring event with 3 hours in the same week
INSERT INTO events (id, title, start_datetime, end_datetime, event_type_id, created_by_user_id, is_recurring)
VALUES (502, 'One-time Engineering Lecture', '2025-05-27 13:00:00', '2025-05-27 16:00:00', 2, 1, FALSE);

-- 4. Assign non-recurring event to professor (id 3)
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status)
VALUES (3, 502, TRUE, 'accepted'); -- 3 hours assigned

-- 5. Try to assign recurring event to the same professor (id 3)
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status)
VALUES (3, 501, TRUE, 'accepted'); -- This should fail because 3 + 6 = 9 > 5 hours limit

-- 6. Modify existing non-recurring event (id 502) to 4 hours (ends at 17:00 instead of 16:00)
UPDATE events
SET end_datetime = '2025-05-27 17:00:00'
WHERE id = 502; -- Should fail because new total 4 + 6 = 10 > 5 hours limit

-- 7. Reassign event (id 502) to another professor (id 4)
UPDATE user_events
SET user_id = 4
WHERE event_id = 502; -- Should succeed because professor 4 has no hours assigned yet

-- 8. Assign new non-conflicting and non-recurring event (2 hours on a different week) to professor (id 3)
INSERT INTO events (id, title, start_datetime, end_datetime, event_type_id, created_by_user_id)
VALUES (503, 'Calculus Seminar', '2025-06-03 09:00:00', '2025-06-03 11:00:00', 1, 1);

-- 9. Try to assign non-recurring event (id 503) to the same professor (id 3)
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status)
VALUES (3, 503, TRUE, 'accepted'); -- Should succeed, different week








-- #2: Trigger para agendamientos con fecha (u hora) posterior.

CREATE OR REPLACE FUNCTION f_fecha_posterior()
RETURNS TRIGGER AS $$
DECLARE
	v_rol TEXT;
BEGIN
	-- 1. Valida que la fecha de inicio del evento no sea en el pasado
	IF NEW.start_datetime < NOW() THEN
        RAISE EXCEPTION 'No se puede crear o actualizar un evento con fecha/hora de inicio en el pasado: %', NEW.start_datetime;
    END IF;

    -- 2. Validar que la fecha de fin no sea en el pasado (Si el evento termina antes de la hora actual, no se permite)
    IF NEW.end_datetime < NOW() THEN
        RAISE EXCEPTION 'No se puede crear o actualizar un evento que ya ha terminado: %', NEW.end_datetime;
    END IF;

    -- 3. Validar que la fecha y hora de fin sea posterior a la de inicio
    IF NEW.end_datetime <= NEW.start_datetime THEN
        RAISE EXCEPTION 'La fecha y hora de fin debe ser posterior a la de inicio. Inicio: %, Fin: %', NEW.start_datetime, NEW.end_datetime;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;


CREATE TRIGGER tri_fecha_posterior
BEFORE INSERT OR UPDATE ON events
FOR EACH ROW EXECUTE FUNCTION f_fecha_posterior();
