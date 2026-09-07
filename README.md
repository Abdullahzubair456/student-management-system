# Student Management System

This is a Student Management System that I developed using PHP, MySQL, Bootstrap, HTML, CSS and JavaScript.

The main purpose of this project is to manage students and courses from one system. It has separate panels for Admin and Students.

## Features

### Admin Panel

* Admin login
* Admin dashboard
* Add new students
* View student details
* Edit student information
* Delete students
* Search students
* Add courses
* View course details
* Edit courses
* Delete courses
* Search courses
* Admin profile
* Change password
* Forgot password and password reset

### Student Panel

* Student login and registration
* Student dashboard
* View and edit profile
* View enrolled course
* Academic information section
* Student account authentication

## Technologies Used

* PHP
* MySQL
* HTML5
* CSS3
* Bootstrap 5
* JavaScript
* XAMPP
* Git & GitHub

## Project Structure

```text
student-management-system/
│
├── admin/
│   ├── dashboard.php
│   ├── students.php
│   ├── add-student.php
│   ├── edit-student.php
│   ├── view-student.php
│   ├── delete-student.php
│   ├── courses.php
│   ├── add-course.php
│   ├── edit-course.php
│   ├── view-course.php
│   ├── delete-course.php
│   ├── profile.php
│   └── change-password.php
│
├── auth/
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   ├── forgot-password.php
│   └── reset-password.php
│
├── assets/
│   ├── css/
│   └── js/
│
├── student/
│   ├── dashboard.php
│   ├── profile.php
│   ├── course.php
│   └── academic.php
│
├── config/
│   └── database.php
│
├── index.php
└── .gitignore
```

## How to Run

1. Install XAMPP on your computer.
2. Start Apache and MySQL from XAMPP.
3. Copy the project folder into the `htdocs` folder.
4. Create a MySQL database named:

```text
student_management
```

5. Import the required database tables.
6. Configure the database connection in `config/database.php`.
7. Open the project in your browser:

```text
http://localhost/student-management-system/
```

## Login

The system has two types of users:

* Admin
* Student

The user role is checked during login so that users can access their respective panels.

## Security

Passwords are stored using PHP's password hashing functions instead of saving plain-text passwords.

The database configuration file is also excluded from the public GitHub repository using `.gitignore`.

## Current Status

The main student and course management features are working.

I am still working on adding more academic-related features and improving the overall system.

## What I Practiced in This Project

While working on this project, I practiced:

* PHP forms
* MySQL database connection
* CRUD operations
* User authentication
* Sessions
* Role-based access
* Password hashing
* Password reset
* SQL queries
* PHP and MySQL integration
* Bootstrap responsive design
* Git and GitHub

## Author

**Abdullah Zubair**

Web Development Student
