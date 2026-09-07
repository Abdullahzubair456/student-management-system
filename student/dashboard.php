```php
<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit;
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

    <title>Student Dashboard - Student Management System</title>

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
            background: #0b0b0b;
            color: #ffffff;
            font-family: Arial, sans-serif;
        }

        /* ================= SIDEBAR ================= */

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;

            width: 250px;
            height: 100vh;

            background: linear-gradient(
                180deg,
                #3F0D16,
                #25070d
            );

            border-right: 1px solid #7A2230;

            display: flex;
            flex-direction: column;

            z-index: 1000;
        }

        .sidebar-brand {
            padding: 25px 20px;

            font-size: 21px;
            font-weight: bold;

            color: #D4AF7F;

            text-align: center;

            border-bottom: 1px solid #7A2230;
        }

        .sidebar-menu {
            padding: 20px 12px;
        }

        .sidebar-menu a {
            display: block;

            padding: 13px 16px;

            margin-bottom: 8px;

            color: #ffffff;

            text-decoration: none;

            border-radius: 7px;

            transition: 0.3s;
        }

        .sidebar-menu a:hover {
            background: #000000;
            color: #D4AF7F;
        }

        .sidebar-menu a.active {
            background: #5A1622;

            color: #D4AF7F;

            border-left: 4px solid #D4AF7F;
        }

        .sidebar-bottom {
            margin-top: auto;

            padding: 15px 12px;

            border-top: 1px solid #7A2230;
        }

        .sidebar-bottom a {
            display: block;

            padding: 13px 16px;

            color: #ffffff;

            text-decoration: none;

            border-radius: 7px;

            transition: 0.3s;
        }

        .sidebar-bottom a:hover {
            background: #000000;
            color: #D4AF7F;
        }

        /* ================= MAIN ================= */

        .main-content {
            margin-left: 250px;

            min-height: 100vh;
        }

        /* ================= TOPBAR ================= */

        .topbar {
            height: 70px;

            background: #0b0b0b;

            border-bottom: 1px solid #3F0D16;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 30px;
        }

        .topbar-title {
            color: #D4AF7F;

            font-size: 20px;

            font-weight: bold;
        }

        .student-name {
            color: #ffffff;
        }

        .menu-toggle {
            display: none;

            background: #5A1622;

            color: #ffffff;

            border: 1px solid #7A2230;

            padding: 7px 12px;

            border-radius: 6px;

            font-size: 20px;
        }

        /* ================= PAGE ================= */

        .page-content {
            padding: 30px;
        }

        .welcome-box {
            background: #5A1622;

            padding: 30px;

            border-radius: 12px;

            border: 1px solid #7A2230;

            margin-bottom: 25px;

            transition: 0.3s;
        }

        .welcome-box:hover {
            background: #000000;
        }

        .welcome-box h2 {
            color: #D4AF7F;

            margin-bottom: 8px;
        }

        .welcome-box p {
            color: #dddddd;

            margin-bottom: 0;
        }

        /* ================= CARDS ================= */

        .dashboard-card {
            background: #5A1622;

            border: 1px solid #7A2230;

            border-radius: 12px;

            height: 100%;

            transition: 0.3s;
        }

        .dashboard-card:hover {
            background: #000000;

            border-color: #D4AF7F;

            transform: translateY(-3px);
        }

        .dashboard-card-body {
            padding: 25px;
        }

        .card-icon {
            font-size: 38px;

            margin-bottom: 15px;
        }

        .dashboard-card h5 {
            color: #D4AF7F;

            margin-bottom: 10px;
        }

        .dashboard-card p {
            color: #dddddd;

            min-height: 48px;
        }

        .card-btn {
            display: inline-block;

            background: #7A2230;

            color: #ffffff;

            border: 1px solid #8B2635;

            padding: 9px 16px;

            border-radius: 7px;

            text-decoration: none;

            transition: 0.3s;
        }

        .card-btn:hover {
            background: #000000;

            color: #D4AF7F;

            border-color: #D4AF7F;
        }

        /* ================= QUICK INFO ================= */

        .info-box {
            background: #3F0D16;

            border: 1px solid #7A2230;

            border-radius: 12px;

            padding: 22px;

            margin-top: 25px;
        }

        .info-box h5 {
            color: #D4AF7F;

            margin-bottom: 15px;
        }

        .info-box p {
            color: #dddddd;

            margin-bottom: 0;
        }

        /* ================= MOBILE ================= */

        @media (max-width: 768px) {

            .sidebar {
                left: -250px;

                transition: 0.3s;
            }

            .sidebar.show {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: inline-block;
            }

            .topbar {
                padding: 0 15px;
            }

            .student-name {
                font-size: 14px;
            }

            .page-content {
                padding: 20px 15px;
            }

            .welcome-box {
                padding: 22px;
            }

        }

    </style>

</head>


<body>


<!-- ================= SIDEBAR ================= -->

<aside class="sidebar" id="sidebar">

    <div class="sidebar-brand">
        Student Management
    </div>


    <div class="sidebar-menu">

        <a
            href="dashboard.php"
            class="active"
        >
            🏠 Dashboard
        </a>

        <a href="profile.php">
            👤 My Profile
        </a>

        <a href="course.php">
            📚 My Course
        </a>

        <a href="academic.php">
            📊 Academic Information
        </a>

    </div>


    <div class="sidebar-bottom">

        <a href="../auth/logout.php">
            🚪 Logout
        </a>

    </div>

</aside>


<!-- ================= MAIN CONTENT ================= -->

<div class="main-content">


    <!-- TOPBAR -->

    <div class="topbar">

        <div>

            <button
                class="menu-toggle"
                onclick="toggleSidebar()"
            >
                ☰
            </button>

            <span class="topbar-title ms-2">
                Student Dashboard
            </span>

        </div>


        <div class="student-name">

            Student:
            <?php echo htmlspecialchars($_SESSION['name']); ?>

        </div>

    </div>


    <!-- PAGE CONTENT -->

    <div class="page-content">


        <!-- WELCOME -->

        <div class="welcome-box">

            <h2>
                👋 Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!
            </h2>

            <p>
                Welcome to your Student Management System dashboard.
                Here you can manage and view your academic information.
            </p>

        </div>


        <!-- DASHBOARD CARDS -->

        <div class="row g-4">


            <!-- PROFILE -->

            <div class="col-lg-4 col-md-6">

                <div class="dashboard-card">

                    <div class="dashboard-card-body">

                        <div class="card-icon">
                            👤
                        </div>

                        <h5>
                            My Profile
                        </h5>

                        <p>
                            View and manage your personal account information.
                        </p>

                        <a
                            href="profile.php"
                            class="card-btn"
                        >
                            View Profile →
                        </a>

                    </div>

                </div>

            </div>


            <!-- COURSE -->

            <div class="col-lg-4 col-md-6">

                <div class="dashboard-card">

                    <div class="dashboard-card-body">

                        <div class="card-icon">
                            📚
                        </div>

                        <h5>
                            My Course
                        </h5>

                        <p>
                            View your enrolled course and course information.
                        </p>

                        <a
                            href="course.php"
                            class="card-btn"
                        >
                            View Course →
                        </a>

                    </div>

                </div>

            </div>


            <!-- ACADEMIC -->

            <div class="col-lg-4 col-md-6">

                <div class="dashboard-card">

                    <div class="dashboard-card-body">

                        <div class="card-icon">
                            📊
                        </div>

                        <h5>
                            Academic Information
                        </h5>

                        <p>
                            View your academic and educational information.
                        </p>

                        <a
                            href="academic.php"
                            class="card-btn"
                        >
                            View Information →
                        </a>

                    </div>

                </div>

            </div>


        </div>


        <!-- QUICK INFORMATION -->

        <div class="info-box">

            <h5>
                ℹ️ Student Portal
            </h5>

            <p>
                Use the menu on the left to access your profile,
                course information and academic details.
            </p>

        </div>


    </div>

</div>


<script>

function toggleSidebar() {

    const sidebar = document.getElementById("sidebar");

    sidebar.classList.toggle("show");

}

</script>


</body>

</html>
```
