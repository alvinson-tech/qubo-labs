-- ============================================
-- UPDATE STUDENTS TABLE FOR CLASS ID 1
-- This will clear existing students and add the new list
-- ============================================

USE qubo_labs;

-- Step 1: Delete existing students for Class ID 1
DELETE FROM students WHERE class_id = 1;

-- Step 2: Reset auto-increment (optional, if you want to start fresh)
-- ALTER TABLE students AUTO_INCREMENT = 1;

-- Step 3: Insert all students for Class ID 1
INSERT INTO students (usn_number, student_name, password, class_id) VALUES
('1MJ23CS001', 'A LAVANYA', 'Lav001', 1),
('1MJ23CS002', 'A S BINUSHA', 'Bin002', 1),
('1MJ23CS003', 'AAKANKSHA ANIL KUMAR', 'Aak003', 1),
('1MJ23CS004', 'AASHIF AHMED', 'Aas004', 1),
('1MJ23CS005', 'ABHISHEK A', 'Abh005', 1),
('1MJ23CS006', 'ABHISHEK K', 'Abh006', 1),
('1MJ23CS007', 'ADARYA TANEW', 'Ada007', 1),
('1MJ23CS008', 'ADITYA SURESH', 'Adi008', 1),
('1MJ23CS009', 'AAKASH SURENDRA ANKAPUR', 'Aak009', 1),
('1MJ23CS010', 'AKSHATA', 'Aks010', 1),
('1MJ23CS011', 'ALISHA', 'Ali011', 1),
('1MJ23CS012', 'ALVIN SONNY', 'Alv012', 1),
('1MJ23CS013', 'AMULYA G GOUDA', 'Amu013', 1),
('1MJ23CS014', 'ANAGHASHREE G K', 'Ana014', 1),
('1MJ23CS015', 'ANANYA SANEW', 'Ana015', 1),
('1MJ23CS016', 'ANIKETHA H N', 'Ani016', 1),
('1MJ23CS017', 'ANKITA CHARAN PAHADI', 'Ank017', 1),
('1MJ23CS018', 'ANUSHREE D S', 'Anu018', 1),
('1MJ23CS019', 'ANKUR DARSHAN KUMAR', 'Ank019', 1),
('1MJ23CS020', 'ARFA KULSUM', 'Arf020', 1),
('1MJ23CS021', 'ARJUN SHARMA', 'Arj021', 1),
('1MJ23CS022', 'ARUN K', 'Aru022', 1),
('1MJ23CS024', 'AVRAJ BHAWRHA', 'Avr024', 1),
('1MJ23CS025', 'AVSITA SHARAV', 'Avs025', 1),
('1MJ23CS026', 'BALAJI R', 'Bal026', 1),
('1MJ23CS027', 'BAPUGOUDA JALIKATTI', 'Bap027', 1),
('1MJ23CS028', 'BASAVARAJ', 'Bas028', 1),
('1MJ23CS029', 'BHARGAVI M', 'Bha029', 1),
('1MJ23CS030', 'BHARATH C', 'Bha030', 1),
('1MJ23CS031', 'BHARATH V R', 'Bha031', 1),
('1MJ23CS032', 'BHAVANA D S', 'Bha032', 1),
('1MJ23CS033', 'CHAITANYA LAREE', 'Cha033', 1),
('1MJ23CS034', 'CHANDAN J D', 'Cha034', 1),
('1MJ23CS035', 'CHANNANABASAVA', 'Cha035', 1),
('1MJ23CS036', 'CHETHANA KUMARES', 'Che036', 1),
('1MJ23CS037', 'CHETHANA SHREE P', 'Che037', 1),
('1MJ23CS038', 'CHIRANTH L', 'Chi038', 1),
('1MJ23CS039', 'CHRISTY SHEPPARD', 'Chr039', 1),
('1MJ23CS040', 'D MANOHAR', 'Man040', 1),
('1MJ23CS041', 'D NEHA', 'Neh041', 1),
('1MJ23CS042', 'DANISH KHAJURIA', 'Dan042', 1),
('1MJ23CS043', 'DARSHAN', 'Dar043', 1),
('1MJ23CS044', 'DARSHAN KUMAR DAS', 'Dar044', 1),
('1MJ23CS045', 'DEEKSHITHA A U', 'Dee045', 1),
('1MJ23CS046', 'DEEPTI H M', 'Dee046', 1),
('1MJ23CS047', 'DEERAJ ASHOK SHIRAHATTI', 'Dee047', 1),
('1MJ23CS048', 'DHANUSH D', 'Dha048', 1),
('1MJ23CS049', 'DHANUSH MAINA', 'Dha049', 1),
('1MJ23CS050', 'DHARSHAN V', 'Dha050', 1),
('1MJ23CS051', 'D SUSHEEL DIWAN', 'Sus051', 1),
('1MJ23CS052', 'DISHA V O', 'Dis052', 1),
('1MJ23CS053', 'D MANOHAR', 'Man053', 1),
('1MJ23CS054', 'G M VISHWANATH', 'Vis054', 1),
('1MJ23CS208', 'V PRANJAL', 'Pra208', 1),
('1MJ23CS900', 'MYTHRI SARAVANA', 'Myt900', 1),
('1MJ24CS401', 'ABHISHEK GOUDA', 'Abh401', 1),
('1MJ24CS402', 'ABHISHEK POLICE PATIL', 'Abh402', 1),
('1MJ24CS404', 'ARPITHA', 'Arp404', 1),
('1MJ24CS405', 'ARYKA K S', 'Ary405', 1),
('1MJ24CS406', 'ASHISH GUPTA', 'Ash406', 1),
('1MJ24CS407', 'AYESHA SIDDIKA', 'Aye407', 1),
('1MJ24CS408', 'B.MS. BHATI', 'Bha408', 1),
('1MJ24CS409', 'BHAVANA', 'Bha409', 1),
('1MJ24CS410', 'CHANDAN', 'Cha410', 1),
('1MJ24CS411', 'D RUSHBH DIWAN', 'Rus411', 1),
('1MJ24CS412', 'DARSHAN B', 'Dar412', 1),
('1MJ24CS413', 'DARSHAN C', 'Dar413', 1);

-- Step 4: Verification - Count students in Class ID 1
SELECT COUNT(*) as total_students, class_id 
FROM students 
WHERE class_id = 1
GROUP BY class_id;

-- Step 5: Display all students for Class ID 1 to verify
SELECT student_id, usn_number, student_name, password, class_id 
FROM students 
WHERE class_id = 1 
ORDER BY usn_number;

SELECT 'Successfully updated students for Class ID 1!' as Status;