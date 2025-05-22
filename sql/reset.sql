-- This script deletes all data from the tables in the correct order to avoid foreign key conflicts.

-- Start by deleting from tables that are referenced by others (many-to-many or child tables)
DELETE FROM user_events;

-- Then delete from tables that are referenced by the ones above, or have fewer dependencies
DELETE FROM events;

-- Now delete data from tables that were referenced by 'events' or 'user_events'
DELETE FROM users; -- Note: 'events' has created_by_user_id
DELETE FROM event_recurrence_rule; -- Referenced by 'events'

-- Delete from tables that were referenced by 'users'
DELETE FROM work_day; -- Referenced by 'users'
DELETE FROM roles; -- Referenced by 'users'

-- Delete from tables that were referenced by 'events'
DELETE FROM classroom; -- Referenced by 'events'
DELETE FROM event_types; -- Referenced by 'events'

-- Finally, delete from tables with no remaining incoming foreign keys from the above tables
DELETE FROM campus; -- Referenced by 'classroom'

-- To reset auto-incrementing primary key sequences (optional, use with caution, TRUNCATE is often better for this but has other implications)
-- For PostgreSQL, you might need to reset sequences separately if you want IDs to start from 1 again, e.g.:
-- ALTER SEQUENCE users_id_seq RESTART WITH 1;
-- ALTER SEQUENCE roles_id_seq RESTART WITH 1;
-- ... and so on for all tables with BIGSERIAL or SERIAL PKs.
-- However, simple DELETE statements are sufficient for clearing data for testing purposes.
