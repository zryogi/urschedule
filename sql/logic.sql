-- This file contains the logic for the SQL queries used in the application.
-- It includes the necessary triggers, functions, and procedures to handle data manipulation and retrieval.

-- #1: Trigger para carga semanal del profesor < 44 horas.

CREATE OR REPLACE FUNCTION f_validar_carga_profesor()
RETURNS TRIGGER AS $$
DECLARE
	v_rol TEXT;
	v_fecha_inicio TIMESTAMPTZ;
	v_fecha_fin TIMESTAMPTZ;
	v_duracion NUMERIC;
	v_inicio_semana DATE;
	v_fin_semana DATE;
	v_total_horas NUMERIC;
BEGIN
	-- 1. Verificar si el usuario tiene rol de profesor
	SELECT r.name INTO v_rol
	FROM users u
	JOIN roles r ON u.role_id = r.id
	WHERE u.id = NEW.user_id;

	IF v_rol != 'professor' THEN
		RETURN NEW; -- Si no es profesor, permitir la operación.
	END IF;

	-- 2. Obtener las fechas del evento
	SELECT e.start_datetime, e.end_datetime
	INTO v_fecha_inicio, v_fecha_fin
	FROM events e
	WHERE e.id = NEW.event_id;

	-- 3. Calcular la duración del nuevo evento en horas
	v_duracion := EXTRACT(EPOCH FROM (v_fecha_fin - v_fecha_inicio)) / 3600;

	-- 4. Calcular inicio y fin de semana (domingo a sábado)
	v_inicio_semana := date_trunc('week', v_fecha_inicio)::DATE;
	v_fin_semana := v_inicio_semana + INTERVAL '6 days';

	-- 5. Sumar todas las horas del profesor en esa semana (excluye el evento actual en caso de UPDATE)
	SELECT COALESCE(SUM(EXTRACT(EPOCH FROM (ev.end_datetime - ev.start_datetime)) / 3600), 0)
	INTO v_total_horas
	FROM user_events ue
	JOIN events ev ON ue.event_id = ev.id
	WHERE ue.user_id = NEW.user_id
	  AND ev.start_datetime::DATE BETWEEN v_inicio_semana AND v_fin_semana
	  AND (TG_OP = 'INSERT' OR (TG_OP = 'UPDATE' AND ue.event_id != OLD.event_id));

	-- 6. Sumar la duración del nuevo evento
	v_total_horas := v_total_horas + v_duracion;

	-- 7. Validar si supera las 44 horas
	IF v_total_horas > 44 THEN
		RAISE EXCEPTION 'No se puede asignar este evento. Carga semanal para el profesor excede las 44 horas (%.2f horas asignadas)', v_total_horas;
	END IF;

	RETURN NEW;
END;
$$ LANGUAGE plpgsql;


CREATE TRIGGER tri_validar_carga_profesor
BEFORE INSERT OR UPDATE ON user_events
FOR EACH ROW EXECUTE FUNCTION f_validar_carga_profesor();


-- Pruebas
-- Insertar un evento (3 horas)
INSERT INTO events (title, start_datetime, end_datetime, event_type_id, created_by_user_id)
VALUES ('Clase de Álgebra', '2025-05-26 09:00', '2025-05-26 12:00', 1, 1);

-- Intentar asignar a un profesor (ya con 42 horas asignadas previamente)
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status)
VALUES (3, 10, TRUE, 'accepted'); -- Esto lanzará error.





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
