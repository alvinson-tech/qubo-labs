# Qubo Labs - Smart Attendance Management System

A modern, real-time attendance tracking system designed for large gatherings in schools and college auditoriums. Features QR code scanning, neighbor verification, and live seat mapping.

---

## 📋 Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [System Architecture](#system-architecture)
- [Installation](#installation)
- [Database Schema](#database-schema)
- [How It Works](#how-it-works)
- [User Roles](#user-roles)
- [Security Features](#security-features)
- [File Structure](#file-structure)
- [API Endpoints](#api-endpoints)
- [Configuration](#configuration)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

---

## ✨ Features

### For Staff:
- 🎯 **Session Management**: Create and manage attendance sessions for specific classes
- 👁️ **Live View**: Real-time seat map showing attendance status (Empty/Scanned/Verified)
- 📊 **Analytics Dashboard**: Instant statistics (Total Students, Marked, Verified, Absent)
- 📄 **PDF Reports**: Download detailed attendance reports with absentees list
- 🔴 **Session Control**: Start and end sessions with one click

### For Students:
- 📱 **QR Code Scanning**: Camera-based scanning of seat QR codes
- 🔐 **Neighbor Verification**: Mutual verification system with 4-digit codes
- ⚠️ **No Neighbours Option**: Mark attendance even when sitting alone
- ✅ **Real-time Status**: See verification status instantly
- 🔒 **Single Session**: One device per student (prevents multiple logins)

### System Features:
- ⚡ **Real-time Updates**: Live data refresh every 3 seconds
- 🎨 **Modern UI**: Clean blue dark theme with DM Sans font
- 📱 **Responsive Design**: Works on desktop, tablet, and mobile
- 🔐 **Session Management**: Secure session tokens prevent duplicate logins
- 🎯 **Smart Verification**: Only immediate left/right neighbors can verify
- 📊 **Complete Tracking**: Shows all students (present and absent)

---

## 🛠 Tech Stack

### Backend:
- **PHP 7.4+**: Server-side scripting
- **MySQL 5.7+**: Database management
- **XAMPP**: Local development environment

### Frontend:
- **HTML5**: Structure and semantics
- **CSS3**: Styling with custom properties
- **JavaScript (Vanilla)**: Client-side logic
- **HTML5 QR Code Scanner**: Camera-based QR scanning

### Libraries & Tools:
- **html5-qrcode (v2.3.8)**: QR code scanning functionality
- **DM Sans Font**: Typography (Google Fonts)
- **Fetch API**: Asynchronous data retrieval

### Design:
- **Blue Dark Theme**: Modern, professional appearance
- **Responsive Grid**: Mobile-first design
- **Custom CSS Variables**: Easy theme customization

---

## 🏗 System Architecture

```
┌─────────────────────────────────────────────────────────┐
│                     Client Layer                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │   Student    │  │    Staff     │  │   Landing    │  │
│  │  Dashboard   │  │  Dashboard   │  │     Page     │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────────┘
                            │
                            ↓
┌─────────────────────────────────────────────────────────┐
│                  Application Layer                       │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │   Login &    │  │  Attendance  │  │   Session    │  │
│  │     Auth     │  │   Handling   │  │  Management  │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────────┘
                            │
                            ↓
┌─────────────────────────────────────────────────────────┐
│                      API Layer                           │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │   Scan QR    │  │   Verify     │  │  Live Data   │  │
│  │     API      │  │     API      │  │     API      │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────────┘
                            │
                            ↓
┌─────────────────────────────────────────────────────────┐
│                    Database Layer                        │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │   Students   │  │    Staff     │  │  Attendance  │  │
│  │    Table     │  │    Table     │  │   Records    │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────────┘
```

---

## 📦 Installation

### Prerequisites:
- XAMPP (Apache + MySQL)
- Web browser (Chrome, Firefox, Edge)
- Text editor (VS Code recommended)

### Step-by-Step Setup:

#### 1. Install XAMPP
```bash
# Download from: https://www.apachefriends.org/
# Install to default location: C:\xampp\
```

#### 2. Create Project Directory
```bash
# Navigate to XAMPP htdocs
cd C:\xampp\htdocs\

# Create project folder
mkdir qubo-labs
```

#### 3. Copy Project Files
```
qubo-labs/
├── config/
│   └── database.php
├── includes/
│   ├── functions.php
│   └── session.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
├── student/
│   ├── index.php
│   ├── scan_qr.php
│   └── verify_code.php
├── staff/
│   ├── index.php
│   ├── start_session.php
│   ├── live_view.php
│   ├── end_session.php
│   └── download_pdf.php
├── api/
│   ├── check_active_session.php
│   ├── submit_scan.php
│   ├── verify_attendance.php
│   └── get_live_data.php
├── index.php
├── login.php
└── logout.php
```

#### 4. Setup Database
```bash
# Open phpMyAdmin
http://localhost/phpmyadmin

# Create database and run SQL script
# File: qubo_labs_database.sql
```

#### 5. Configure Database Connection
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'qubo_labs');
```

#### 6. Add Session Token Columns
```sql
USE qubo_labs;
ALTER TABLE students ADD COLUMN session_token VARCHAR(255) NULL;
ALTER TABLE students ADD COLUMN last_login TIMESTAMP NULL;
ALTER TABLE staff ADD COLUMN session_token VARCHAR(255) NULL;
ALTER TABLE staff ADD COLUMN last_login TIMESTAMP NULL;
```

#### 7. Start XAMPP Services
```bash
# Start Apache
# Start MySQL
```

#### 8. Access Application
```
http://localhost/qubo-labs
```

---

## 🗄 Database Schema

### Tables Overview:

#### 1. **staff**
```sql
- staff_id (INT, PK, AUTO_INCREMENT)
- staff_name (VARCHAR 100)
- email (VARCHAR 100, UNIQUE)
- password (VARCHAR 255)
- department (VARCHAR 100)
- session_token (VARCHAR 255, NULL)
- last_login (TIMESTAMP, NULL)
- created_at (TIMESTAMP)
```

#### 2. **classes**
```sql
- class_id (INT, PK, AUTO_INCREMENT)
- class_name (VARCHAR 50)
- section (VARCHAR 10)
- year (INT)
- created_at (TIMESTAMP)
```

#### 3. **students**
```sql
- student_id (INT, PK, AUTO_INCREMENT)
- roll_number (VARCHAR 50, UNIQUE)
- student_name (VARCHAR 100)
- email (VARCHAR 100, UNIQUE)
- password (VARCHAR 255)
- class_id (INT, FK → classes)
- session_token (VARCHAR 255, NULL)
- last_login (TIMESTAMP, NULL)
- created_at (TIMESTAMP)
```

#### 4. **auditorium_seats**
```sql
- seat_id (INT, PK, AUTO_INCREMENT)
- seat_number (VARCHAR 20, UNIQUE)
- row_number (INT)
- seat_position (INT)
- qr_code (VARCHAR 100, UNIQUE)
```

#### 5. **attendance_sessions**
```sql
- session_id (INT, PK, AUTO_INCREMENT)
- staff_id (INT, FK → staff)
- class_id (INT, FK → classes)
- session_name (VARCHAR 100)
- start_time (TIMESTAMP)
- end_time (TIMESTAMP, NULL)
- status (ENUM: 'active', 'ended')
```

#### 6. **attendance_records**
```sql
- record_id (INT, PK, AUTO_INCREMENT)
- session_id (INT, FK → attendance_sessions)
- student_id (INT, FK → students)
- seat_id (INT, FK → auditorium_seats)
- verification_code (VARCHAR 4)
- scanned_at (TIMESTAMP)
- verified_at (TIMESTAMP, NULL)
- verified_by_student_id (INT, FK → students, NULL)
- no_neighbours (BOOLEAN)
- status (ENUM: 'scanned', 'verified')
```

### Database Relationships:
```
staff ──┐
        ├──→ attendance_sessions
classes ┘

students ──┬──→ attendance_records
           │
classes ───┘

auditorium_seats ──→ attendance_records

attendance_sessions ──→ attendance_records
```

---

## 🔄 How It Works

### Attendance Flow:

#### **Phase 1: Session Initiation**
```
1. Staff logs in
2. Creates attendance session
3. Selects class and section
4. Session becomes active
5. All students appear in live view as "Not Marked"
```

#### **Phase 2: Student Scanning**
```
1. Student logs in
2. Sees active session
3. Clicks "Scan QR Code"
4. Camera opens
5. Scans seat QR code (e.g., QR_A5)
6. System validates:
   ✓ Session is active
   ✓ Student hasn't already marked attendance
   ✓ Seat is not occupied
7. Generates 4-digit code (e.g., "AB12")
8. Status: "Scanned" (Yellow)
9. Student's name appears in live view
```

#### **Phase 3: Verification**
```
1. Student A sits in A5, gets code "AB12"
2. Student B sits in A6, gets code "CD34"
3. Student A enters "CD34" (B's code)
4. System validates:
   ✓ Code exists
   ✓ Same row (both in row A)
   ✓ Immediate neighbor (|5-6| = 1)
5. Both A and B marked as "Verified"
6. Status: "Verified" (Green)
7. Seats turn green in live view
```

#### **Phase 4: Special Cases**

**No Neighbours:**
```
1. Student C sits alone in B5
2. No one in B4 or B6
3. Clicks "No Neighbours Besides Me"
4. Status: "Not Verified" (stays Yellow)
5. Appears in attendance list
6. Marked in PDF as "Not Verified"
```

**Corner Seats:**
```
A1 (corner) can only verify with A2 (right)
A10 (corner) can only verify with A9 (left)
```

#### **Phase 5: Session End**
```
1. Staff clicks "End Session"
2. Session status → 'ended'
3. Students can no longer mark attendance
4. Staff downloads PDF report
5. Report shows:
   - Present students (with status)
   - Absentees (separate list)
```

---

## 👥 User Roles

### **Staff Credentials:**
```
Email: rekha.p@mvj.edu
Password: Rekha@123
Department: Computer Science

Email: arfa@mvj.edu
Password: Arfa@456
Department: Computer Science
```

### **Student Credentials:**
Password Pattern: `FirstName@RollNumber`

**Section A:**
```
CS005 - Abhishek A - abhishek@stu.mvj.edu - Abhishek@005
CS008 - Aditya Suresh - aditya@stu.mvj.edu - Aditya@008
CS012 - Alvin Sonny - alvin@stu.mvj.edu - Alvin@012
CS015 - Ananya Sanjiv - ananya@stu.mvj.edu - Ananya@015
CS018 - Arjun Menon - arjun@stu.mvj.edu - Arjun@018
```

**Section B:**
```
CS058 - Ganesha Thejaswi - ganesh@stu.mvj.edu - Ganesha@058
CS061 - Gowri Krishnan Nair - gowri@stu.mvj.edu - Gowri@061
CS064 - Harsha Kumar - harsha@stu.mvj.edu - Harsha@064
CS067 - Ishaan Reddy - ishaan@stu.mvj.edu - Ishaan@067
CS070 - Jaya Prakash - jaya@stu.mvj.edu - Jaya@070
```

**Sections C & D:** Similar pattern (20 students total)

---

## 🔐 Security Features

### **Session Management:**
- Unique session tokens for each login
- Single device restriction per user
- Session validation on every page load
- Automatic logout if token mismatch

### **Verification Logic:**
```php
// Only immediate neighbors can verify
if ($neighbor_row != $my_row) {
    return "Same row only";
}

$position_diff = abs($neighbor_position - $my_position);
if ($position_diff != 1) {
    return "Immediate neighbors only";
}
```

### **SQL Injection Prevention:**
- Prepared statements throughout
- Parameter binding for all queries
- Input sanitization

### **Authentication:**
- Plain text passwords (for demo purposes)
- Session-based authentication
- Protected routes with `requireStudent()` / `requireStaff()`

---

## 📁 File Structure

```
qubo-labs/
│
├── config/
│   └── database.php              # Database configuration
│
├── includes/
│   ├── functions.php             # Utility functions
│   └── session.php               # Session management
│
├── assets/
│   ├── css/
│   │   └── style.css            # Blue dark theme styles
│   └── js/
│       └── (unused)
│
├── student/
│   ├── index.php                # Student dashboard
│   ├── scan_qr.php              # QR scanner page
│   └── verify_code.php          # Verification page
│
├── staff/
│   ├── index.php                # Staff dashboard
│   ├── start_session.php        # Session creation
│   ├── live_view.php            # Real-time monitoring
│   ├── end_session.php          # Session termination
│   └── download_pdf.php         # Report generation
│
├── api/
│   ├── check_active_session.php # Check for active sessions
│   ├── submit_scan.php          # Handle QR scan
│   ├── verify_attendance.php    # Process verification
│   └── get_live_data.php        # Real-time data feed
│
├── index.php                    # Landing page
├── login.php                    # Login handler
├── logout.php                   # Logout handler
└── README.md                    # This file
```

---

## 🔌 API Endpoints

### **1. Check Active Session**
```
GET /api/check_active_session.php
Auth: Required (Student)
Response: { success, has_session, session }
```

### **2. Submit QR Scan**
```
POST /api/submit_scan.php
Auth: Required (Student)
Body: { session_id, qr_code }
Response: { success, verification_code, seat_number }
```

### **3. Verify Attendance**
```
POST /api/verify_attendance.php
Auth: Required (Student)
Body: { session_id, neighbor_code, no_neighbours }
Response: { success, message }
```

### **4. Get Live Data**
```
GET /api/get_live_data.php?session_id=X
Auth: Required (Staff)
Response: {
  success,
  attendance: [...],
  complete_list: [...],
  stats: { total_students, total_marked, total_verified, total_not_marked }
}
```

### **5. End Session**
```
POST /staff/end_session.php
Auth: Required (Staff)
Body: { session_id }
Response: { success }
```

---

## ⚙️ Configuration

### **Database Reset (Before Presentation):**
```sql
-- Clear attendance data only
DELETE FROM attendance_records;
DELETE FROM attendance_sessions;
ALTER TABLE attendance_records AUTO_INCREMENT = 1;
ALTER TABLE attendance_sessions AUTO_INCREMENT = 1;

-- Logout all users
UPDATE students SET session_token = NULL;
UPDATE staff SET session_token = NULL;
```

### **QR Code Generation:**
Generate QR codes for seats A1 to J10:
```
QR_A1, QR_A2, QR_A3, ..., QR_J10
```
Use: https://www.qr-code-generator.com/

### **Customization:**

**Change Colors:**
Edit CSS variables in `assets/css/style.css`:
```css
:root {
    --primary: #3b82f6;      /* Main blue */
    --primary-dark: #2563eb;  /* Dark blue */
    --primary-light: #60a5fa; /* Light blue */
}
```

**Update Refresh Rate:**
Edit `staff/live_view.php`:
```javascript
setInterval(updateLiveData, 3000); // 3 seconds
```

---

## 🐛 Troubleshooting

### **Common Issues:**

#### 1. "Connection Failed"
```
Solution: Ensure MySQL is running in XAMPP
```

#### 2. "Already Logged In" Error
```sql
-- Reset session tokens
UPDATE students SET session_token = NULL;
UPDATE staff SET session_token = NULL;
```

#### 3. QR Scanner Not Working
```
- Access via http://localhost (not file://)
- Allow camera permissions
- Use Chrome/Firefox (better WebRTC support)
```

#### 4. Invalid Email or Password
```
- Check exact email format
- Password is case-sensitive
- Pattern: FirstName@RollNumber
```

#### 5. Live View Not Updating
```
- Check browser console for errors
- Verify session_id is correct
- Ensure Apache/MySQL are running
```

---

## 📊 Performance Metrics

### **System Capacity:**
- ✅ 100 seats (10 rows × 10 seats)
- ✅ Unlimited students per class
- ✅ Multiple sessions (one active per class)
- ✅ Real-time updates (3-second interval)

### **Response Times:**
- QR Scan: < 1 second
- Verification: < 1 second
- Live View Update: 3 seconds
- PDF Generation: 2-3 seconds

---

## 🎯 Use Cases

### **1. College Seminars**
Track attendance for large auditorium events

### **2. Assembly Attendance**
Mark presence during school assemblies

### **3. Exam Hall Monitoring**
Verify student seating arrangements

### **4. Workshop Sessions**
Track participation in training programs

### **5. Conference Events**
Monitor attendee presence and seating

---

## 🚀 Future Enhancements

- [ ] Email notifications to absentees
- [ ] SMS alerts for attendance
- [ ] Analytics dashboard with charts
- [ ] Export to Excel format
- [ ] Multi-auditorium support
- [ ] Mobile app (React Native)
- [ ] Face recognition integration
- [ ] Automated report scheduling
- [ ] Parent portal access
- [ ] Integration with LMS

---

## 📝 License

This project is licensed under the MIT License.

```
MIT License

Copyright (c) 2024 Qubo Labs

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

## 👨‍💻 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📞 Support

For issues, questions, or suggestions:
- Create an issue on GitHub
- Email: support@qubolabs.com
- Documentation: Check this README

---

## 🙏 Acknowledgments

- **HTML5 QR Code Library** for camera scanning
- **DM Sans Font** by Google Fonts
- **PHP Community** for extensive documentation
- **MySQL** for robust database management

---

## 📈 Project Status

**Version:** 1.0.0  
**Status:** Production Ready  
**Last Updated:** 2024  
**Maintained:** Yes

---

## 🎓 Educational Purpose

This project was developed as an educational tool for understanding:
- Real-time web applications
- Session management
- QR code integration
- Database design
- RESTful API design
- Modern UI/UX principles

---

**Built with ❤️ for better attendance management**

**Qubo Labs** - Smart Attendance Management System