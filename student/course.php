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
| Fetch Student Course
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

    <title>My Course - Student Management System</title>

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


        /* Course Card */

        .course-card {
            background: #5A1622;

            border: 1px solid #7A2230;

            border-radius: 15px;

            padding: 35px;

            transition: 0.3s;
        }

        .course-card:hover {
            background: #000000;
            border-color: #D4AF7F;
        }


        .course-icon {
            width: 80px;
            height: 80px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #3F0D16;

            border: 1px solid #7A2230;

            border-radius: 50%;

            font-size: 35px;

            margin-bottom: 20px;
        }


        .course-title {
            color: #D4AF7F;

            font-size: 28px;

            font-weight: bold;

            margin-bottom: 8px;
        }


        .course-code {
            color: #d8d8d8;

            font-size: 16px;

            margin-bottom: 30px;
        }


        /* Information Boxes */

        .info-box {
            background: #3F0D16;

            border: 1px solid #7A2230;

            border-radius: 12px;

            padding: 20px;

            height: 100%;
        }

        .info-label {
            color: #D4AF7F;

            font-size: 14px;

            margin-bottom: 6px;
        }

        .info-value {
            color: #ffffff;

            font-weight: 500;

            font-size: 17px;
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


        /* No Course */

        .no-course {
            background: #5A1622;

            border: 1px solid #7A2230;

            border-radius: 15px;

            padding: 50px;

            text-align: center;
        }

        .no-course h4 {
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


    <a href="course.php" class="active">
        📚 My Course
    </a>


    <a href="academic.php">
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
                My Course
            </h3>

            <small>
                View your currently assigned course
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


    <?php if ($student && !empty($student['course_name'])): ?>


        <!-- Course Card -->

        <div class="course-card">


            <div class="course-icon">
                📚
            </div>


            <div class="course-title">

                <?php
                echo htmlspecialchars(
                    $student['course_name']
                );
                ?>

            </div>


            <div class="course-code">

                Course Code:

                <strong>
                    <?php
                    echo htmlspecialchars(
                        $student['course_code'] ?? 'N/A'
                    );
                    ?>
                </strong>

            </div>


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


                <!-- Status -->

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


            </div>


        </div>


    <?php else: ?>


        <!-- No Course -->

        <div class="no-course">

            <div style="font-size: 50px;">
                📚
            </div>

            <h4 class="mt-3">
                No Course Assigned
            </h4>

            <p class="text-light">
                You currently don't have a course assigned.
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