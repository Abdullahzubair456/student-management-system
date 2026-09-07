<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/database.php';


// Check Course ID

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: courses.php");
    exit;
}

$id = intval($_GET['id']);


// Get Course

$sql = "SELECT
            id,
            course_name,
            course_code
        FROM courses
        WHERE id = $id
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}


// Check Course Exists

if (mysqli_num_rows($result) == 0) {
    die("Course not found.");
}

$course = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        View Course -
        <?php echo htmlspecialchars($course['course_name']); ?>
    </title>

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


        /* =========================
           SIDEBAR
        ========================= */

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

            z-index: 1000;

            display: flex;
            flex-direction: column;
        }


        .sidebar-brand {
            padding: 25px 20px;

            font-size: 21px;
            font-weight: bold;

            color: #D4AF7F;

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

            border-radius: 8px;

            transition: 0.2s;
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

            padding: 12px 16px;

            color: #ffffff;

            text-decoration: none;

            border-radius: 8px;

            transition: 0.2s;
        }


        .sidebar-bottom a:hover {
            background: #000000;
            color: #D4AF7F;
        }


        /* =========================
           MAIN CONTENT
        ========================= */

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }


        /* =========================
           TOPBAR
        ========================= */

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
            font-size: 20px;

            font-weight: bold;

            color: #D4AF7F;
        }


        .admin-name {
            color: #ffffff;
        }


        /* =========================
           PAGE
        ========================= */

        .page-content {
            padding: 30px;
        }


        /* =========================
           PAGE HEADER
        ========================= */

        .page-header {
            background: #5A1622;

            padding: 25px;

            border-radius: 12px;

            border: 1px solid #7A2230;

            margin-bottom: 25px;

            transition: 0.2s;
        }


        .page-header:hover {
            background: #000000;
        }


        .page-header h2 {
            color: #D4AF7F;

            margin-bottom: 5px;
        }


        .page-header p {
            color: #dddddd;

            margin-bottom: 0;
        }


        /* =========================
           BUTTONS
        ========================= */

        .btn-luxury {
            background: #7A2230;

            color: #ffffff;

            border: 1px solid #8B2635;

            border-radius: 7px;

            padding: 9px 16px;

            text-decoration: none;

            transition: 0.2s;
        }


        .btn-luxury:hover {
            background: #000000;

            color: #D4AF7F;

            border-color: #D4AF7F;
        }


        .btn-back {
            background: #3F0D16;

            color: #ffffff;

            border: 1px solid #7A2230;

            border-radius: 7px;

            padding: 9px 16px;

            text-decoration: none;

            transition: 0.2s;
        }


        .btn-back:hover {
            background: #000000;

            color: #D4AF7F;
        }


        /* =========================
           COURSE CARD
        ========================= */

        .course-card {
            background: #5A1622;

            border: 1px solid #7A2230;

            border-radius: 12px;

            overflow: hidden;

            transition: 0.2s;
        }


        .course-card:hover {
            background: #000000;
        }


        .course-card-header {
            background: #3F0D16;

            padding: 18px 22px;

            border-bottom: 1px solid #7A2230;
        }


        .course-card-header h5 {
            margin: 0;

            color: #D4AF7F;
        }


        .course-card-body {
            padding: 30px;
        }


        /* =========================
           DETAIL BOXES
        ========================= */

        .detail-group {
            margin-bottom: 5px;
        }


        .detail-label {
            display: block;

            color: #D4AF7F;

            font-weight: bold;

            margin-bottom: 8px;
        }


        .detail-box {
            background: #3F0D16;

            color: #ffffff;

            border: 1px solid #7A2230;

            border-radius: 8px;

            padding: 13px 15px;

            min-height: 48px;
        }


        .course-id {
            color: #D4AF7F;

            font-weight: bold;
        }


        /* =========================
           FOOTER
        ========================= */

        .course-card-footer {
            background: #3F0D16;

            border-top: 1px solid #7A2230;

            padding: 18px 22px;
        }


        /* =========================
           MOBILE
        ========================= */

        .menu-toggle {
            display: none;

            background: #5A1622;

            color: #D4AF7F;

            border: 1px solid #7A2230;

            padding: 7px 12px;

            border-radius: 6px;
        }


        @media (max-width: 991px) {

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
                padding: 0 18px;
            }


            .admin-name {
                display: none;
            }


            .page-content {
                padding: 20px;
            }

        }


        @media (max-width: 576px) {

            .page-header {
                display: block !important;
            }


            .header-buttons {
                margin-top: 15px;
            }


            .course-card-body {
                padding: 20px;
            }


            .course-card-footer a {
                margin-bottom: 7px;
            }

        }

    </style>

</head>


<body>


<!-- =========================
     SIDEBAR
========================= -->

<aside class="sidebar" id="sidebar">


    <div class="sidebar-brand">

        Student Management

    </div>


    <div class="sidebar-menu">


        <a href="dashboard.php">

            🏠 Dashboard

        </a>


        <a href="students.php">

            👨‍🎓 Students

        </a>


        <a href="courses.php" class="active">

            📚 Courses

        </a>


        <a href="profile.php">

            👤 My Profile

        </a>


        <a href="change-password.php">

            🔐 Change Password

        </a>


    </div>


    <div class="sidebar-bottom">


        <a href="../auth/logout.php">

            🚪 Logout

        </a>


    </div>


</aside>



<!-- =========================
     MAIN CONTENT
========================= -->

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

                View Course

            </span>

        </div>


        <div class="admin-name">

            Admin:
            <?php echo htmlspecialchars($_SESSION['name']); ?>

        </div>


    </div>



    <!-- PAGE CONTENT -->

    <div class="page-content">


        <!-- PAGE HEADER -->

        <div
            class="page-header d-flex justify-content-between align-items-center"
        >


            <div>

                <h2>
                    Course Details
                </h2>


                <p>
                    View complete information about this course.
                </p>

            </div>


            <div class="header-buttons">


                <a
                    href="courses.php"
                    class="btn-back"
                >
                    ← Back to Courses
                </a>


                <a
                    href="edit-course.php?id=<?php echo $course['id']; ?>"
                    class="btn-luxury"
                >
                    ✏️ Edit Course
                </a>


            </div>


        </div>



        <!-- COURSE CARD -->

        <div class="course-card">


            <div class="course-card-header">

                <h5>
                    📚 Course Information
                </h5>

            </div>



            <div class="course-card-body">


                <div class="row g-4">


                    <!-- COURSE ID -->

                    <div class="col-md-6">


                        <div class="detail-group">


                            <span class="detail-label">

                                Course ID

                            </span>


                            <div class="detail-box course-id">

                                <?php
                                echo $course['id'];
                                ?>

                            </div>


                        </div>


                    </div>



                    <!-- COURSE NAME -->

                    <div class="col-md-6">


                        <div class="detail-group">


                            <span class="detail-label">

                                Course Name

                            </span>


                            <div class="detail-box">

                                <?php
                                echo htmlspecialchars(
                                    $course['course_name']
                                );
                                ?>

                            </div>


                        </div>


                    </div>



                    <!-- COURSE CODE -->

                    <div class="col-md-6">


                        <div class="detail-group">


                            <span class="detail-label">

                                Course Code

                            </span>


                            <div class="detail-box">

                                <?php
                                echo htmlspecialchars(
                                    $course['course_code']
                                );
                                ?>

                            </div>


                        </div>


                    </div>


                </div>


            </div>



            <!-- FOOTER -->

            <div class="course-card-footer">


                <a
                    href="courses.php"
                    class="btn-back"
                >
                    ← Back
                </a>


                <a
                    href="edit-course.php?id=<?php echo $course['id']; ?>"
                    class="btn-luxury"
                >
                    ✏️ Edit Course
                </a>


            </div>


        </div>


    </div>


</div>



<script>

function toggleSidebar() {

    const sidebar =
        document.getElementById("sidebar");

    sidebar.classList.toggle("show");

}

</script>


</body>

</html>