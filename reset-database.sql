-- ============================================
-- COMPLETE DATABASE RESET SCRIPT
-- ============================================

USE qubo_labs;

-- Step 1: Clear all attendance-related data
-- This removes all attendance records and sessions
DELETE FROM attendance_records;
DELETE FROM attendance_sessions;

-- Step 2: Reset auto-increment counters
-- This ensures new records start from ID 1
ALTER TABLE attendance_records AUTO_INCREMENT = 1;
ALTER TABLE attendance_sessions AUTO_INCREMENT = 1;

-- Step 3: Clear all active sessions (logout all users)
-- This logs out all students and staff from the system
UPDATE students SET session_token = NULL, last_login = NULL;
UPDATE staff SET session_token = NULL, last_login = NULL;

-- ============================================
-- SUCCESS MESSAGE
-- ============================================
SELECT 'Database reset completed successfully! All attendance data cleared and all users logged out.' as Status;

-- ============================================
-- OPTIONAL: Complete System Reset
-- Uncomment the sections below if you want to:
-- 1. Reset all users (students and staff)
-- 2. Reset all classes
-- 3. Reset seminar halls and seats
-- ============================================

/*
-- OPTION A: Delete all students (keeps the student table structure)
DELETE FROM students;
ALTER TABLE students AUTO_INCREMENT = 1;

-- OPTION B: Delete all staff (keeps the staff table structure)
DELETE FROM staff;
ALTER TABLE staff AUTO_INCREMENT = 1;

-- OPTION C: Delete all classes
DELETE FROM classes;
ALTER TABLE classes AUTO_INCREMENT = 1;

-- OPTION D: Delete all seminar halls and their seats
DELETE FROM seminar_seats;
DELETE FROM seminar_halls;
ALTER TABLE seminar_seats AUTO_INCREMENT = 1;
ALTER TABLE seminar_halls AUTO_INCREMENT = 1;
*/