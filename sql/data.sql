INSERT INTO roles (id, name, description)
VALUES 
  (1, 'admin', 'Administrador con acceso a todo el sistema con permisos para asignar eventos a cualquier profesor'), -- Corrected name to enum value
  (2, 'professor', 'Profesor de la facultad a quien se le asignan eventos'); -- Corrected name to enum value

-- Professor Users (IDs 1-10)
INSERT INTO users (id, first_name, last_name, email, password_hash, role_id) VALUES (1, 'Levi', 'Scrooby', 'lscrooby0@ur.com', '$2y$10$d.YyzHVi5PZxHCDuqbi/auolitjkxNkH/tTCoC7C6nIiWkm7HX5sa', 2);
INSERT INTO users (id, first_name, last_name, email, password_hash, role_id) VALUES (2, 'Jere', 'Dufer', 'jdufer1@ur.com', '$2y$10$d.YyzHVi5PZxHCDuqbi/auolitjkxNkH/tTCoC7C6nIiWkm7HX5sa', 2);
INSERT INTO users (id, first_name, last_name, email, password_hash, role_id) VALUES (3, 'Lyndsey', 'Preon', 'lpreon2@ur.com', '$2y$10$d.YyzHVi5PZxHCDuqbi/auolitjkxNkH/tTCoC7C6nIiWkm7HX5sa', 2);
INSERT INTO users (id, first_name, last_name, email, password_hash, role_id) VALUES (4, 'Darrin', 'Mariot', 'dmariot3@ur.com', '$2y$10$d.YyzHVi5PZxHCDuqbi/auolitjkxNkH/tTCoC7C6nIiWkm7HX5sa', 2);
INSERT INTO users (id, first_name, last_name, email, password_hash, role_id) VALUES (5, 'Leonid', 'Circuit', 'lcircuit4@ur.com', '$2y$10$d.YyzHVi5PZxHCDuqbi/auolitjkxNkH/tTCoC7C6nIiWkm7HX5sa', 2);
INSERT INTO users (id, first_name, last_name, email, password_hash, role_id) VALUES (6, 'Edward', 'Lamberteschi', 'elamberteschi5@ur.com', '$2y$10$d.YyzHVi5PZxHCDuqbi/auolitjkxNkH/tTCoC7C6nIiWkm7HX5sa', 2);
INSERT INTO users (id, first_name, last_name, email, password_hash, role_id) VALUES (7, 'Agretha', 'Yousef', 'ayousef6@ur.com', '$2y$10$d.YyzHVi5PZxHCDuqbi/auolitjkxNkH/tTCoC7C6nIiWkm7HX5sa', 2);
INSERT INTO users (id, first_name, last_name, email, password_hash, role_id) VALUES (8, 'Arvy', 'Heintz', 'aheintz7@ur.com', '$2y$10$d.YyzHVi5PZxHCDuqbi/auolitjkxNkH/tTCoC7C6nIiWkm7HX5sa', 2);
INSERT INTO users (id, first_name, last_name, email, password_hash, role_id) VALUES (9, 'Domenico', 'Penticoot', 'dpenticoot8@ur.com', '$2y$10$d.YyzHVi5PZxHCDuqbi/auolitjkxNkH/tTCoC7C6nIiWkm7HX5sa', 2);
INSERT INTO users (id, first_name, last_name, email, password_hash, role_id) VALUES (10, 'Pat', 'Maleck', 'pmaleck9@ur.com', '$2y$10$d.YyzHVi5PZxHCDuqbi/auolitjkxNkH/tTCoC7C6nIiWkm7HX5sa', 2);

-- Admin Users (IDs 11-13, corrected from original 1-3 to avoid PK conflict)
INSERT INTO users (id, first_name, last_name, email, password_hash, role_id) VALUES (11, 'Maje', 'Lorek', 'mlorek0@ur.com', '$2y$10$d.YyzHVi5PZxHCDuqbi/auolitjkxNkH/tTCoC7C6nIiWkm7HX5sa', 1);
INSERT INTO users (id, first_name, last_name, email, password_hash, role_id) VALUES (12, 'Napoleon', 'Gligoraci', 'ngligoraci1@ur.com', '$2y$10$d.YyzHVi5PZxHCDuqbi/auolitjkxNkH/tTCoC7C6nIiWkm7HX5sa', 1);
INSERT INTO users (id, first_name, last_name, email, password_hash, role_id) VALUES (13, 'Murdock', 'Kemetz', 'mkemetz2@ur.com', '$2y$10$d.YyzHVi5PZxHCDuqbi/auolitjkxNkH/tTCoC7C6nIiWkm7HX5sa', 1);

-- Campuses
INSERT INTO campus (id, name, address) VALUES
(1, 'Claustro', 'Calle 12 # 3-45'),
(2, 'Mutis', 'Avenida Caracas # 60-78'),
(3, 'Innovación', 'Carrera 7 # 80-90');

-- Classrooms
-- Claustro Campus (Campus ID 1)
INSERT INTO classroom (id, campus_id, name) VALUES
(1, 1, 'Salon 101'),
(2, 1, 'Salon 102'),
(3, 1, 'Salon 201'),
(4, 1, 'Auditorio Menor'),
(5, 1, 'Laboratorio de Física');

-- Mutis Campus (Campus ID 2)
INSERT INTO classroom (id, campus_id, name) VALUES
(6, 2, 'Aula Maxima'),
(7, 2, 'Salon 301 Mutis'),
(8, 2, 'Salon 302 Mutis'),
(9, 2, 'Sala de Conferencias Mutis'),
(10, 2, 'Laboratorio de Química');

-- Innovación Campus (Campus ID 3)
INSERT INTO classroom (id, campus_id, name) VALUES
(11, 3, 'Salon Inteligente 1'),
(12, 3, 'Salon Inteligente 2'),
(13, 3, 'Hub de Innovación A'),
(14, 3, 'Sala de Proyectos B'),
(15, 3, 'Laboratorio de Computo Avanzado');

-- Event Types
INSERT INTO event_types (id, name, description)
VALUES
  (1, 'lecture', 'Standard university lecture session.'),
  (2, 'seminar', 'Interactive seminar with student participation.'),
  (3, 'talk', 'Guest talk or special presentation.'),
  (4, 'office_hours', 'Professor availability for student consultations.'),
  (5, 'meeting', 'Faculty or departmental meeting.'),
  (6, 'conference_presentation', 'Presentation at an academic conference.'),
  (7, 'workshop', 'Hands-on workshop session.'),
  (8, 'other', 'Any other type of academic event.');

-- Events (created by admin user ID 11, Maje Lorek)
-- Event IDs are specified for FK consistency in user_events.
-- Dates are around May 2025.

-- Week of May 19 - May 25, 2025
-- Note: classroom_id is used for physical rooms, location for other (e.g., online, external)
INSERT INTO events (id, title, description, start_datetime, end_datetime, event_type_id, classroom_id, location, created_by_user_id)
VALUES
  (1, 'Advanced Calculus Lecture', 'Covering chapters 5 and 6.', '2025-05-22 10:00:00-05', '2025-05-22 11:30:00-05', 1, 1, NULL, 11), -- Claustro, Salon 101
  (2, 'Faculty Meeting', 'Monthly department update and planning.', '2025-05-22 14:00:00-05', '2025-05-22 15:30:00-05', 5, 9, NULL, 11), -- Mutis, Sala de Conferencias Mutis
  (3, 'Student Office Hours - L. Scrooby', 'Open for COMP101 students.', '2025-05-23 09:00:00-05', '2025-05-23 11:00:00-05', 4, NULL, 'Online - Zoom Link Provided', 11),
  (4, 'AI Ethics Seminar', 'Discussion on recent AI advancements.', '2025-05-23 13:00:00-05', '2025-05-23 14:30:00-05', 2, 4, NULL, 11), -- Claustro, Auditorio Menor
  (5, 'Quantum Physics Workshop', 'Introductory workshop for physics majors.', '2025-05-24 10:00:00-05', '2025-05-24 13:00:00-05', 7, 5, NULL, 11); -- Claustro, Laboratorio de Física

-- Week of May 26 - June 1, 2025
INSERT INTO events (id, title, description, start_datetime, end_datetime, event_type_id, classroom_id, location, created_by_user_id)
VALUES
  (6, 'Data Structures Lecture', 'Topic: Trees and Graphs.', '2025-05-27 10:00:00-05', '2025-05-27 11:30:00-05', 1, 7, NULL, 11), -- Mutis, Salon 301 Mutis
  (7, 'Guest Talk: Future of Web Dev', 'By industry expert Jane Doe.', '2025-05-28 16:00:00-05', '2025-05-28 17:00:00-05', 3, 6, NULL, 11), -- Mutis, Aula Maxima
  (8, 'Office Hours - J. Dufer', 'For students of Advanced Algorithms.', '2025-05-29 11:00:00-05', '2025-05-29 12:00:00-05', 4, NULL, 'Prof. Dufer Office (Mutis)', 11),
  (9, 'Research Group Meeting', 'Weekly sync for the AI research team.', '2025-05-30 10:00:00-05', '2025-05-30 11:00:00-05', 5, NULL, 'Online - MS Teams', 11);

-- User Events (Assigning events to professors)
-- Event 1 (Calculus Lecture) assigned to Prof. Scrooby (ID 1)
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (1, 1, true, 'accepted');
-- Event 2 (Faculty Meeting) assigned to Prof. Scrooby (ID 1) and Prof. Dufer (ID 2)
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (1, 2, true, 'accepted');
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (2, 2, true, 'accepted');
-- Event 3 (Office Hours) for Prof. Scrooby (ID 1)
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (1, 3, true, 'accepted');
-- Event 4 (AI Ethics Seminar) assigned to Prof. Preon (ID 3) and Prof. Mariot (ID 4)
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (3, 4, true, 'accepted');
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (4, 4, false, 'tentative');
-- Event 5 (Quantum Physics Workshop) assigned to Prof. Circuit (ID 5)
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (5, 5, true, 'accepted');
-- Event 6 (Data Structures Lecture) assigned to Prof. Dufer (ID 2)
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (2, 6, true, 'accepted');
-- Event 7 (Guest Talk) assigned to Prof. Lamberteschi (ID 6) and Prof. Yousef (ID 7)
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (6, 7, false, 'no_response');
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (7, 7, false, 'no_response');
-- Event 8 (Office Hours) for Prof. Dufer (ID 2)
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (2, 8, true, 'accepted');
-- Event 9 (Research Group Meeting) assigned to Prof. Heintz (ID 8), Prof. Penticoot (ID 9), Prof. Maleck (ID 10)
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (8, 9, true, 'accepted');
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (9, 9, true, 'accepted');
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (10, 9, true, 'tentative');

-- Add a few more events for variety
INSERT INTO events (id, title, description, start_datetime, end_datetime, event_type_id, classroom_id, location, created_by_user_id)
VALUES
  (10, 'Thesis Defense Seminar', 'PhD Candidate Presentation', '2025-06-02 14:00:00-05', '2025-06-02 16:00:00-05', 2, 13, NULL, 12), -- Innovacion, Hub de Innovacion A
  (11, 'Curriculum Review Meeting', 'Review of undergraduate curriculum.', '2025-06-03 09:30:00-05', '2025-06-03 11:30:00-05', 5, NULL, 'Hotel Tequendama - Salon Rojo', 12),
  (12, 'Intro to Python Workshop', 'For non-CS majors.', '2025-06-04 13:00:00-05', '2025-06-04 15:00:00-05', 7, 15, NULL, 13), -- Innovacion, Laboratorio de Computo Avanzado
  (13, 'Webinar: Cybersecurity Trends', 'Online webinar for all students.', '2025-06-05 10:00:00-05', '2025-06-05 11:00:00-05', 3, NULL, 'Online - Webinar Platform', 11),
  (14, 'Postgraduate Info Session', 'Information session for new postgraduate programs.', '2025-06-06 17:00:00-05', '2025-06-06 18:30:00-05', 3, 2, NULL, 12); -- Claustro, Salon 102

-- Assign these new events
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (1, 10, false, 'tentative'); -- Prof. Scrooby to attend Thesis Defense
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (2, 10, false, 'accepted');  -- Prof. Dufer to attend Thesis Defense
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (3, 11, true, 'accepted');   -- Prof. Preon for Curriculum Review
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (4, 11, true, 'accepted');   -- Prof. Mariot for Curriculum Review
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (5, 12, true, 'no_response'); -- Prof. Circuit for Python Workshop
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (6, 12, true, 'no_response'); -- Prof. Lamberteschi for Python Workshop
-- Assign professors to events 13 and 14
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (7, 13, true, 'accepted'); -- Prof. Yousef for Webinar
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (8, 13, false, 'tentative'); -- Prof. Heintz for Webinar
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (9, 14, true, 'accepted'); -- Prof. Penticoot for Info Session
INSERT INTO user_events (user_id, event_id, is_mandatory, rsvp_status) VALUES (10, 14, true, 'accepted'); -- Prof. Maleck for Info Session