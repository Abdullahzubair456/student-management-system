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

$message = "";
$message_type = "";

$user_id = $_SESSION['user_id'];


/*
|--------------------------------------------------------------------------
| Update Profile
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $gender = trim($_POST['gender']);
    $date_of_birth = $_POST['date_of_birth'];
    $address = trim($_POST['address']);

    if (empty($first_name)) {

        $message = "First name is required.";
        $message_type = "danger";

    } else {

        $stmt = $conn->prepare(
            "UPDATE students
             SET first_name = ?,
                 last_name = ?,
                 phone = ?,
                 gender = ?,
                 date_of_birth = ?,
                 address = ?
             WHERE user_id = ?"
        );

        $stmt->bind_param(
            "ssssssi",
            $first_name,
            $last_name,
            $phone,
            $gender,
            $date_of_birth,
            $address,
            $user_id
        );

        if ($stmt->execute()) {

            $message = "Profile updated successfully!";
            $message_type = "success";

        } else {

            $message = "Something went wrong: " . $stmt->error;
            $message_type = "danger";
        }

        $stmt->close();
    }
}


/*
|--------------------------------------------------------------------------
| Fetch Student Profile
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        s.id,
        s.user_id,
        s.student_id,
        s.first_name,
        s.last_name,
        s.email,
        s.phone,
        s.gender,
        s.date_of_birth,
        s.address,
        s.status,
        s.course_id,
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


/*
|--------------------------------------------------------------------------
| Student Not Found
|--------------------------------------------------------------------------
*/

if (!$student) {

    $message = "Your student profile was not found. Please contact the administrator.";
    $message_type = "danger";

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

    <title>My Profile - Student Management System</title>

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

        /* Main */

        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

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

        /* Cards */

        .profile-card {
            background: #5A1622;
            border: 1px solid #7A2230;

            border-radius: 15px;

            padding: 30px;

            transition: 0.3s;
        }

        .profile-card:hover {
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

        .form-label {
            color: #ffffff;
            font-weight: 500;
        }

        .form-control,
        .form-select,
        textarea {
            background: #25070d !important;
            color: #ffffff !important;

            border: 1px solid #7A2230 !important;
        }

        .form-control:focus,
        .form-select:focus,
        textarea:focus {
            border-color: #D4AF7F !important;

            box-shadow: 0 0 0 0.2rem rgba(
                212,
                175,
                127,
                0.15
            ) !important;
        }

        .form-control::placeholder,
        textarea::placeholder {
            color: #aaaaaa;
        }

        .form-select option {
            background: #25070d;
            color: #ffffff;
        }

        .btn-maroon {
            background: #7A2230;
            color: #ffffff;
            border: 1px solid #8B2635;

            transition: 0.3s;
        }

        .btn-maroon:hover {
            background: #000000;
            color: #D4AF7F;
            border-color: #D4AF7F;
        }

        .info-box {
            background: #3F0D16;
            border: 1px solid #7A2230;

            border-radius: 12px;

            padding: 20px;
        }

        .info-label {
            color: #D4AF7F;
            font-size: 14px;
        }

        .info-value {
            color: #ffffff;
            font-weight: 500;
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


    <a href="profile.php" class="active">
        👤 My Profile
    </a>


    <a href="course.php">
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
                My Profile
            </h3>

            <small>
                View and update your personal information
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


    <!-- Message -->

    <?php if (!empty($message)): ?>

        <div class="alert alert-<?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <?php if ($student): ?>


    <!-- Profile Information -->

    <div class="profile-card mb-4">

        <h4 class="section-title">
            Personal Information
        </h4>


        <form method="POST">

            <div class="row g-4">


                <!-- Student ID -->

                <div class="col-md-6">

                    <label class="form-label">
                        Student ID
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="<?php echo htmlspecialchars($student['student_id']); ?>"
                        readonly
                    >

                </div>


                <!-- Email -->

                <div class="col-md-6">

                    <label class="form-label">
                        Email
                    </label>

                    <input
                        type="email"
                        class="form-control"
                        value="<?php echo htmlspecialchars($student['email']); ?>"
                        readonly
                    >

                </div>


                <!-- First Name -->

                <div class="col-md-6">

                    <label class="form-label">
                        First Name
                    </label>

                    <input
                        type="text"
                        name="first_name"
                        class="form-control"
                        value="<?php echo htmlspecialchars($student['first_name']); ?>"
                        required
                    >

                </div>


                <!-- Last Name -->

                <div class="col-md-6">

                    <label class="form-label">
                        Last Name
                    </label>

                    <input
                        type="text"
                        name="last_name"
                        class="form-control"
                        value="<?php echo htmlspecialchars($student['last_name']); ?>"
                    >

                </div>


                <!-- Phone -->

                <div class="col-md-6">

                    <label class="form-label">
                        Phone
                    </label>

                    <input
                        type="text"
                        name="phone"
                        class="form-control"
                        value="<?php echo htmlspecialchars($student['phone']); ?>"
                    >

                </div>


                <!-- Gender -->

                <div class="col-md-3">

                    <label class="form-label">
                        Gender
                    </label>

                    <select
                        name="gender"
                        class="form-select"
                    >

                        <option value="">
                            Select Gender
                        </option>

                        <option
                            value="Male"
                            <?php echo ($student['gender'] === 'Male') ? 'selected' : ''; ?>
                        >
                            Male
                        </option>

                        <option
                            value="Female"
                            <?php echo ($student['gender'] === 'Female') ? 'selected' : ''; ?>
                        >
                            Female
                        </option>

                    </select>

                </div>


                <!-- Date of Birth -->

                <div class="col-md-3">

                    <label class="form-label">
                        Date of Birth
                    </label>

                    <input
                        type="date"
                        name="date_of_birth"
                        class="form-control"
                        value="<?php echo htmlspecialchars($student['date_of_birth'] ?? ''); ?>"
                    >

                </div>


                <!-- Address -->

                <div class="col-12">

                    <label class="form-label">
                        Address
                    </label>

                    <textarea
                        name="address"
                        class="form-control"
                        rows="3"
                    ><?php echo htmlspecialchars($student['address']); ?></textarea>

                </div>


                <!-- Button -->

                <div class="col-12">

                    <button
                        type="submit"
                        class="btn btn-maroon px-4"
                    >
                        Save Changes
                    </button>

                </div>

            </div>

        </form>

    </div>


    <!-- Course & Status -->

    <div class="profile-card">

        <h4 class="section-title">
            Academic / Course Information
        </h4>


        <div class="row g-3">


            <div class="col-md-4">

                <div class="info-box">

                    <div class="info-label">
                        Course
                    </div>

                    <div class="info-value mt-1">

                        <?php
                        echo htmlspecialchars(
                            $student['course_name'] ?? 'Not Assigned'
                        );
                        ?>

                    </div>

                </div>

            </div>


            <div class="col-md-4">

                <div class="info-box">

                    <div class="info-label">
                        Course Code
                    </div>

                    <div class="info-value mt-1">

                        <?php
                        echo htmlspecialchars(
                            $student['course_code'] ?? 'N/A'
                        );
                        ?>

                    </div>

                </div>

            </div>


            <div class="col-md-4">

                <div class="info-box">

                    <div class="info-label">
                        Status
                    </div>

                    <div class="info-value mt-1">

                        <?php
                        echo htmlspecialchars($student['status']);
                        ?>

                    </div>

                </div>

            </div>


        </div>

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