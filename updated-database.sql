-- Create Database
CREATE DATABASE IF NOT EXISTS qubo_labs;
USE qubo_labs;

-- Staff Table
CREATE TABLE IF NOT EXISTS staff (
    staff_id INT PRIMARY KEY AUTO_INCREMENT,
    staff_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    department VARCHAR(100),
    session_token VARCHAR(255) NULL,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Classes Table
CREATE TABLE IF NOT EXISTS classes (
    class_id INT PRIMARY KEY AUTO_INCREMENT,
    class_name VARCHAR(50) NOT NULL,
    section VARCHAR(10),
    year INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students Table
CREATE TABLE IF NOT EXISTS students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    roll_number VARCHAR(50) UNIQUE NOT NULL,
    student_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    class_id INT,
    session_token VARCHAR(255) NULL,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(class_id)
);

-- Seminar Halls Table
CREATE TABLE IF NOT EXISTS seminar_halls (
    hall_id INT PRIMARY KEY AUTO_INCREMENT,
    hall_name VARCHAR(50) NOT NULL,
    room_number VARCHAR(10) NOT NULL,
    capacity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seminar Hall Seats Table (Updated)
CREATE TABLE IF NOT EXISTS seminar_seats (
    seat_id INT PRIMARY KEY AUTO_INCREMENT,
    hall_id INT NOT NULL,
    seat_number VARCHAR(20) NOT NULL,
    row_number INT NOT NULL,
    seat_position INT NOT NULL,
    qr_code VARCHAR(100) NOT NULL,
    UNIQUE KEY unique_seat_per_hall (hall_id, seat_number),
    UNIQUE KEY unique_qr_code (qr_code),
    FOREIGN KEY (hall_id) REFERENCES seminar_halls(hall_id)
);

-- Attendance Sessions Table (Updated with hall_id)
CREATE TABLE IF NOT EXISTS attendance_sessions (
    session_id INT PRIMARY KEY AUTO_INCREMENT,
    staff_id INT NOT NULL,
    class_id INT NOT NULL,
    hall_id INT NOT NULL,
    session_name VARCHAR(100) NOT NULL,
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time TIMESTAMP NULL,
    status ENUM('active', 'ended') DEFAULT 'active',
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id),
    FOREIGN KEY (class_id) REFERENCES classes(class_id),
    FOREIGN KEY (hall_id) REFERENCES seminar_halls(hall_id)
);

-- Attendance Records Table
CREATE TABLE IF NOT EXISTS attendance_records (
    record_id INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT NOT NULL,
    student_id INT NOT NULL,
    seat_id INT NOT NULL,
    verification_code VARCHAR(4) NOT NULL,
    scanned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    verified_at TIMESTAMP NULL,
    verified_by_student_id INT NULL,
    no_neighbours BOOLEAN DEFAULT FALSE,
    status ENUM('scanned', 'verified') DEFAULT 'scanned',
    FOREIGN KEY (session_id) REFERENCES attendance_sessions(session_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (seat_id) REFERENCES seminar_seats(seat_id),
    FOREIGN KEY (verified_by_student_id) REFERENCES students(student_id)
);

-- Insert Sample Data

-- Insert Staff
INSERT INTO staff (staff_name, email, password, department, session_token, last_login) VALUES
('Prof. Rekha P', 'rekha.p@mvj.edu', 'Rekha@123', 'Computer Science', NULL, NULL),
('Prof. Arfa Bhandari', 'arfa@mvj.edu', 'Arfa@456', 'Computer Science', NULL, NULL);

-- Insert Classes (Computer Science A, B, C, D)
INSERT INTO classes (class_name, section, year) VALUES
('Computer Science', 'A', 2024),
('Computer Science', 'B', 2024),
('Computer Science', 'C', 2024),
('Computer Science', 'D', 2024);

-- Insert Students
-- Section A (5 students)
INSERT INTO students (roll_number, student_name, email, password, class_id, session_token, last_login) VALUES
('CS005', 'Abhishek A', 'abhishek@stu.mvj.edu', 'Abhishek@005', 1, NULL, NULL),
('CS008', 'Aditya Suresh', 'aditya@stu.mvj.edu', 'Aditya@008', 1, NULL, NULL),
('CS012', 'Alvin Sonny', 'alvin@stu.mvj.edu', 'Alvin@012', 1, NULL, NULL),
('CS015', 'Ananya Sanjiv', 'ananya@stu.mvj.edu', 'Ananya@015', 1, NULL, NULL),
('CS018', 'Arjun Menon', 'arjun@stu.mvj.edu', 'Arjun@018', 1, NULL, NULL);

-- Section B (5 students)
INSERT INTO students (roll_number, student_name, email, password, class_id, session_token, last_login) VALUES
('CS058', 'Ganesha Thejaswi', 'ganesh@stu.mvj.edu', 'Ganesha@058', 2, NULL, NULL),
('CS061', 'Gowri Krishnan Nair', 'gowri@stu.mvj.edu', 'Gowri@061', 2, NULL, NULL),
('CS064', 'Harsha Kumar', 'harsha@stu.mvj.edu', 'Harsha@064', 2, NULL, NULL),
('CS067', 'Ishaan Reddy', 'ishaan@stu.mvj.edu', 'Ishaan@067', 2, NULL, NULL),
('CS070', 'Jaya Prakash', 'jaya@stu.mvj.edu', 'Jaya@070', 2, NULL, NULL);

-- Section C (5 students)
INSERT INTO students (roll_number, student_name, email, password, class_id, session_token, last_login) VALUES
('CS101', 'Karthik Sharma', 'karthik@stu.mvj.edu', 'Karthik@101', 3, NULL, NULL),
('CS104', 'Lakshmi Iyer', 'lakshmi@stu.mvj.edu', 'Lakshmi@104', 3, NULL, NULL),
('CS107', 'Manoj Kumar', 'manoj@stu.mvj.edu', 'Manoj@107', 3, NULL, NULL),
('CS110', 'Nisha Patel', 'nisha@stu.mvj.edu', 'Nisha@110', 3, NULL, NULL),
('CS113', 'Omkar Singh', 'omkar@stu.mvj.edu', 'Omkar@113', 3, NULL, NULL);

-- Section D (5 students)
INSERT INTO students (roll_number, student_name, email, password, class_id, session_token, last_login) VALUES
('CS145', 'Priya Ramesh', 'priya@stu.mvj.edu', 'Priya@145', 4, NULL, NULL),
('CS148', 'Rahul Verma', 'rahul@stu.mvj.edu', 'Rahul@148', 4, NULL, NULL),
('CS151', 'Sneha Desai', 'sneha@stu.mvj.edu', 'Sneha@151', 4, NULL, NULL),
('CS154', 'Tarun Gupta', 'tarun@stu.mvj.edu', 'Tarun@154', 4, NULL, NULL),
('CS157', 'Usha Nair', 'usha@stu.mvj.edu', 'Usha@157', 4, NULL, NULL);

-- Insert Seminar Halls
INSERT INTO seminar_halls (hall_name, room_number, capacity) VALUES
('Seminar Hall 1', '025', 104),
('Seminar Hall 2', '033', 104),
('Seminar Hall 3', '034', 104);

-- Insert Seminar Hall Seats
-- For each hall (1, 2, 3), create 104 seats:
-- Rows 1-4: 21 seats each
-- Row 5: 20 seats

-- Seminar Hall 1 (hall_id = 1)
-- Row 1: 21 seats
INSERT INTO seminar_seats (hall_id, seat_number, row_number, seat_position, qr_code) VALUES
(1, 'R1-S1', 1, 1, 'QR_H1_R1S1'), (1, 'R1-S2', 1, 2, 'QR_H1_R1S2'), (1, 'R1-S3', 1, 3, 'QR_H1_R1S3'),
(1, 'R1-S4', 1, 4, 'QR_H1_R1S4'), (1, 'R1-S5', 1, 5, 'QR_H1_R1S5'), (1, 'R1-S6', 1, 6, 'QR_H1_R1S6'),
(1, 'R1-S7', 1, 7, 'QR_H1_R1S7'), (1, 'R1-S8', 1, 8, 'QR_H1_R1S8'), (1, 'R1-S9', 1, 9, 'QR_H1_R1S9'),
(1, 'R1-S10', 1, 10, 'QR_H1_R1S10'), (1, 'R1-S11', 1, 11, 'QR_H1_R1S11'), (1, 'R1-S12', 1, 12, 'QR_H1_R1S12'),
(1, 'R1-S13', 1, 13, 'QR_H1_R1S13'), (1, 'R1-S14', 1, 14, 'QR_H1_R1S14'), (1, 'R1-S15', 1, 15, 'QR_H1_R1S15'),
(1, 'R1-S16', 1, 16, 'QR_H1_R1S16'), (1, 'R1-S17', 1, 17, 'QR_H1_R1S17'), (1, 'R1-S18', 1, 18, 'QR_H1_R1S18'),
(1, 'R1-S19', 1, 19, 'QR_H1_R1S19'), (1, 'R1-S20', 1, 20, 'QR_H1_R1S20'), (1, 'R1-S21', 1, 21, 'QR_H1_R1S21');

-- Row 2: 21 seats
INSERT INTO seminar_seats (hall_id, seat_number, row_number, seat_position, qr_code) VALUES
(1, 'R2-S1', 2, 1, 'QR_H1_R2S1'), (1, 'R2-S2', 2, 2, 'QR_H1_R2S2'), (1, 'R2-S3', 2, 3, 'QR_H1_R2S3'),
(1, 'R2-S4', 2, 4, 'QR_H1_R2S4'), (1, 'R2-S5', 2, 5, 'QR_H1_R2S5'), (1, 'R2-S6', 2, 6, 'QR_H1_R2S6'),
(1, 'R2-S7', 2, 7, 'QR_H1_R2S7'), (1, 'R2-S8', 2, 8, 'QR_H1_R2S8'), (1, 'R2-S9', 2, 9, 'QR_H1_R2S9'),
(1, 'R2-S10', 2, 10, 'QR_H1_R2S10'), (1, 'R2-S11', 2, 11, 'QR_H1_R2S11'), (1, 'R2-S12', 2, 12, 'QR_H1_R2S12'),
(1, 'R2-S13', 2, 13, 'QR_H1_R2S13'), (1, 'R2-S14', 2, 14, 'QR_H1_R2S14'), (1, 'R2-S15', 2, 15, 'QR_H1_R2S15'),
(1, 'R2-S16', 2, 16, 'QR_H1_R2S16'), (1, 'R2-S17', 2, 17, 'QR_H1_R2S17'), (1, 'R2-S18', 2, 18, 'QR_H1_R2S18'),
(1, 'R2-S19', 2, 19, 'QR_H1_R2S19'), (1, 'R2-S20', 2, 20, 'QR_H1_R2S20'), (1, 'R2-S21', 2, 21, 'QR_H1_R2S21');

-- Row 3: 21 seats
INSERT INTO seminar_seats (hall_id, seat_number, row_number, seat_position, qr_code) VALUES
(1, 'R3-S1', 3, 1, 'QR_H1_R3S1'), (1, 'R3-S2', 3, 2, 'QR_H1_R3S2'), (1, 'R3-S3', 3, 3, 'QR_H1_R3S3'),
(1, 'R3-S4', 3, 4, 'QR_H1_R3S4'), (1, 'R3-S5', 3, 5, 'QR_H1_R3S5'), (1, 'R3-S6', 3, 6, 'QR_H1_R3S6'),
(1, 'R3-S7', 3, 7, 'QR_H1_R3S7'), (1, 'R3-S8', 3, 8, 'QR_H1_R3S8'), (1, 'R3-S9', 3, 9, 'QR_H1_R3S9'),
(1, 'R3-S10', 3, 10, 'QR_H1_R3S10'), (1, 'R3-S11', 3, 11, 'QR_H1_R3S11'), (1, 'R3-S12', 3, 12, 'QR_H1_R3S12'),
(1, 'R3-S13', 3, 13, 'QR_H1_R3S13'), (1, 'R3-S14', 3, 14, 'QR_H1_R3S14'), (1, 'R3-S15', 3, 15, 'QR_H1_R3S15'),
(1, 'R3-S16', 3, 16, 'QR_H1_R3S16'), (1, 'R3-S17', 3, 17, 'QR_H1_R3S17'), (1, 'R3-S18', 3, 18, 'QR_H1_R3S18'),
(1, 'R3-S19', 3, 19, 'QR_H1_R3S19'), (1, 'R3-S20', 3, 20, 'QR_H1_R3S20'), (1, 'R3-S21', 3, 21, 'QR_H1_R3S21');

-- Row 4: 21 seats
INSERT INTO seminar_seats (hall_id, seat_number, row_number, seat_position, qr_code) VALUES
(1, 'R4-S1', 4, 1, 'QR_H1_R4S1'), (1, 'R4-S2', 4, 2, 'QR_H1_R4S2'), (1, 'R4-S3', 4, 3, 'QR_H1_R4S3'),
(1, 'R4-S4', 4, 4, 'QR_H1_R4S4'), (1, 'R4-S5', 4, 5, 'QR_H1_R4S5'), (1, 'R4-S6', 4, 6, 'QR_H1_R4S6'),
(1, 'R4-S7', 4, 7, 'QR_H1_R4S7'), (1, 'R4-S8', 4, 8, 'QR_H1_R4S8'), (1, 'R4-S9', 4, 9, 'QR_H1_R4S9'),
(1, 'R4-S10', 4, 10, 'QR_H1_R4S10'), (1, 'R4-S11', 4, 11, 'QR_H1_R4S11'), (1, 'R4-S12', 4, 12, 'QR_H1_R4S12'),
(1, 'R4-S13', 4, 13, 'QR_H1_R4S13'), (1, 'R4-S14', 4, 14, 'QR_H1_R4S14'), (1, 'R4-S15', 4, 15, 'QR_H1_R4S15'),
(1, 'R4-S16', 4, 16, 'QR_H1_R4S16'), (1, 'R4-S17', 4, 17, 'QR_H1_R4S17'), (1, 'R4-S18', 4, 18, 'QR_H1_R4S18'),
(1, 'R4-S19', 4, 19, 'QR_H1_R4S19'), (1, 'R4-S20', 4, 20, 'QR_H1_R4S20'), (1, 'R4-S21', 4, 21, 'QR_H1_R4S21');

-- Row 5: 20 seats
INSERT INTO seminar_seats (hall_id, seat_number, row_number, seat_position, qr_code) VALUES
(1, 'R5-S1', 5, 1, 'QR_H1_R5S1'), (1, 'R5-S2', 5, 2, 'QR_H1_R5S2'), (1, 'R5-S3', 5, 3, 'QR_H1_R5S3'),
(1, 'R5-S4', 5, 4, 'QR_H1_R5S4'), (1, 'R5-S5', 5, 5, 'QR_H1_R5S5'), (1, 'R5-S6', 5, 6, 'QR_H1_R5S6'),
(1, 'R5-S7', 5, 7, 'QR_H1_R5S7'), (1, 'R5-S8', 5, 8, 'QR_H1_R5S8'), (1, 'R5-S9', 5, 9, 'QR_H1_R5S9'),
(1, 'R5-S10', 5, 10, 'QR_H1_R5S10'), (1, 'R5-S11', 5, 11, 'QR_H1_R5S11'), (1, 'R5-S12', 5, 12, 'QR_H1_R5S12'),
(1, 'R5-S13', 5, 13, 'QR_H1_R5S13'), (1, 'R5-S14', 5, 14, 'QR_H1_R5S14'), (1, 'R5-S15', 5, 15, 'QR_H1_R5S15'),
(1, 'R5-S16', 5, 16, 'QR_H1_R5S16'), (1, 'R5-S17', 5, 17, 'QR_H1_R5S17'), (1, 'R5-S18', 5, 18, 'QR_H1_R5S18'),
(1, 'R5-S19', 5, 19, 'QR_H1_R5S19'), (1, 'R5-S20', 5, 20, 'QR_H1_R5S20');

-- Seminar Hall 2 (hall_id = 2) - Same pattern
INSERT INTO auditorium_seats (hall_id, seat_number, row_number, seat_position, qr_code) VALUES (2, 'R1-S1', 1, 1, 'QR_033_R1S1'), (2, 'R1-S2', 1, 2, 'QR_033_R1S2'), (2, 'R1-S3', 1, 3, 'QR_033_R1S3'), (2, 'R1-S4', 1, 4, 'QR_033_R1S4'), (2, 'R1-S5', 1, 5, 'QR_033_R1S5'), (2, 'R1-S6', 1, 6, 'QR_033_R1S6'), (2, 'R1-S7', 1, 7, 'QR_033_R1S7'), (2, 'R1-S8', 1, 8, 'QR_033_R1S8'), (2, 'R1-S9', 1, 9, 'QR_033_R1S9'), (2, 'R1-S10', 1, 10, 'QR_033_R1S10'), (2, 'R1-S11', 1, 11, 'QR_033_R1S11'), (2, 'R1-S12', 1, 12, 'QR_033_R1S12'), (2, 'R1-S13', 1, 13, 'QR_033_R1S13'), (2, 'R1-S14', 1, 14, 'QR_033_R1S14'), (2, 'R1-S15', 1, 15, 'QR_033_R1S15'), (2, 'R1-S16', 1, 16, 'QR_033_R1S16'), (2, 'R1-S17', 1, 17, 'QR_033_R1S17'), (2, 'R1-S18', 1, 18, 'QR_033_R1S18'), (2, 'R1-S19', 1, 19, 'QR_033_R1S19'), (2, 'R1-S20', 1, 20, 'QR_033_R1S20'), (2, 'R1-S21', 1, 21, 'QR_033_R1S21'), (2, 'R2-S1', 2, 1, 'QR_033_R2S1'), (2, 'R2-S2', 2, 2, 'QR_033_R2S2'), (2, 'R2-S3', 2, 3, 'QR_033_R2S3'), (2, 'R2-S4', 2, 4, 'QR_033_R2S4'), (2, 'R2-S5', 2, 5, 'QR_033_R2S5'), (2, 'R2-S6', 2, 6, 'QR_033_R2S6'), (2, 'R2-S7', 2, 7, 'QR_033_R2S7'), (2, 'R2-S8', 2, 8, 'QR_033_R2S8'), (2, 'R2-S9', 2, 9, 'QR_033_R2S9'), (2, 'R2-S10', 2, 10, 'QR_033_R2S10'), (2, 'R2-S11', 2, 11, 'QR_033_R2S11'), (2, 'R2-S12', 2, 12, 'QR_033_R2S12'), (2, 'R2-S13', 2, 13, 'QR_033_R2S13'), (2, 'R2-S14', 2, 14, 'QR_033_R2S14'), (2, 'R2-S15', 2, 15, 'QR_033_R2S15'), (2, 'R2-S16', 2, 16, 'QR_033_R2S16'), (2, 'R2-S17', 2, 17, 'QR_033_R2S17'), (2, 'R2-S18', 2, 18, 'QR_033_R2S18'), (2, 'R2-S19', 2, 19, 'QR_033_R2S19'), (2, 'R2-S20', 2, 20, 'QR_033_R2S20'), (2, 'R2-S21', 2, 21, 'QR_033_R2S21'), (2, 'R3-S1', 3, 1, 'QR_033_R3S1'), (2, 'R3-S2', 3, 2, 'QR_033_R3S2'), (2, 'R3-S3', 3, 3, 'QR_033_R3S3'), (2, 'R3-S4', 3, 4, 'QR_033_R3S4'), (2, 'R3-S5', 3, 5, 'QR_033_R3S5'), (2, 'R3-S6', 3, 6, 'QR_033_R3S6'), (2, 'R3-S7', 3, 7, 'QR_033_R3S7'), (2, 'R3-S8', 3, 8, 'QR_033_R3S8'), (2, 'R3-S9', 3, 9, 'QR_033_R3S9'), (2, 'R3-S10', 3, 10, 'QR_033_R3S10'), (2, 'R3-S11', 3, 11, 'QR_033_R3S11'), (2, 'R3-S12', 3, 12, 'QR_033_R3S12'), (2, 'R3-S13', 3, 13, 'QR_033_R3S13'), (2, 'R3-S14', 3, 14, 'QR_033_R3S14'), (2, 'R3-S15', 3, 15, 'QR_033_R3S15'), (2, 'R3-S16', 3, 16, 'QR_033_R3S16'), (2, 'R3-S17', 3, 17, 'QR_033_R3S17'), (2, 'R3-S18', 3, 18, 'QR_033_R3S18'), (2, 'R3-S19', 3, 19, 'QR_033_R3S19'), (2, 'R3-S20', 3, 20, 'QR_033_R3S20'), (2, 'R3-S21', 3, 21, 'QR_033_R3S21'), (2, 'R4-S1', 4, 1, 'QR_033_R4S1'), (2, 'R4-S2', 4, 2, 'QR_033_R4S2'), (2, 'R4-S3', 4, 3, 'QR_033_R4S3'), (2, 'R4-S4', 4, 4, 'QR_033_R4S4'), (2, 'R4-S5', 4, 5, 'QR_033_R4S5'), (2, 'R4-S6', 4, 6, 'QR_033_R4S6'), (2, 'R4-S7', 4, 7, 'QR_033_R4S7'), (2, 'R4-S8', 4, 8, 'QR_033_R4S8'), (2, 'R4-S9', 4, 9, 'QR_033_R4S9'), (2, 'R4-S10', 4, 10, 'QR_033_R4S10'), (2, 'R4-S11', 4, 11, 'QR_033_R4S11'), (2, 'R4-S12', 4, 12, 'QR_033_R4S12'), (2, 'R4-S13', 4, 13, 'QR_033_R4S13'), (2, 'R4-S14', 4, 14, 'QR_033_R4S14'), (2, 'R4-S15', 4, 15, 'QR_033_R4S15'), (2, 'R4-S16', 4, 16, 'QR_033_R4S16'), (2, 'R4-S17', 4, 17, 'QR_033_R4S17'), (2, 'R4-S18', 4, 18, 'QR_033_R4S18'), (2, 'R4-S19', 4, 19, 'QR_033_R4S19'), (2, 'R4-S20', 4, 20, 'QR_033_R4S20'), (2, 'R4-S21', 4, 21, 'QR_033_R4S21'), (2, 'R5-S1', 5, 1, 'QR_033_R5S1'), (2, 'R5-S2', 5, 2, 'QR_033_R5S2'), (2, 'R5-S3', 5, 3, 'QR_033_R5S3'), (2, 'R5-S4', 5, 4, 'QR_033_R5S4'), (2, 'R5-S5', 5, 5, 'QR_033_R5S5'), (2, 'R5-S6', 5, 6, 'QR_033_R5S6'), (2, 'R5-S7', 5, 7, 'QR_033_R5S7'), (2, 'R5-S8', 5, 8, 'QR_033_R5S8'), (2, 'R5-S9', 5, 9, 'QR_033_R5S9'), (2, 'R5-S10', 5, 10, 'QR_033_R5S10'), (2, 'R5-S11', 5, 11, 'QR_033_R5S11'), (2, 'R5-S12', 5, 12, 'QR_033_R5S12'), (2, 'R5-S13', 5, 13, 'QR_033_R5S13'), (2, 'R5-S14', 5, 14, 'QR_033_R5S14'), (2, 'R5-S15', 5, 15, 'QR_033_R5S15'), (2, 'R5-S16', 5, 16, 'QR_033_R5S16'), (2, 'R5-S17', 5, 17, 'QR_033_R5S17'), (2, 'R5-S18', 5, 18, 'QR_033_R5S18'), (2, 'R5-S19', 5, 19, 'QR_033_R5S19'), (2, 'R5-S20', 5, 20, 'QR_033_R5S20');

-- Inserts for Seminar Hall 3 (Room 034)
INSERT INTO auditorium_seats (hall_id, seat_number, row_number, seat_position, qr_code) VALUES
(3, 'R1-S1', 1, 1, 'QR_034_R1S1'), (3, 'R1-S2', 1, 2, 'QR_034_R1S2'), (3, 'R1-S3', 1, 3, 'QR_034_R1S3'),
(3, 'R1-S4', 1, 4, 'QR_034_R1S4'), (3, 'R1-S5', 1, 5, 'QR_034_R1S5'), (3, 'R1-S6', 1, 6, 'QR_034_R1S6'),
(3, 'R1-S7', 1, 7, 'QR_034_R1S7'), (3, 'R1-S8', 1, 8, 'QR_034_R1S8'), (3, 'R1-S9', 1, 9, 'QR_034_R1S9'),
(3, 'R1-S10', 1, 10, 'QR_034_R1S10'), (3, 'R1-S11', 1, 11, 'QR_034_R1S11'), (3, 'R1-S12', 1, 12, 'QR_034_R1S12'),
(3, 'R1-S13', 1, 13, 'QR_034_R1S13'), (3, 'R1-S14', 1, 14, 'QR_034_R1S14'), (3, 'R1-S15', 1, 15, 'QR_034_R1S15'),
(3, 'R1-S16', 1, 16, 'QR_034_R1S16'), (3, 'R1-S17', 1, 17, 'QR_034_R1S17'), (3, 'R1-S18', 1, 18, 'QR_034_R1S18'),
(3, 'R1-S19', 1, 19, 'QR_034_R1S19'), (3, 'R1-S20', 1, 20, 'QR_034_R1S20'), (3, 'R1-S21', 1, 21, 'QR_034_R1S21'),
(3, 'R2-S1', 2, 1, 'QR_034_R2S1'), (3, 'R2-S2', 2, 2, 'QR_034_R2S2'), (3, 'R2-S3', 2, 3, 'QR_034_R2S3'),
(3, 'R2-S4', 2, 4, 'QR_034_R2S4'), (3, 'R2-S5', 2, 5, 'QR_034_R2S5'), (3, 'R2-S6', 2, 6, 'QR_034_R2S6'),
(3, 'R2-S7', 2, 7, 'QR_034_R2S7'), (3, 'R2-S8', 2, 8, 'QR_034_R2S8'), (3, 'R2-S9', 2, 9, 'QR_034_R2S9'),
(3, 'R2-S10', 2, 10, 'QR_034_R2S10'), (3, 'R2-S11', 2, 11, 'QR_034_R2S11'), (3, 'R2-S12', 2, 12, 'QR_034_R2S12'),
(3, 'R2-S13', 2, 13, 'QR_034_R2S13'), (3, 'R2-S14', 2, 14, 'QR_034_R2S14'), (3, 'R2-S15', 2, 15, 'QR_034_R2S15'),
(3, 'R2-S16', 2, 16, 'QR_034_R2S16'), (3, 'R2-S17', 2, 17, 'QR_034_R2S17'), (3, 'R2-S18', 2, 18, 'QR_034_R2S18'),
(3, 'R2-S19', 2, 19, 'QR_034_R2S19'), (3, 'R2-S20', 2, 20, 'QR_034_R2S20'), (3, 'R2-S21', 2, 21, 'QR_034_R2S21'),
(3, 'R3-S1', 3, 1, 'QR_034_R3S1'), (3, 'R3-S2', 3, 2, 'QR_034_R3S2'), (3, 'R3-S3', 3, 3, 'QR_034_R3S3'),
(3, 'R3-S4', 3, 4, 'QR_034_R3S4'), (3, 'R3-S5', 3, 5, 'QR_034_R3S5'), (3, 'R3-S6', 3, 6, 'QR_034_R3S6'),
(3, 'R3-S7', 3, 7, 'QR_034_R3S7'), (3, 'R3-S8', 3, 8, 'QR_034_R3S8'), (3, 'R3-S9', 3, 9, 'QR_034_R3S9'),
(3, 'R3-S10', 3, 10, 'QR_034_R3S10'), (3, 'R3-S11', 3, 11, 'QR_034_R3S11'), (3, 'R3-S12', 3, 12, 'QR_034_R3S12'),
(3, 'R3-S13', 3, 13, 'QR_034_R3S13'), (3, 'R3-S14', 3, 14, 'QR_034_R3S14'), (3, 'R3-S15', 3, 15, 'QR_034_R3S15'),
(3, 'R3-S16', 3, 16, 'QR_034_R3S16'), (3, 'R3-S17', 3, 17, 'QR_034_R3S17'), (3, 'R3-S18', 3, 18, 'QR_034_R3S18'),
(3, 'R3-S19', 3, 19, 'QR_034_R3S19'), (3, 'R3-S20', 3, 20, 'QR_034_R3S20'), (3, 'R3-S21', 3, 21, 'QR_034_R3S21'),
(3, 'R4-S1', 4, 1, 'QR_034_R4S1'), (3, 'R4-S2', 4, 2, 'QR_034_R4S2'), (3, 'R4-S3', 4, 3, 'QR_034_R4S3'),
(3, 'R4-S4', 4, 4, 'QR_034_R4S4'), (3, 'R4-S5', 4, 5, 'QR_034_R4S5'), (3, 'R4-S6', 4, 6, 'QR_034_R4S6'),
(3, 'R4-S7', 4, 7, 'QR_034_R4S7'), (3, 'R4-S8', 4, 8, 'QR_034_R4S8'), (3, 'R4-S9', 4, 9, 'QR_034_R4S9'),
(3, 'R4-S10', 4, 10, 'QR_034_R4S10'), (3, 'R4-S11', 4, 11, 'QR_034_R4S11'), (3, 'R4-S12', 4, 12, 'QR_034_R4S12'),
(3, 'R4-S13', 4, 13, 'QR_034_R4S13'), (3, 'R4-S14', 4, 14, 'QR_034_R4S14'), (3, 'R4-S15', 4, 15, 'QR_034_R4S15'),
(3, 'R4-S16', 4, 16, 'QR_034_R4S16'), (3, 'R4-S17', 4, 17, 'QR_034_R4S17'), (3, 'R4-S18', 4, 18, 'QR_034_R4S18'),
(3, 'R4-S19', 4, 19, 'QR_034_R4S19'), (3, 'R4-S20', 4, 20, 'QR_034_R4S20'), (3, 'R4-S21', 4, 21, 'QR_034_R4S21'),
(3, 'R5-S1', 5, 1, 'QR_034_R5S1'), (3, 'R5-S2', 5, 2, 'QR_034_R5S2'), (3, 'R5-S3', 5, 3, 'QR_034_R5S3'),
(3, 'R5-S4', 5, 4, 'QR_034_R5S4'), (3, 'R5-S5', 5, 5, 'QR_034_R5S5'), (3, 'R5-S6', 5, 6, 'QR_034_R5S6'),
(3, 'R5-S7', 5, 7, 'QR_034_R5S7'), (3, 'R5-S8', 5, 8, 'QR_034_R5S8'), (3, 'R5-S9', 5, 9, 'QR_034_R5S9'),
(3, 'R5-S10', 5, 10, 'QR_034_R5S10'), (3, 'R5-S11', 5, 11, 'QR_034_R5S11'), (3, 'R5-S12', 5, 12, 'QR_034_R5S12'),
(3, 'R5-S13', 5, 13, 'QR_034_R5S13'), (3, 'R5-S14', 5, 14, 'QR_034_R5S14'), (3, 'R5-S15', 5, 15, 'QR_034_R5S15'),
(3, 'R5-S16', 5, 16, 'QR_034_R5S16'), (3, 'R5-S17', 5, 17, 'QR_034_R5S17'), (3, 'R5-S18', 5, 18, 'QR_034_R5S18'),
(3, 'R5-S19', 5, 19, 'QR_034_R5S19'), (3, 'R5-S20', 5, 20, 'QR_034_R5S20');