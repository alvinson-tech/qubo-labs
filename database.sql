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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(class_id)
);

-- Auditorium Seats Table
CREATE TABLE IF NOT EXISTS auditorium_seats (
    seat_id INT PRIMARY KEY AUTO_INCREMENT,
    seat_number VARCHAR(20) UNIQUE NOT NULL,
    row_number INT NOT NULL,
    seat_position INT NOT NULL,
    qr_code VARCHAR(100) UNIQUE NOT NULL
);

-- Attendance Sessions Table
CREATE TABLE IF NOT EXISTS attendance_sessions (
    session_id INT PRIMARY KEY AUTO_INCREMENT,
    staff_id INT NOT NULL,
    class_id INT NOT NULL,
    session_name VARCHAR(100) NOT NULL,
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time TIMESTAMP NULL,
    status ENUM('active', 'ended') DEFAULT 'active',
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id),
    FOREIGN KEY (class_id) REFERENCES classes(class_id)
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
    FOREIGN KEY (seat_id) REFERENCES auditorium_seats(seat_id),
    FOREIGN KEY (verified_by_student_id) REFERENCES students(student_id)
);

-- Insert Sample Data

-- Insert Staff
INSERT INTO staff (staff_name, email, password, department) VALUES
('Prof. Rekha P', 'rekha.p@mvj.edu', 'Rekha@123', 'Computer Science'),
('Prof. Arfa Bhandari', 'arfa@mvj.edu', 'Arfa@456', 'Computer Science');

-- Insert Classes (Computer Science A, B, C, D)
INSERT INTO classes (class_name, section, year) VALUES
('Computer Science', 'A', 2024),
('Computer Science', 'B', 2024),
('Computer Science', 'C', 2024),
('Computer Science', 'D', 2024);

-- Insert Students
-- Section A (5 students)
INSERT INTO students (roll_number, student_name, email, password, class_id) VALUES
('CS005', 'Abhishek A', 'abhishek@stu.mvj.edu', 'Abhishek@005', 1),
('CS008', 'Aditya Suresh', 'aditya@stu.mvj.edu', 'Aditya@008', 1),
('CS012', 'Alvin Sonny', 'alvin@stu.mvj.edu', 'Alvin@012', 1),
('CS015', 'Ananya Sanjiv', 'ananya@stu.mvj.edu', 'Ananya@015', 1),
('CS018', 'Arjun Menon', 'arjun@stu.mvj.edu', 'Arjun@018', 1);

-- Section B (5 students)
INSERT INTO students (roll_number, student_name, email, password, class_id) VALUES
('CS058', 'Ganesha Thejaswi', 'ganesh@stu.mvj.edu', 'Ganesha@058', 2),
('CS061', 'Gowri Krishnan Nair', 'gowri@stu.mvj.edu', 'Gowri@061', 2),
('CS064', 'Harsha Kumar', 'harsha@stu.mvj.edu', 'Harsha@064', 2),
('CS067', 'Ishaan Reddy', 'ishaan@stu.mvj.edu', 'Ishaan@067', 2),
('CS070', 'Jaya Prakash', 'jaya@stu.mvj.edu', 'Jaya@070', 2);

-- Section C (5 students)
INSERT INTO students (roll_number, student_name, email, password, class_id) VALUES
('CS101', 'Karthik Sharma', 'karthik@stu.mvj.edu', 'Karthik@101', 3),
('CS104', 'Lakshmi Iyer', 'lakshmi@stu.mvj.edu', 'Lakshmi@104', 3),
('CS107', 'Manoj Kumar', 'manoj@stu.mvj.edu', 'Manoj@107', 3),
('CS110', 'Nisha Patel', 'nisha@stu.mvj.edu', 'Nisha@110', 3),
('CS113', 'Omkar Singh', 'omkar@stu.mvj.edu', 'Omkar@113', 3);

-- Section D (5 students)
INSERT INTO students (roll_number, student_name, email, password, class_id) VALUES
('CS145', 'Priya Ramesh', 'priya@stu.mvj.edu', 'Priya@145', 4),
('CS148', 'Rahul Verma', 'rahul@stu.mvj.edu', 'Rahul@148', 4),
('CS151', 'Sneha Desai', 'sneha@stu.mvj.edu', 'Sneha@151', 4),
('CS154', 'Tarun Gupta', 'tarun@stu.mvj.edu', 'Tarun@154', 4),
('CS157', 'Usha Nair', 'usha@stu.mvj.edu', 'Usha@157', 4);

-- Insert Auditorium Seats (10 rows x 10 seats = 100 seats)
INSERT INTO auditorium_seats (seat_number, row_number, seat_position, qr_code) VALUES
('A1', 1, 1, 'QR_A1'), ('A2', 1, 2, 'QR_A2'), ('A3', 1, 3, 'QR_A3'), ('A4', 1, 4, 'QR_A4'), ('A5', 1, 5, 'QR_A5'),
('A6', 1, 6, 'QR_A6'), ('A7', 1, 7, 'QR_A7'), ('A8', 1, 8, 'QR_A8'), ('A9', 1, 9, 'QR_A9'), ('A10', 1, 10, 'QR_A10'),
('B1', 2, 1, 'QR_B1'), ('B2', 2, 2, 'QR_B2'), ('B3', 2, 3, 'QR_B3'), ('B4', 2, 4, 'QR_B4'), ('B5', 2, 5, 'QR_B5'),
('B6', 2, 6, 'QR_B6'), ('B7', 2, 7, 'QR_B7'), ('B8', 2, 8, 'QR_B8'), ('B9', 2, 9, 'QR_B9'), ('B10', 2, 10, 'QR_B10'),
('C1', 3, 1, 'QR_C1'), ('C2', 3, 2, 'QR_C2'), ('C3', 3, 3, 'QR_C3'), ('C4', 3, 4, 'QR_C4'), ('C5', 3, 5, 'QR_C5'),
('C6', 3, 6, 'QR_C6'), ('C7', 3, 7, 'QR_C7'), ('C8', 3, 8, 'QR_C8'), ('C9', 3, 9, 'QR_C9'), ('C10', 3, 10, 'QR_C10'),
('D1', 4, 1, 'QR_D1'), ('D2', 4, 2, 'QR_D2'), ('D3', 4, 3, 'QR_D3'), ('D4', 4, 4, 'QR_D4'), ('D5', 4, 5, 'QR_D5'),
('D6', 4, 6, 'QR_D6'), ('D7', 4, 7, 'QR_D7'), ('D8', 4, 8, 'QR_D8'), ('D9', 4, 9, 'QR_D9'), ('D10', 4, 10, 'QR_D10'),
('E1', 5, 1, 'QR_E1'), ('E2', 5, 2, 'QR_E2'), ('E3', 5, 3, 'QR_E3'), ('E4', 5, 4, 'QR_E4'), ('E5', 5, 5, 'QR_E5'),
('E6', 5, 6, 'QR_E6'), ('E7', 5, 7, 'QR_E7'), ('E8', 5, 8, 'QR_E8'), ('E9', 5, 9, 'QR_E9'), ('E10', 5, 10, 'QR_E10'),
('F1', 6, 1, 'QR_F1'), ('F2', 6, 2, 'QR_F2'), ('F3', 6, 3, 'QR_F3'), ('F4', 6, 4, 'QR_F4'), ('F5', 6, 5, 'QR_F5'),
('F6', 6, 6, 'QR_F6'), ('F7', 6, 7, 'QR_F7'), ('F8', 6, 8, 'QR_F8'), ('F9', 6, 9, 'QR_F9'), ('F10', 6, 10, 'QR_F10'),
('G1', 7, 1, 'QR_G1'), ('G2', 7, 2, 'QR_G2'), ('G3', 7, 3, 'QR_G3'), ('G4', 7, 4, 'QR_G4'), ('G5', 7, 5, 'QR_G5'),
('G6', 7, 6, 'QR_G6'), ('G7', 7, 7, 'QR_G7'), ('G8', 7, 8, 'QR_G8'), ('G9', 7, 9, 'QR_G9'), ('G10', 7, 10, 'QR_G10'),
('H1', 8, 1, 'QR_H1'), ('H2', 8, 2, 'QR_H2'), ('H3', 8, 3, 'QR_H3'), ('H4', 8, 4, 'QR_H4'), ('H5', 8, 5, 'QR_H5'),
('H6', 8, 6, 'QR_H6'), ('H7', 8, 7, 'QR_H7'), ('H8', 8, 8, 'QR_H8'), ('H9', 8, 9, 'QR_H9'), ('H10', 8, 10, 'QR_H10'),
('I1', 9, 1, 'QR_I1'), ('I2', 9, 2, 'QR_I2'), ('I3', 9, 3, 'QR_I3'), ('I4', 9, 4, 'QR_I4'), ('I5', 9, 5, 'QR_I5'),
('I6', 9, 6, 'QR_I6'), ('I7', 9, 7, 'QR_I7'), ('I8', 9, 8, 'QR_I8'), ('I9', 9, 9, 'QR_I9'), ('I10', 9, 10, 'QR_I10'),
('J1', 10, 1, 'QR_J1'), ('J2', 10, 2, 'QR_J2'), ('J3', 10, 3, 'QR_J3'), ('J4', 10, 4, 'QR_J4'), ('J5', 10, 5, 'QR_J5'),
('J6', 10, 6, 'QR_J6'), ('J7', 10, 7, 'QR_J7'), ('J8', 10, 8, 'QR_J8'), ('J9', 10, 9, 'QR_J9'), ('J10', 10, 10, 'QR_J10');