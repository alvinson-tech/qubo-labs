USE qubo_labs;

-- Clear attendance data
DELETE FROM attendance_records;
DELETE FROM attendance_sessions;
ALTER TABLE attendance_records AUTO_INCREMENT = 1;
ALTER TABLE attendance_sessions AUTO_INCREMENT = 1;

-- Clear all active sessions (logout everyone)
UPDATE students SET session_token = NULL, last_login = NULL;
UPDATE staff SET session_token = NULL, last_login = NULL;