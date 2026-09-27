# E-Learning Platform (PHP + MySQL)

A role-based Learning Management System with **Admin**, **Teacher**, and **Student** modules, built with plain PHP, MySQL (mysqli), HTML, CSS, and JavaScript — no frameworks required.

## Features

**Admin**
- Secure login
- Add / block / unblock / delete teachers
- Monitor all courses, materials, and quizzes
- Hide or remove inappropriate courses
- Monitor student activity and participation
- Dashboard with platform-wide stats

**Teacher**
- Login (accounts created by Admin)
- Create and manage classes/courses
- Upload video lectures and text/PDF notes per class
- Create quizzes with multiple-choice questions and correct answers
- Edit/delete classes, materials, and quizzes
- View student quiz attempts and scores

**Student**
- Register and log in
- Browse available classes
- Watch videos / view or download notes
- Attempt quizzes (one attempt per quiz)
- View quiz results and answer review
- Activity (materials viewed) is logged automatically

## File Structure

```
elearning-platform/
├── config/
│   └── db.php                 # Database connection
├── includes/
│   ├── header.php             # Shared page header/nav
│   ├── footer.php             # Shared page footer
│   └── functions.php          # Helpers: auth guards, flash messages, sanitization
├── assets/
│   ├── css/style.css          # All styling
│   └── js/script.js           # Confirm dialogs, alert auto-hide, quiz submit check
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   ├── add_teacher.php
│   ├── manage_teachers.php
│   ├── manage_courses.php
│   ├── manage_students.php
│   └── logout.php
├── teacher/
│   ├── login.php
│   ├── dashboard.php
│   ├── add_class.php
│   ├── manage_classes.php
│   ├── upload_material.php
│   ├── manage_quiz.php
│   ├── add_quiz.php
│   ├── add_question.php
│   ├── student_performance.php
│   └── logout.php
├── student/
│   ├── register.php
│   ├── login.php
│   ├── dashboard.php
│   ├── browse_classes.php
│   ├── view_class.php
│   ├── attempt_quiz.php
│   ├── submit_quiz.php
│   ├── view_results.php
│   └── logout.php
├── uploads/
│   ├── videos/                # Uploaded video files land here
│   └── notes/                 # Uploaded notes/PDFs land here
├── index.php                  # Public landing page
├── database.sql               # Full MySQL schema + seed admin account
└── README.md
```

## Setup Instructions

1. **Install a local server stack** such as XAMPP, WAMP, or MAMP (PHP 7.4+ and MySQL/MariaDB).
2. Copy the `elearning-platform` folder into your server's web root:
   - XAMPP: `C:\xampp\htdocs\elearning-platform`
   - MAMP: `/Applications/MAMP/htdocs/elearning-platform`
3. **Create the database**:
   - Open phpMyAdmin (or the MySQL CLI).
   - Import `database.sql`. This creates the `elearning_db` database, all tables, and one default admin account.
4. **Configure the connection** in `config/db.php` if your MySQL username/password differ from the defaults (`root` / empty password).
5. **Set upload folder permissions** (Linux/Mac): `chmod -R 755 uploads/`
6. Visit `http://localhost/elearning-platform/` in your browser.

## Default Admin Login

```
Email:    admin@elearning.com
Password: admin123
```

Use this account to log in as Admin and create your first Teacher account. Teachers do not self-register — the Admin adds them, matching the workflow: **Admin → Teacher → Class Content → Student → Quiz → Result**.

## Notes on Security

- Passwords are hashed with PHP's `password_hash()` / verified with `password_verify()`.
- All SQL queries affecting user input use **prepared statements** to prevent SQL injection.
- Output is escaped with `htmlspecialchars()` to reduce XSS risk.
- Ownership checks ensure a teacher can only edit/delete their own classes, materials, and quizzes.
- For production use, also add HTTPS, CSRF tokens on forms, file-type/size validation on uploads, and rate-limiting on login attempts.

## Extending the Project

- Add pagination to long tables (teachers, students, courses).
- Add a "forgot password" flow with email reset links.
- Add file-type validation (only allow .mp4/.mov for videos, .pdf/.docx for notes).
- Add per-question point values and a passing threshold.
- Add email notifications when a teacher publishes a new class or quiz.
