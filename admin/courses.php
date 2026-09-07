<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/database.php';


$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT 
            id,
            course_name,
            course_code
        FROM courses";

if (!empty($search)) {

    $search = mysqli_real_escape_string($conn, $search);

    $sql .= " WHERE
                course_name LIKE '%$search%'
                OR course_code LIKE '%$search%'";
}

$sql .= " ORDER BY id DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}


/* Total Courses */

$total_sql = "SELECT COUNT(*) AS total FROM courses";

$total_result = mysqli_query($conn, $total_sql);

$total_data = mysqli_fetch_assoc($total_result);

$total_courses = $total_data['total'];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Manage Courses</title>

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
           TOP BAR
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


        .btn-dashboard {
            background: #3F0D16;
            color: #ffffff;

            border: 1px solid #7A2230;

            border-radius: 7px;

            padding: 9px 16px;

            text-decoration: none;

            transition: 0.2s;
        }


        .btn-dashboard:hover {
            background: #000000;
            color: #D4AF7F;
        }


        /* =========================
           ALERTS
        ========================= */

        .luxury-alert {
            background: #3F0D16;
            color: #ffffff;

            border: 1px solid #7A2230;

            border-radius: 8px;
        }


        .luxury-alert strong {
            color: #D4AF7F;
        }


        /* =========================
           TOTAL COURSES
        ========================= */

        .total-box {
            background: #5A1622;

            border: 1px solid #7A2230;

            border-radius: 10px;

            padding: 18px 20px;

            margin-bottom: 20px;

            color: #ffffff;

            transition: 0.2s;
        }


        .total-box:hover {
            background: #000000;
        }


        .total-box strong {
            color: #D4AF7F;
        }


        /* =========================
           SEARCH
        ========================= */

        .search-box {
            background: #5A1622;

            border: 1px solid #7A2230;

            border-radius: 10px;

            padding: 20px;

            margin-bottom: 20px;
        }


        .search-input {
            background: #3F0D16;
            color: #ffffff;

            border: 1px solid #7A2230;

            border-radius: 7px;

            padding: 11px 14px;
        }


        .search-input:focus {
            background: #000000;
            color: #ffffff;

            border-color: #D4AF7F;

            box-shadow: none;
        }


        .search-input::placeholder {
            color: #bbbbbb;
        }


        /* =========================
           COURSES CARD
        ========================= */

        .courses-card {
            background: #5A1622;

            border: 1px solid #7A2230;

            border-radius: 12px;

            overflow: hidden;

            transition: 0.2s;
        }


        .courses-card:hover {
            background: #000000;
        }


        .courses-card-header {
            background: #3F0D16;

            padding: 18px 22px;

            border-bottom: 1px solid #7A2230;
        }


        .courses-card-header h5 {
            margin: 0;

            color: #D4AF7F;
        }


        .courses-card-body {
            padding: 22px;
        }


        /* =========================
           TABLE
        ========================= */

        .courses-table {
            margin-bottom: 0;

            color: #ffffff;
        }


        .courses-table thead th {
            background: #3F0D16 !important;

            color: #D4AF7F !important;

            border-color: #7A2230 !important;

            padding: 14px;
        }


        .courses-table tbody tr {
            background: #5A1622 !important;

            color: #ffffff !important;
        }


        /* NO ROW HOVER */

        .courses-table tbody tr:hover {
            background: #5A1622 !important;

            color: #ffffff !important;
        }


        .courses-table tbody td {
            background: #5A1622 !important;

            color: #ffffff !important;

            border-color: #7A2230 !important;

            padding: 14px;
        }


        .courses-table tbody tr:hover td {
            background: #5A1622 !important;

            color: #ffffff !important;
        }


        /* =========================
           ACTION BUTTONS
        ========================= */

        .action-btn {
            background: #7A2230;

            color: #ffffff;

            border: 1px solid #8B2635;

            padding: 6px 11px;

            border-radius: 6px;

            text-decoration: none;

            font-size: 13px;

            display: inline-block;

            margin-right: 4px;

            transition: 0.2s;
        }


        .action-btn:hover {
            background: #000000;

            color: #D4AF7F;

            border-color: #D4AF7F;
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


            .page-header {
                padding: 20px;
            }


        }


        @media (max-width: 576px) {

            .page-header .header-buttons {
                margin-top: 15px;
            }


            .page-header {
                display: block !important;
            }


            .header-buttons a {
                display: inline-block;

                margin-bottom: 7px;
            }


            .courses-card-body {
                padding: 12px;
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


    <!-- TOP BAR -->

    <div class="topbar">

        <div>

            <button
                class="menu-toggle"
                onclick="toggleSidebar()"
            >
                ☰
            </button>

            <span class="topbar-title ms-2">
                Manage Courses
            </span>

        </div>


        <div class="admin-name">

            Admin:
            <?php echo htmlspecialchars($_SESSION['name']); ?>

        </div>

    </div>



    <!-- PAGE CONTENT -->

    <div class="page-content">


        <!-- HEADER -->

        <div
            class="page-header d-flex justify-content-between align-items-center"
        >

            <div>

                <h2>
                    Manage Courses
                </h2>

                <p>
                    View and manage all available courses.
                </p>

            </div>


            <div class="header-buttons">

                <a
                    href="dashboard.php"
                    class="btn-dashboard"
                >
                    ← Dashboard
                </a>


                <a
                    href="add-course.php"
                    class="btn-luxury"
                >
                    + Add Course
                </a>

            </div>

        </div>



        <!-- ALERT: DELETED -->

        <?php if (isset($_GET['deleted']) && $_GET['deleted'] == '1'): ?>

            <div class="alert luxury-alert alert-dismissible fade show">

                Course deleted successfully.

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="alert"
                ></button>

            </div>

        <?php endif; ?>



        <!-- ALERT: NOT FOUND -->

        <?php if (isset($_GET['error']) && $_GET['error'] == 'not_found'): ?>

            <div class="alert luxury-alert alert-dismissible fade show">

                Course not found.

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="alert"
                ></button>

            </div>

        <?php endif; ?>



        <!-- ALERT: INVALID ID -->

        <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid_id'): ?>

            <div class="alert luxury-alert alert-dismissible fade show">

                Invalid course ID.

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="alert"
                ></button>

            </div>

        <?php endif; ?>



        <!-- TOTAL COURSES -->

        <div class="total-box">

            <strong>
                Total Courses:
            </strong>

            <?php echo $total_courses; ?>

        </div>



        <!-- SEARCH -->

        <div class="search-box">

            <form
                method="GET"
                class="row g-2"
            >

                <div class="col-md-10">

                    <input
                        type="text"
                        name="search"
                        class="form-control search-input"
                        placeholder="Search course name or code..."
                        value="<?php echo htmlspecialchars($search); ?>"
                    >

                </div>


                <div class="col-md-2">

                    <button
                        type="submit"
                        class="btn-luxury w-100"
                    >
                        🔍 Search
                    </button>

                </div>

            </form>

        </div>



        <!-- COURSES CARD -->

        <div class="courses-card">


            <div class="courses-card-header">

                <h5>
                    Courses List
                </h5>

            </div>



            <div class="courses-card-body">


                <div class="table-responsive">


                    <table class="table courses-table align-middle">


                        <thead>

                            <tr>

                                <th>
                                    #
                                </th>

                                <th>
                                    Course Name
                                </th>

                                <th>
                                    Course Code
                                </th>

                                <th>
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php if (mysqli_num_rows($result) > 0): ?>


                            <?php while ($course = mysqli_fetch_assoc($result)): ?>


                                <tr>


                                    <td>

                                        <?php
                                        echo $course['id'];
                                        ?>

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

                                        <?php
                                        echo htmlspecialchars(
                                            $course['course_code']
                                        );
                                        ?>

                                    </td>


                                    <td>

                                        <a
                                            href="view-course.php?id=<?php echo $course['id']; ?>"
                                            class="action-btn"
                                        >
                                            View
                                        </a>


                                        <a
                                            href="edit-course.php?id=<?php echo $course['id']; ?>"
                                            class="action-btn"
                                        >
                                            Edit
                                        </a>


                                        <a
                                            href="delete-course.php?id=<?php echo $course['id']; ?>"
                                            class="action-btn"
                                            onclick="return confirm('Are you sure you want to delete this course?');"
                                        >
                                            Delete
                                        </a>

                                    </td>


                                </tr>


                            <?php endwhile; ?>


                        <?php else: ?>


                            <tr>

                                <td
                                    colspan="4"
                                    class="text-center py-5"
                                >

                                    <h5>
                                        No Courses Found
                                    </h5>

                                    <p class="text-muted">
                                        No courses have been added yet.
                                    </p>


                                    <a
                                        href="add-course.php"
                                        class="btn-luxury"
                                    >
                                        + Add First Course
                                    </a>

                                </td>

                            </tr>


                        <?php endif; ?>


                        </tbody>


                    </table>


                </div>


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


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>