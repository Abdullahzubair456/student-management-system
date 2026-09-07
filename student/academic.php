<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] !== 'student') {
    header("Location: ../admin/dashboard.php");
    exit;
}

include '../config/database.php';

$user_id = $_SESSION['user_id'];


/*
|--------------------------------------------------------------------------
| Fetch Academic Information
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        s.student_id,
        s.first_name,
        s.last_name,
        s.status,
        c.course_name,
        c.course_code
     FROM students s
     LEFT JOIN courses c ON s.course_id = c.id
     WHERE s.user_id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$student = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Academic Information - Student Management System</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #0b0b0b;
            color: #ffffff;
            min-height: 100vh;
        }

        /* Sidebar */

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

            padding: 25px 15px;

            z-index: 1000;
        }

        .sidebar-logo {
            text-align: center;
            margin-bottom: 35px;
        }

        .sidebar-logo h4 {
            color: #D4AF7F;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .sidebar-logo small {
            color: #d8d8d8;
        }

        .sidebar a {
            display: block;

            color: #ffffff;
            text-decoration: none;

            padding: 13px 15px;
            margin-bottom: 8px;

            border-radius: 8px;

            transition: 0.3s;
        }

        .sidebar a:hover {
            background: #000000;
            color: #D4AF7F;
        }

        .sidebar a.active {
            background: #000000;
            color: #D4AF7F;

            border-left: 4px solid #D4AF7F;
        }

        .logout {
            margin-top: 30px;

            border-top: 1px solid #7A2230;

            padding-top: 20px;
        }


        /* Main Content */

        .main-content {
            margin-left: 250px;
            padding: 30px;
        }


        /* Topbar */

        .topbar {
            background: #3F0D16;

            border: 1px solid #7A2230;

            border-radius: 12px;

            padding: 18px 25px;

            margin-bottom: 25px;
        }

        .topbar h3 {
            color: #D4AF7F;
            margin: 0;
        }


        /* Main Card */

        .academic-card {
            background: #5A1622;

            border: 1px solid #7A2230;

            border-radius: 15px;

            padding: 30px;

            transition: 0.3s;
        }

        .academic-card:hover {
            background: #000000;
            border-color: #D4AF7F;
        }


        .section-title {
            color: #D4AF7F;

            font-weight: bold;

            border-bottom: 1px solid #7A2230;

            padding-bottom: 12px;

            margin-bottom: 25px;
        }


        /* Info Boxes */

        .info-box {
            background: #3F0D16;

            border: 1px solid #7A2230;

            border-radius: 12px;

            padding: 20px;

            height: 100%;

            transition: 0.3s;
        }

        .info-box:hover {
            background: #000000;
            border-color: #D4AF7F;
        }

        .info-label {
            color: #D4AF7F;

            font-size: 14px;

            margin-bottom: 7px;
        }

        .info-value {
            color: #ffffff;

            font-size: 17px;

            font-weight: 500;
        }


        /* Course Header */

        .course-header {
            text-align: center;

            padding: 25px;

            margin-bottom: 25px;

            background: #3F0D16;

            border: 1px solid #7A2230;

            border-radius: 12px;
        }

        .course-icon {
            font-size: 50px;

            margin-bottom: 10px;
        }

        .course-name {
            color: #D4AF7F;

            font-size: 27px;

            font-weight: bold;
        }

        .course-code {
            color: #d8d8d8;

            margin-top: 5px;
        }


        /* Status */

        .status-badge {
            display: inline-block;

            background: #7A2230;

            color: #ffffff;

            padding: 7px 15px;

            border-radius: 20px;

            font-size: 14px;
        }


        /* Notice */

        .notice {
            background: #25070d;

            border: 1px solid #7A2230;

            border-radius: 12px;

            padding: 20px;

            margin-top: 25px;
        }

        .notice-title {
            color: #D4AF7F;

            font-weight: bold;

            margin-bottom: 8px;
        }


        /* No Record */

        .no-record {
            background: #5A1622;

            border: 1px solid #7A2230;

            border-radius: 15px;

            padding: 50px;

            text-align: center;
        }

        .no-record h4 {
            color: #D4AF7F;
        }


        /* Mobile */

        .menu-btn {
            display: none;

            background: #7A2230;

            color: #ffffff;

            border: none;

            border-radius: 6px;

            padding: 8px 12px;
        }


        @media (max-width: 768px) {

            .sidebar {
                transform: translateX(-100%);

                transition: 0.3s;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;

                padding: 20px;
            }

            .menu-btn {
                display: block;
            }

        }

    </style>

</head>

<body>


<!-- Sidebar -->

<div class="sidebar" id="sidebar">

    <div class="sidebar-logo">

        <h4>Student Portal</h4>

        <small>
            Student Management System
        </small>

    </div>


    <a href="dashboard.php">
        🏠 Dashboard
    </a>


    <a href="profile.php">
        👤 My Profile
    </a>


    <a href="course.php">
        📚 My Course
    </a>


    <a href="academic.php" class="active">
        📊 Academic Information
    </a>


    <div class="logout">

        <a href="../auth/logout.php">
            🚪 Logout
        </a>

    </div>

</div>


<!-- Main Content -->

<div class="main-content">


    <!-- Topbar -->

    <div class="topbar d-flex justify-content-between align-items-center">

        <div>

            <h3>
                Academic Information
            </h3>

            <small>
                View your academic and enrollment information
            </small>

        </div>


        <div class="d-flex align-items-center gap-3">

            <span>
                <?php echo htmlspecialchars($_SESSION['name']); ?>
            </span>

            <button
                class="menu-btn"
                onclick="toggleSidebar()"
            >
                ☰
            </button>

        </div>

    </div>


    <?php if ($student): ?>


        <!-- Course Header -->

        <div class="course-header">

            <div class="course-icon">
                📚
            </div>

            <div class="course-name">

                <?php
                echo htmlspecialchars(
                    $student['course_name'] ?? 'Course Not Assigned'
                );
                ?>

            </div>

            <div class="course-code">

                Course Code:

                <?php
                echo htmlspecialchars(
                    $student['course_code'] ?? 'N/A'
                );
                ?>

            </div>

        </div>


        <!-- Academic Information -->

        <div class="academic-card">

            <h4 class="section-title">
                Student Academic Details
            </h4>


            <div class="row g-4">


                <!-- Student ID -->

                <div class="col-md-4">

                    <div class="info-box">

                        <div class="info-label">
                            Student ID
                        </div>

                        <div class="info-value">

                            <?php
                            echo htmlspecialchars(
                                $student['student_id']
                            );
                            ?>

                        </div>

                    </div>

                </div>


                <!-- Student Name -->

                <div class="col-md-4">

                    <div class="info-box">

                        <div class="info-label">
                            Student Name
                        </div>

                        <div class="info-value">

                            <?php
                            echo htmlspecialchars(
                                $student['first_name'] . ' ' .
                                $student['last_name']
                            );
                            ?>

                        </div>

                    </div>

                </div>


                <!-- Enrollment Status -->

                <div class="col-md-4">

                    <div class="info-box">

                        <div class="info-label">
                            Enrollment Status
                        </div>

                        <div class="info-value">

                            <span class="status-badge">

                                <?php
                                echo htmlspecialchars(
                                    $student['status']
                                );
                                ?>

                            </span>

                        </div>

                    </div>

                </div>


                <!-- Course -->

                <div class="col-md-6">

                    <div class="info-box">

                        <div class="info-label">
                            Enrolled Course
                        </div>

                        <div class="info-value">

                            <?php
                            echo htmlspecialchars(
                                $student['course_name'] ?? 'Not Assigned'
                            );
                            ?>

                        </div>

                    </div>

                </div>


                <!-- Course Code -->

                <div class="col-md-6">

                    <div class="info-box">

                        <div class="info-label">
                            Course Code
                        </div>

                        <div class="info-value">

                            <?php
                            echo htmlspecialchars(
                                $student['course_code'] ?? 'N/A'
                            );
                            ?>

                        </div>

                    </div>

                </div>


            </div>


            <!-- Notice -->

            <div class="notice">

                <div class="notice-title">
                    📌 Academic Records
                </div>

                <p class="mb-0 text-light">

                    Your marks, grades, attendance and examination
                    records will appear here once they are added
                    by the administrator.

                </p>

            </div>


        </div>


    <?php else: ?>


        <!-- No Record -->

        <div class="no-record">

            <div style="font-size: 50px;">
                📊
            </div>

            <h4 class="mt-3">
                Academic Information Not Available
            </h4>

            <p class="text-light">

                Your student profile has not been linked yet.
                Please contact the administrator.

            </p>

        </div>


    <?php endif; ?>


</div>


<script>

function toggleSidebar() {

    document
        .getElementById("sidebar")
        .classList
        .toggle("show");

}

</script>


</body>

</html>