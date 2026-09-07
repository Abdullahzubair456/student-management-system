```php
<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../student/dashboard.php");
    exit;
}

include '../config/database.php';


// ===============================
// Dashboard Statistics
// ===============================

// Total Students
$student_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM students"
);

$student_data = mysqli_fetch_assoc($student_query);
$total_students = $student_data['total'];


// Total Courses
$course_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM courses"
);

$course_data = mysqli_fetch_assoc($course_query);
$total_courses = $course_data['total'];


// Active Students
$active_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM students
     WHERE status = 'Active'"
);

$active_data = mysqli_fetch_assoc($active_query);
$active_students = $active_data['total'];


// Inactive Students
$inactive_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM students
     WHERE status = 'Inactive'"
);

$inactive_data = mysqli_fetch_assoc($inactive_query);
$inactive_students = $inactive_data['total'];


// ===============================
// Recent Students
// ===============================

$recent_sql = "SELECT
                    students.id,
                    students.student_id,
                    students.first_name,
                    students.last_name,
                    students.email,
                    courses.course_name
               FROM students
               LEFT JOIN courses
               ON students.course_id = courses.id
               ORDER BY students.id DESC
               LIMIT 5";

$recent_result = mysqli_query($conn, $recent_sql);

if (!$recent_result) {
    die("Recent Students Query Failed: " . mysqli_error($conn));
}


// ===============================
// Recent Courses
// ===============================

$recent_courses_sql = "SELECT
                            id,
                            course_name,
                            course_code
                       FROM courses
                       ORDER BY id DESC
                       LIMIT 5";

$recent_courses_result = mysqli_query(
    $conn,
    $recent_courses_sql
);

if (!$recent_courses_result) {
    die("Recent Courses Query Failed: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Dashboard - Student Management System</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background-color: #0b0b0b;
            color: #ffffff;
            font-family: Arial, sans-serif;
        }


        /* =========================
           SIDEBAR
        ========================== */

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;

            background: linear-gradient(
                180deg,
                #3f0d16 0%,
                #25070d 100%
            );

            border-right: 1px solid #7a2230;
            z-index: 1000;

            padding: 25px 15px;
        }


        .sidebar-brand {
            text-align: center;
            padding-bottom: 25px;
            border-bottom: 1px solid #7a2230;
            margin-bottom: 25px;
        }


        .sidebar-brand .logo {
            font-size: 32px;
            margin-bottom: 8px;
        }


        .sidebar-brand h4 {
            color: #d4af7f;
            font-weight: 700;
            margin: 0;
        }


        .sidebar-brand small {
            color: #e0cfd2;
        }


        /* =========================
           SIDEBAR LINKS
        ========================== */

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }


        .sidebar-menu li {
            margin-bottom: 8px;
        }


        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;

            padding: 13px 15px;

            color: #ffffff;
            text-decoration: none;

            border-radius: 9px;

            transition: all 0.25s ease;
        }


        .sidebar-menu a:hover {
            background-color: #000000;
            color: #d4af7f;
            transform: translateX(3px);
        }


        .sidebar-menu a.active {
            background-color: #7a2230;
            color: #d4af7f;

            border-left: 4px solid #d4af7f;
        }


        .menu-icon {
            width: 25px;
            text-align: center;
            font-size: 18px;
        }


        /* =========================
           SIDEBAR BOTTOM
        ========================== */

        .sidebar-bottom {
            position: absolute;
            bottom: 25px;
            left: 15px;
            right: 15px;
        }


        .sidebar-bottom a {
            display: flex;
            align-items: center;
            gap: 12px;

            padding: 13px 15px;

            color: #ffffff;
            text-decoration: none;

            border-radius: 9px;

            transition: 0.25s ease;
        }


        .sidebar-bottom a:hover {
            background-color: #000000;
            color: #d4af7f;
        }


        /* =========================
           MAIN AREA
        ========================== */

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }


        /* =========================
           TOPBAR
        ========================== */

        .topbar {
            height: 70px;

            background-color: #050505;

            border-bottom: 1px solid #3f0d16;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 30px;
        }


        .topbar-title {
            color: #d4af7f;
            font-weight: 700;
            font-size: 20px;
        }


        .admin-name {
            color: #ffffff;
        }


        .admin-name strong {
            color: #d4af7f;
        }


        /* =========================
           PAGE CONTENT
        ========================== */

        .page-content {
            padding: 30px;
        }


        /* =========================
           WELCOME BOX
        ========================== */

        .welcome-box {
            background-color: #5a1622;
            border: 1px solid #7a2230;
            border-radius: 14px;

            padding: 25px;
            margin-bottom: 25px;

            transition: 0.25s ease;
        }


        .welcome-box:hover {
            background-color: #000000;
            border-color: #d4af7f;
            transform: translateY(-3px);
        }


        .welcome-box h2 {
            color: #d4af7f;
            font-weight: 700;
        }


        .welcome-box p {
            color: #e0cfd2;
            margin-bottom: 0;
        }


        /* =========================
           STAT CARDS
        ========================== */

        .dashboard-card {
            background-color: #5a1622;
            border: 1px solid #7a2230;
            border-radius: 14px;

            color: #ffffff;

            transition: all 0.25s ease;
        }


        .dashboard-card:hover {
            background-color: #000000;
            border-color: #d4af7f;

            transform: translateY(-5px);

            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.7);
        }


        .stat-title {
            color: #e0cfd2;
            font-size: 14px;
        }


        .stat-number {
            color: #d4af7f;
            font-size: 34px;
            font-weight: 700;
        }


        /* =========================
           MANAGEMENT CARDS
        ========================== */

        .management-card {
            background-color: #5a1622;
            border: 1px solid #7a2230;
            border-radius: 14px;

            color: #ffffff;

            transition: all 0.25s ease;
        }


        .management-card:hover {
            background-color: #000000;
            border-color: #d4af7f;

            transform: translateY(-5px);
        }


        .management-card h4 {
            color: #d4af7f;
        }


        .management-card p {
            color: #e0cfd2;
        }


        /* =========================
           SECTION CARDS
        ========================== */

        .section-card {
            background-color: #5a1622;
            border: 1px solid #7a2230;
            border-radius: 14px;

            color: #ffffff;

            overflow: hidden;
        }


        .card-header {
            background-color: #3f0d16 !important;
            color: #d4af7f !important;

            border-bottom: 1px solid #7a2230;
            padding: 15px 20px;
        }


        .card-body {
            background-color: transparent;
        }


        /* =========================
           TABLE
        ========================== */

        .table {
            color: #ffffff;
            margin-bottom: 0;
        }


        .table-light {
            --bs-table-bg: #3f0d16;
            --bs-table-color: #d4af7f;
        }


        .table td,
        .table th {
            border-color: #7a2230;
        }


        .table-hover > tbody > tr:hover {
            --bs-table-hover-bg: #000000;
            --bs-table-hover-color: #ffffff;
        }


        .table th {
            white-space: nowrap;
        }


        /* =========================
           BADGES
        ========================== */

        .badge {
            background-color: #25070d !important;
            color: #d4af7f !important;

            border: 1px solid #7a2230;
        }


        /* =========================
           BUTTONS
        ========================== */

        .btn-primary,
        .btn-success,
        .btn-info,
        .btn-secondary {
            background-color: #7a2230;
            border-color: #8b2635;
            color: #ffffff;
        }


        .btn-primary:hover,
        .btn-success:hover,
        .btn-info:hover,
        .btn-secondary:hover {
            background-color: #000000;
            border-color: #d4af7f;
            color: #d4af7f;
        }


        .btn-danger {
            background-color: #8b2635;
            border-color: #8b2635;
        }


        .btn-danger:hover {
            background-color: #000000;
            border-color: #d4af7f;
            color: #d4af7f;
        }


        .btn-outline-light:hover {
            background-color: #7a2230;
            border-color: #7a2230;
        }


        /* =========================
           MOBILE BUTTON
        ========================== */

        .mobile-menu-btn {
            display: none;

            background-color: #7a2230;
            border: 1px solid #d4af7f;
            color: #ffffff;

            border-radius: 7px;
            padding: 6px 11px;

            font-size: 20px;
        }


        /* =========================
           RESPONSIVE
        ========================== */

        @media (max-width: 991px) {

            .sidebar {
                left: -250px;
                transition: 0.3s ease;
            }


            .sidebar.show {
                left: 0;
            }


            .main-content {
                margin-left: 0;
            }


            .mobile-menu-btn {
                display: inline-block;
            }


            .topbar {
                padding: 0 20px;
            }


            .page-content {
                padding: 20px;
            }

        }


        @media (max-width: 576px) {

            .topbar-title {
                font-size: 16px;
            }


            .admin-name {
                display: none;
            }


            .page-content {
                padding: 15px;
            }


            .welcome-box {
                padding: 20px;
            }

        }

    </style>

</head>


<body>


<!-- ===============================
     SIDEBAR
================================ -->

<aside class="sidebar" id="sidebar">


    <div class="sidebar-brand">

        <div class="logo">
            🎓
        </div>

        <h4>
            Student Management
        </h4>

        <small>
            Admin Panel
        </small>

    </div>


    <ul class="sidebar-menu">


        <!-- Dashboard -->

        <li>

            <a
                href="dashboard.php"
                class="active"
            >

                <span class="menu-icon">
                    🏠
                </span>

                <span>
                    Dashboard
                </span>

            </a>

        </li>


        <!-- Students -->

        <li>

            <a href="students.php">

                <span class="menu-icon">
                    👨‍🎓
                </span>

                <span>
                    Students
                </span>

            </a>

        </li>


        <!-- Courses -->

        <li>

            <a href="courses.php">

                <span class="menu-icon">
                    📚
                </span>

                <span>
                    Courses
                </span>

            </a>

        </li>


        <!-- Profile -->

        <li>

            <a href="profile.php">

                <span class="menu-icon">
                    👤
                </span>

                <span>
                    My Profile
                </span>

            </a>

        </li>


        <!-- Change Password -->

        <li>

            <a href="change-password.php">

                <span class="menu-icon">
                    🔐
                </span>

                <span>
                    Change Password
                </span>

            </a>

        </li>


    </ul>


    <!-- Logout -->

    <div class="sidebar-bottom">

        <a href="../auth/logout.php">

            <span class="menu-icon">
                🚪
            </span>

            <span>
                Logout
            </span>

        </a>

    </div>


</aside>


<!-- ===============================
     MAIN CONTENT
================================ -->

<div class="main-content">


    <!-- ===============================
         TOPBAR
    ================================ -->

    <div class="topbar">


        <div class="d-flex align-items-center gap-3">

            <button
                class="mobile-menu-btn"
                onclick="toggleSidebar()"
            >
                ☰
            </button>

            <div class="topbar-title">
                Admin Dashboard
            </div>

        </div>


        <div class="admin-name">

            Welcome,
            <strong>
                <?php echo htmlspecialchars($_SESSION['name']); ?>
            </strong>

        </div>

    </div>


    <!-- ===============================
         PAGE CONTENT
    ================================ -->

    <div class="page-content">


        <!-- Welcome -->

        <div class="welcome-box">

            <h2 class="mb-1">
                Welcome back, Admin 👋
            </h2>

            <p>
                Manage your students, courses and
                system information from your dashboard.
            </p>

        </div>


        <!-- ===============================
             STATISTICS
        ================================ -->

        <div class="row g-4 mb-4">


            <!-- Total Students -->

            <div class="col-md-6 col-xl-3">

                <div class="card dashboard-card h-100">

                    <div class="card-body">

                        <div class="stat-title">
                            👨‍🎓 Total Students
                        </div>

                        <div class="stat-number">
                            <?php echo $total_students; ?>
                        </div>

                        <a
                            href="students.php"
                            class="btn btn-primary btn-sm mt-2"
                        >
                            Manage Students
                        </a>

                    </div>

                </div>

            </div>


            <!-- Total Courses -->

            <div class="col-md-6 col-xl-3">

                <div class="card dashboard-card h-100">

                    <div class="card-body">

                        <div class="stat-title">
                            📚 Total Courses
                        </div>

                        <div class="stat-number">
                            <?php echo $total_courses; ?>
                        </div>

                        <a
                            href="courses.php"
                            class="btn btn-success btn-sm mt-2"
                        >
                            Manage Courses
                        </a>

                    </div>

                </div>

            </div>


            <!-- Active Students -->

            <div class="col-md-6 col-xl-3">

                <div class="card dashboard-card h-100">

                    <div class="card-body">

                        <div class="stat-title">
                            ✅ Active Students
                        </div>

                        <div class="stat-number">
                            <?php echo $active_students; ?>
                        </div>

                        <a
                            href="students.php"
                            class="btn btn-info btn-sm mt-2"
                        >
                            View Students
                        </a>

                    </div>

                </div>

            </div>


            <!-- Inactive Students -->

            <div class="col-md-6 col-xl-3">

                <div class="card dashboard-card h-100">

                    <div class="card-body">

                        <div class="stat-title">
                            ⏸️ Inactive Students
                        </div>

                        <div class="stat-number">
                            <?php echo $inactive_students; ?>
                        </div>

                        <a
                            href="students.php"
                            class="btn btn-secondary btn-sm mt-2"
                        >
                            View Students
                        </a>

                    </div>

                </div>

            </div>


        </div>


        <!-- ===============================
             MANAGEMENT
        ================================ -->

        <div class="row g-4 mb-4">


            <!-- Student Management -->

            <div class="col-md-6">

                <div class="card management-card h-100">

                    <div class="card-body p-4">

                        <h4 class="fw-bold">
                            👨‍🎓 Student Management
                        </h4>

                        <p>
                            Add, view, edit, search and delete
                            student records.
                        </p>

                        <a
                            href="students.php"
                            class="btn btn-primary"
                        >
                            Manage Students →
                        </a>

                    </div>

                </div>

            </div>


            <!-- Course Management -->

            <div class="col-md-6">

                <div class="card management-card h-100">

                    <div class="card-body p-4">

                        <h4 class="fw-bold">
                            📚 Course Management
                        </h4>

                        <p>
                            Add, view, edit, search and delete
                            available courses.
                        </p>

                        <a
                            href="courses.php"
                            class="btn btn-success"
                        >
                            Manage Courses →
                        </a>

                    </div>

                </div>

            </div>


        </div>


        <!-- ===============================
             RECENT COURSES
        ================================ -->

        <div class="card section-card mb-4">


            <div class="card-header">

                <h5 class="mb-0">
                    📚 Recent Courses
                </h5>

            </div>


            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead class="table-light">

                            <tr>

                                <th>#</th>
                                <th>Course Name</th>
                                <th>Course Code</th>
                                <th>Action</th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php if (mysqli_num_rows($recent_courses_result) > 0): ?>

                            <?php while ($course = mysqli_fetch_assoc($recent_courses_result)): ?>

                                <tr>

                                    <td>
                                        <?php echo $course['id']; ?>
                                    </td>

                                    <td>

                                        <strong>
                                            <?php
                                            echo htmlspecialchars(
                                                $course['course_name']
                                            );
                                            ?>
                                        </strong>

                                    </td>

                                    <td>

                                        <span class="badge">

                                            <?php
                                            echo htmlspecialchars(
                                                $course['course_code']
                                            );
                                            ?>

                                        </span>

                                    </td>

                                    <td>

                                        <a
                                            href="view-course.php?id=<?php echo $course['id']; ?>"
                                            class="btn btn-info btn-sm"
                                        >
                                            View
                                        </a>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="4"
                                    class="text-center py-4"
                                >

                                    <h6>
                                        No Courses Found
                                    </h6>

                                    <p class="text-muted mb-0">
                                        No courses have been added yet.
                                    </p>

                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>


                <div class="text-end mt-3">

                    <a
                        href="courses.php"
                        class="btn btn-success"
                    >
                        View All Courses →
                    </a>

                </div>

            </div>

        </div>


        <!-- ===============================
             RECENT STUDENTS
        ================================ -->

        <div class="card section-card mb-4">


            <div class="card-header">

                <h5 class="mb-0">
                    👨‍🎓 Recent Students
                </h5>

            </div>


            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead class="table-light">

                            <tr>

                                <th>#</th>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Action</th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php if (mysqli_num_rows($recent_result) > 0): ?>

                            <?php while ($student = mysqli_fetch_assoc($recent_result)): ?>

                                <tr>

                                    <td>
                                        <?php echo $student['id']; ?>
                                    </td>

                                    <td>

                                        <strong>
                                            <?php
                                            echo htmlspecialchars(
                                                $student['student_id']
                                            );
                                            ?>
                                        </strong>

                                    </td>

                                    <td>

                                        <?php
                                        echo htmlspecialchars(
                                            $student['first_name']
                                            . ' '
                                            . $student['last_name']
                                        );
                                        ?>

                                    </td>

                                    <td>

                                        <?php
                                        echo htmlspecialchars(
                                            $student['email']
                                        );
                                        ?>

                                    </td>

                                    <td>

                                        <?php if (!empty($student['course_name'])): ?>

                                            <?php
                                            echo htmlspecialchars(
                                                $student['course_name']
                                            );
                                            ?>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                Not Assigned
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td>

                                        <a
                                            href="view-student.php?id=<?php echo $student['id']; ?>"
                                            class="btn btn-info btn-sm"
                                        >
                                            View
                                        </a>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="6"
                                    class="text-center py-4"
                                >

                                    <h6>
                                        No Students Found
                                    </h6>

                                    <p class="text-muted mb-0">
                                        No students have been added yet.
                                    </p>

                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>


                <div class="text-end mt-3">

                    <a
                        href="students.php"
                        class="btn btn-primary"
                    >
                        View All Students →
                    </a>

                </div>

            </div>

        </div>


        <!-- ===============================
             ADMIN ACCOUNT
        ================================ -->

        <div class="card section-card mb-4">


            <div class="card-header">

                <h5 class="mb-0">
                    👤 Admin Account
                </h5>

            </div>


            <div class="card-body">

                <div class="row align-items-center">

                    <div class="col-md-6">

                        <p class="mb-2">

                            <strong>
                                Name:
                            </strong>

                            <?php
                            echo htmlspecialchars(
                                $_SESSION['name']
                            );
                            ?>

                        </p>


                        <p class="mb-0">

                            <strong>
                                Role:
                            </strong>

                            <span class="badge">
                                <?php
                                echo htmlspecialchars(
                                    $_SESSION['role']
                                );
                                ?>
                            </span>

                        </p>

                    </div>


                    <div class="col-md-6 text-md-end mt-3 mt-md-0">

                        <a
                            href="profile.php"
                            class="btn btn-primary me-2"
                        >
                            My Profile
                        </a>

                        <a
                            href="change-password.php"
                            class="btn btn-secondary me-2"
                        >
                            Change Password
                        </a>

                        <a
                            href="../auth/logout.php"
                            class="btn btn-danger"
                        >
                            Logout
                        </a>

                    </div>

                </div>

            </div>

        </div>


    </div>

</div>


<!-- ===============================
     SIDEBAR JAVASCRIPT
================================ -->

<script>

    function toggleSidebar() {

        const sidebar = document.getElementById("sidebar");

        sidebar.classList.toggle("show");

    }

</script>


</body>

</html>
```
