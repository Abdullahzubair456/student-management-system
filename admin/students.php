```php
<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/database.php';


$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT 
            students.id,
            students.student_id,
            students.first_name,
            students.last_name,
            students.email,
            students.phone,
            students.gender,
            students.status,
            courses.course_name,
            courses.course_code
        FROM students
        LEFT JOIN courses 
        ON students.course_id = courses.id";

if (!empty($search)) {

    $search = mysqli_real_escape_string($conn, $search);

    $sql .= " WHERE 
                students.student_id LIKE '%$search%'
                OR students.first_name LIKE '%$search%'
                OR students.last_name LIKE '%$search%'
                OR students.email LIKE '%$search%'";

}

$sql .= " ORDER BY students.id DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
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

    <title>Manage Students - Student Management System</title>

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

            padding: 25px 15px;

            z-index: 1000;
        }


        .sidebar-brand {
            text-align: center;

            padding-bottom: 25px;
            margin-bottom: 25px;

            border-bottom: 1px solid #7a2230;
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
           SIDEBAR MENU
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
           SIDEBAR LOGOUT
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
           MAIN CONTENT
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
            font-size: 20px;
            font-weight: 700;
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
           PAGE HEADER
        ========================== */

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;

            background-color: #5a1622;

            border: 1px solid #7a2230;
            border-radius: 14px;

            padding: 25px;

            margin-bottom: 25px;

            transition: 0.25s ease;
        }


        .page-header:hover {
            background-color: #000000;
            border-color: #d4af7f;
        }


        .page-header h2 {
            color: #d4af7f;
            font-weight: 700;
            margin-bottom: 5px;
        }


        .page-header p {
            color: #e0cfd2;
            margin-bottom: 0;
        }


        /* =========================
           MAIN CARD
        ========================== */

        .students-card {
            background-color: #5a1622;

            border: 1px solid #7a2230;
            border-radius: 14px;

            overflow: hidden;
        }


        .students-card-body {
            padding: 25px;
        }


        /* =========================
           SEARCH
        ========================== */

        .search-input {
            background-color: #25070d;
            border: 1px solid #7a2230;

            color: #ffffff;

            padding: 11px 15px;

            border-radius: 8px;
        }


        .search-input:focus {
            background-color: #000000;

            color: #ffffff;

            border-color: #d4af7f;

            box-shadow: 0 0 0 0.2rem rgba(212, 175, 127, 0.15);
        }


        .search-input::placeholder {
            color: #c9b5b8;
        }


        /* =========================
           TABLE
        ========================== */

        .table {
            color: #ffffff;
            margin-bottom: 0;
        }


        .table-dark {
            --bs-table-bg: #3f0d16;
            --bs-table-color: #d4af7f;

            border-color: #7a2230;
        }


        .table td,
        .table th {
            border-color: #7a2230;
            vertical-align: middle;
        }


       


        .table th {
            white-space: nowrap;
        }


        /* =========================
           BADGES
        ========================== */

        .badge {
            border: 1px solid #7a2230;
        }


        .course-code {
            color: #d4af7f;
        }


        /* =========================
           BUTTONS
        ========================== */

        .btn-primary,
        .btn-info,
        .btn-warning,
        .btn-secondary {
            background-color: #7a2230;
            border-color: #8b2635;
            color: #ffffff;
        }


        .btn-primary:hover,
        .btn-info:hover,
        .btn-warning:hover,
        .btn-secondary:hover {
            background-color: #000000;
            border-color: #d4af7f;
            color: #d4af7f;
        }


        .btn-danger {
            background-color: #8b2635;
            border-color: #8b2635;
            color: #ffffff;
        }


        .btn-danger:hover {
            background-color: #000000;
            border-color: #d4af7f;
            color: #d4af7f;
        }


        /* =========================
           MOBILE MENU
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


            .page-content {
                padding: 20px;
            }

        }


        @media (max-width: 768px) {

            .page-header {
                flex-direction: column;
                align-items: flex-start;

                gap: 15px;
            }


            .page-header .header-buttons {
                width: 100%;
            }


            .page-header .header-buttons a {
                margin-bottom: 5px;
            }

        }


        @media (max-width: 576px) {

            .admin-name {
                display: none;
            }


            .page-content {
                padding: 15px;
            }


            .topbar {
                padding: 0 15px;
            }


            .topbar-title {
                font-size: 17px;
            }


            .students-card-body {
                padding: 15px;
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

            <a href="dashboard.php">

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

            <a
                href="students.php"
                class="active"
            >

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
                Manage Students
            </div>

        </div>


        <div class="admin-name">

            Admin:
            <strong>
                <?php echo htmlspecialchars($_SESSION['name']); ?>
            </strong>

        </div>

    </div>


    <!-- ===============================
         PAGE CONTENT
    ================================ -->

    <div class="page-content">


        <!-- ===============================
             PAGE HEADER
        ================================ -->

        <div class="page-header">


            <div>

                <h2>
                    Manage Students
                </h2>

                <p>
                    View and manage all registered students.
                </p>

            </div>


            <div class="header-buttons">

                <a
                    href="dashboard.php"
                    class="btn btn-secondary me-2"
                >
                    ← Dashboard
                </a>

                <a
                    href="add-student.php"
                    class="btn btn-primary"
                >
                    + Add Student
                </a>

            </div>


        </div>


        <!-- ===============================
             STUDENTS CARD
        ================================ -->

        <div class="students-card">


            <div class="students-card-body">


                <!-- Search -->

                <form
                    method="GET"
                    class="row g-2 mb-4"
                >

                    <div class="col-md-10">

                        <input
                            type="text"
                            name="search"
                            class="form-control search-input"
                            placeholder="Search by Student ID, Name or Email..."
                            value="<?php echo htmlspecialchars($search); ?>"
                        >

                    </div>


                    <div class="col-md-2">

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            🔍 Search
                        </button>

                    </div>

                </form>


                <!-- ===============================
                     STUDENTS TABLE
                ================================ -->

                <div class="table-responsive">

                    <table
                        class="table table-bordered table-hover align-middle"
                    >

                        <thead class="table-dark">

                            <tr>

                                <th>#</th>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Course</th>
                                <th>Gender</th>
                                <th>Status</th>
                                <th>Actions</th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php if (mysqli_num_rows($result) > 0): ?>


                            <?php while ($student = mysqli_fetch_assoc($result)): ?>


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

                                        <?php
                                        echo htmlspecialchars(
                                            $student['phone']
                                        );
                                        ?>

                                    </td>


                                    <td>


                                        <?php if ($student['course_name']): ?>


                                            <?php
                                            echo htmlspecialchars(
                                                $student['course_name']
                                            );
                                            ?>


                                            <br>


                                            <small class="course-code">

                                                <?php
                                                echo htmlspecialchars(
                                                    $student['course_code']
                                                );
                                                ?>

                                            </small>


                                        <?php else: ?>


                                            <span class="text-muted">

                                                Not Assigned

                                            </span>


                                        <?php endif; ?>


                                    </td>


                                    <td>

                                        <?php
                                        echo htmlspecialchars(
                                            $student['gender']
                                        );
                                        ?>

                                    </td>


                                    <td>


                                        <?php if ($student['status'] === 'Active'): ?>


                                            <span class="badge bg-success">

                                                Active

                                            </span>


                                        <?php else: ?>


                                            <span class="badge bg-secondary">

                                                Inactive

                                            </span>


                                        <?php endif; ?>


                                    </td>


                                    <td>


                                        <a
                                            href="view-student.php?id=<?php echo $student['id']; ?>"
                                            class="btn btn-info btn-sm mb-1"
                                        >
                                            View
                                        </a>


                                        <a
                                            href="edit-student.php?id=<?php echo $student['id']; ?>"
                                            class="btn btn-warning btn-sm mb-1"
                                        >
                                            Edit
                                        </a>


                                        <a
                                            href="delete-student.php?id=<?php echo $student['id']; ?>"
                                            class="btn btn-danger btn-sm mb-1"
                                            onclick="return confirm('Are you sure you want to delete this student?');"
                                        >
                                            Delete
                                        </a>


                                    </td>


                                </tr>


                            <?php endwhile; ?>


                        <?php else: ?>


                            <tr>


                                <td
                                    colspan="9"
                                    class="text-center py-5"
                                >


                                    <h5 class="text-warning">

                                        No Students Found

                                    </h5>


                                    <p class="text-muted">

                                        No students have been added yet.

                                    </p>


                                    <a
                                        href="add-student.php"
                                        class="btn btn-primary"
                                    >
                                        Add First Student
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


<!-- ===============================
     JAVASCRIPT
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
