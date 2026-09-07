<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/database.php';

$error = "";
$success = "";


// Add Course

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $course_name = trim($_POST['course_name']);
    $course_code = trim($_POST['course_code']);


    // Validation

    if (empty($course_name) || empty($course_code)) {

        $error = "Please fill in all fields.";

    } else {

        // Check duplicate course code

        $check_sql = "SELECT id FROM courses WHERE course_code = ? LIMIT 1";

        $check_stmt = mysqli_prepare($conn, $check_sql);

        mysqli_stmt_bind_param(
            $check_stmt,
            "s",
            $course_code
        );

        mysqli_stmt_execute($check_stmt);

        $check_result = mysqli_stmt_get_result($check_stmt);


        if (mysqli_num_rows($check_result) > 0) {

            $error = "This course code already exists.";

        } else {

            // Insert Course

            $insert_sql = "INSERT INTO courses
                           (course_name, course_code)
                           VALUES (?, ?)";

            $stmt = mysqli_prepare($conn, $insert_sql);

            mysqli_stmt_bind_param(
                $stmt,
                "ss",
                $course_name,
                $course_code
            );


            if (mysqli_stmt_execute($stmt)) {

                header("Location: courses.php?added=1");
                exit;

            } else {

                $error = "Course could not be added: " . mysqli_error($conn);

            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($check_stmt);
    }
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

    <title>Add Course</title>

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
           PAGE CONTENT
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
           ERROR
        ========================= */

        .error-box {
            background: #3F0D16;

            color: #ffffff;

            border: 1px solid #8B2635;

            border-radius: 8px;

            padding: 14px 18px;

            margin-bottom: 20px;
        }


        /* =========================
           FORM CARD
        ========================= */

        .form-card {
            background: #5A1622;

            border: 1px solid #7A2230;

            border-radius: 12px;

            overflow: hidden;

            transition: 0.2s;
        }


        .form-card:hover {
            background: #000000;
        }


        .form-card-header {
            background: #3F0D16;

            padding: 18px 22px;

            border-bottom: 1px solid #7A2230;
        }


        .form-card-header h5 {
            margin: 0;

            color: #D4AF7F;
        }


        .form-card-body {
            padding: 30px;
        }


        /* =========================
           FORM
        ========================= */

        .form-label {
            color: #D4AF7F;

            margin-bottom: 8px;
        }


        .form-control {
            background: #3F0D16;

            color: #ffffff;

            border: 1px solid #7A2230;

            border-radius: 7px;

            padding: 11px 14px;
        }


        .form-control:focus {
            background: #000000;

            color: #ffffff;

            border-color: #D4AF7F;

            box-shadow: none;
        }


        .form-control::placeholder {
            color: #aaaaaa;
        }


        .help-text {
            color: #bbbbbb;

            display: block;

            margin-top: 7px;
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


            .page-header .header-button {
                display: inline-block;

                margin-top: 15px;
            }


            .form-card-body {
                padding: 20px;
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

                Add Course

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
                    Add New Course
                </h2>


                <p>
                    Add a new course to the student management system.
                </p>

            </div>


            <div>

                <a
                    href="courses.php"
                    class="btn-back header-button"
                >
                    ← Back to Courses
                </a>

            </div>


        </div>



        <!-- ERROR -->

        <?php if (!empty($error)): ?>


            <div class="error-box">

                ⚠️
                <?php echo htmlspecialchars($error); ?>

            </div>


        <?php endif; ?>



        <!-- FORM CARD -->

        <div class="form-card">


            <div class="form-card-header">

                <h5>
                    Course Information
                </h5>

            </div>



            <div class="form-card-body">


                <form method="POST">


                    <div class="row g-4">


                        <!-- COURSE NAME -->

                        <div class="col-md-6">


                            <label
                                for="course_name"
                                class="form-label fw-bold"
                            >
                                Course Name
                            </label>


                            <input
                                type="text"
                                id="course_name"
                                name="course_name"
                                class="form-control"
                                placeholder="Example: Web Development"
                                value="<?php echo isset($_POST['course_name']) ? htmlspecialchars($_POST['course_name']) : ''; ?>"
                                required
                            >


                        </div>



                        <!-- COURSE CODE -->

                        <div class="col-md-6">


                            <label
                                for="course_code"
                                class="form-label fw-bold"
                            >
                                Course Code
                            </label>


                            <input
                                type="text"
                                id="course_code"
                                name="course_code"
                                class="form-control"
                                placeholder="Example: WD-101"
                                value="<?php echo isset($_POST['course_code']) ? htmlspecialchars($_POST['course_code']) : ''; ?>"
                                required
                            >


                            <small class="help-text">

                                Course code must be unique.

                            </small>


                        </div>


                    </div>



                    <!-- BUTTONS -->

                    <div class="mt-4">


                        <button
                            type="submit"
                            class="btn-luxury"
                        >
                            + Add Course
                        </button>


                        <a
                            href="courses.php"
                            class="btn-back"
                        >
                            Cancel
                        </a>


                    </div>


                </form>


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

</html>vv