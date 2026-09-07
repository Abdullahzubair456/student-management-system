<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/database.php';

$message = "";
$message_type = "";

/*
|--------------------------------------------------------------------------
| Fetch Student Accounts
|--------------------------------------------------------------------------
| Only student accounts will appear in the dropdown.
*/

$users_sql = "SELECT id, name, email 
              FROM users 
              WHERE role = 'student'
              ORDER BY name ASC";

$users_result = $conn->query($users_sql);


/*
|--------------------------------------------------------------------------
| Fetch Courses
|--------------------------------------------------------------------------
*/

$courses_sql = "SELECT id, course_name, course_code 
                FROM courses 
                ORDER BY course_name ASC";

$courses_result = $conn->query($courses_sql);


/*
|--------------------------------------------------------------------------
| Form Submission
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = intval($_POST['user_id']);
    $student_id = trim($_POST['student_id']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $gender = trim($_POST['gender']);
    $date_of_birth = $_POST['date_of_birth'];
    $address = trim($_POST['address']);
    $course_id = intval($_POST['course_id']);
    $status = trim($_POST['status']);


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if (
        empty($user_id) ||
        empty($student_id) ||
        empty($first_name) ||
        empty($email) ||
        empty($course_id)
    ) {

        $message = "Student account, Student ID, First Name, Email and Course are required.";
        $message_type = "danger";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "danger";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Check Duplicate Student ID
        |--------------------------------------------------------------------------
        */

        $check = $conn->prepare(
            "SELECT id FROM students WHERE student_id = ?"
        );

        $check->bind_param("s", $student_id);
        $check->execute();

        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {

            $message = "This Student ID already exists.";
            $message_type = "danger";

        } else {

            /*
            |--------------------------------------------------------------------------
            | Check User Account Already Linked
            |--------------------------------------------------------------------------
            */

            $check_user = $conn->prepare(
                "SELECT id FROM students WHERE user_id = ?"
            );

            $check_user->bind_param("i", $user_id);
            $check_user->execute();

            $user_result = $check_user->get_result();

            if ($user_result->num_rows > 0) {

                $message = "This student account is already linked to a student profile.";
                $message_type = "danger";

            } else {

                /*
                |--------------------------------------------------------------------------
                | Insert Student
                |--------------------------------------------------------------------------
                */

                $stmt = $conn->prepare(
                    "INSERT INTO students
                    (
                        user_id,
                        student_id,
                        first_name,
                        last_name,
                        email,
                        phone,
                        gender,
                        date_of_birth,
                        address,
                        course_id,
                        status
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );

                $stmt->bind_param(
                    "isssssssiis",
                    $user_id,
                    $student_id,
                    $first_name,
                    $last_name,
                    $email,
                    $phone,
                    $gender,
                    $date_of_birth,
                    $address,
                    $course_id,
                    $status
                );


                if ($stmt->execute()) {

                    $message = "Student added successfully and account linked.";
                    $message_type = "success";

                    /*
                    |--------------------------------------------------------------------------
                    | Clear Form
                    |--------------------------------------------------------------------------
                    */

                    $_POST = [];

                } else {

                    $message = "Something went wrong. Please try again.";
                    $message_type = "danger";
                }

                $stmt->close();
            }

            $check_user->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Student - Student Management System</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            margin: 0;
            background: #0b0b0b;
            color: #ffffff;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: linear-gradient(
                180deg,
                #3F0D16,
                #25070d
            );
            border-right: 1px solid #7A2230;
            padding: 25px 15px;
        }

        .sidebar h3 {
            color: #D4AF7F;
            text-align: center;
            margin-bottom: 35px;
            font-weight: bold;
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
        }

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

        .topbar h4 {
            color: #D4AF7F;
            margin: 0;
        }

        .form-card {
            background: #5A1622;
            border: 1px solid #7A2230;
            border-radius: 14px;
            padding: 30px;
        }

        .form-label {
            color: #D4AF7F;
            font-weight: 600;
        }

        .form-control,
        .form-select {
            background: #3F0D16;
            color: #ffffff;
            border: 1px solid #7A2230;
        }

        .form-control:focus,
        .form-select:focus {
            background: #000000;
            color: #ffffff;
            border-color: #D4AF7F;
            box-shadow: none;
        }

        .form-control::placeholder {
            color: #c8aeb3;
        }

        .form-select option {
            background: #3F0D16;
            color: #ffffff;
        }

        .btn-maroon {
            background: #7A2230;
            color: #ffffff;
            border: 1px solid #8B2635;
            padding: 10px 20px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .btn-maroon:hover {
            background: #000000;
            color: #D4AF7F;
            border-color: #D4AF7F;
        }

        .btn-secondary-custom {
            background: #3F0D16;
            color: #ffffff;
            border: 1px solid #7A2230;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
        }

        .btn-secondary-custom:hover {
            background: #000000;
            color: #D4AF7F;
            border-color: #D4AF7F;
        }

        .alert {
            border-radius: 8px;
        }

        @media (max-width: 768px) {

            .sidebar {
                width: 220px;
            }

            .main-content {
                margin-left: 220px;
                padding: 20px;
            }

        }

    </style>

</head>

<body>


<!-- Sidebar -->

<div class="sidebar">

    <h3>SMS ADMIN</h3>

    <a href="dashboard.php">
        🏠 Dashboard
    </a>

    <a href="students.php" class="active">
        👨‍🎓 Students
    </a>

    <a href="courses.php">
        📚 Courses
    </a>

    <a href="profile.php">
        👤 My Profile
    </a>

    <a href="change-password.php">
        🔐 Change Password
    </a>

    <a href="../auth/logout.php">
        🚪 Logout
    </a>

</div>


<!-- Main Content -->

<div class="main-content">


    <!-- Topbar -->

    <div class="topbar">

        <h4>
            Add New Student
        </h4>

    </div>


    <!-- Message -->

    <?php if (!empty($message)): ?>

        <div class="alert alert-<?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <!-- Form -->

    <div class="form-card">

        <form method="POST">


            <!-- Student Account -->

            <div class="mb-4">

                <label class="form-label">
                    Student Account *
                </label>

                <select
                    name="user_id"
                    class="form-select"
                    required
                >

                    <option value="">
                        Select Student Account
                    </option>

                    <?php if ($users_result && $users_result->num_rows > 0): ?>

                        <?php while ($user = $users_result->fetch_assoc()): ?>

                            <option
                                value="<?php echo $user['id']; ?>"
                                <?php
                                if (
                                    isset($_POST['user_id']) &&
                                    $_POST['user_id'] == $user['id']
                                ) {
                                    echo 'selected';
                                }
                                ?>
                            >

                                <?php echo htmlspecialchars($user['name']); ?>
                                -
                                <?php echo htmlspecialchars($user['email']); ?>

                            </option>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <option value="">
                            No student accounts available
                        </option>

                    <?php endif; ?>

                </select>

                <small class="text-light">
                    Select the login account that belongs to this student.
                </small>

            </div>


            <div class="row">


                <!-- Student ID -->

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Student ID *
                    </label>

                    <input
                        type="text"
                        name="student_id"
                        class="form-control"
                        placeholder="e.g. STU-006"
                        value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>"
                        required
                    >

                </div>


                <!-- First Name -->

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        First Name *
                    </label>

                    <input
                        type="text"
                        name="first_name"
                        class="form-control"
                        placeholder="Enter first name"
                        value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                        required
                    >

                </div>


                <!-- Last Name -->

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Last Name
                    </label>

                    <input
                        type="text"
                        name="last_name"
                        class="form-control"
                        placeholder="Enter last name"
                        value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                    >

                </div>


                <!-- Email -->

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Email *
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="Enter student email"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        required
                    >

                </div>


                <!-- Phone -->

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Phone
                    </label>

                    <input
                        type="text"
                        name="phone"
                        class="form-control"
                        placeholder="Enter phone number"
                        value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                    >

                </div>


                <!-- Gender -->

                <div class="col-md-6 mb-3">

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
                            <?php echo (($_POST['gender'] ?? '') == 'Male') ? 'selected' : ''; ?>
                        >
                            Male
                        </option>

                        <option
                            value="Female"
                            <?php echo (($_POST['gender'] ?? '') == 'Female') ? 'selected' : ''; ?>
                        >
                            Female
                        </option>

                    </select>

                </div>


                <!-- Date of Birth -->

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Date of Birth
                    </label>

                    <input
                        type="date"
                        name="date_of_birth"
                        class="form-control"
                        value="<?php echo htmlspecialchars($_POST['date_of_birth'] ?? ''); ?>"
                    >

                </div>


                <!-- Course -->

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Course *
                    </label>

                    <select
                        name="course_id"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Select Course
                        </option>

                        <?php if ($courses_result && $courses_result->num_rows > 0): ?>

                            <?php while ($course = $courses_result->fetch_assoc()): ?>

                                <option
                                    value="<?php echo $course['id']; ?>"
                                    <?php
                                    if (
                                        isset($_POST['course_id']) &&
                                        $_POST['course_id'] == $course['id']
                                    ) {
                                        echo 'selected';
                                    }
                                    ?>
                                >

                                    <?php echo htmlspecialchars($course['course_name']); ?>

                                    <?php if (!empty($course['course_code'])): ?>

                                        -
                                        <?php echo htmlspecialchars($course['course_code']); ?>

                                    <?php endif; ?>

                                </option>

                            <?php endwhile; ?>

                        <?php endif; ?>

                    </select>

                </div>


                <!-- Address -->

                <div class="col-12 mb-3">

                    <label class="form-label">
                        Address
                    </label>

                    <textarea
                        name="address"
                        class="form-control"
                        rows="3"
                        placeholder="Enter student address"
                    ><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>

                </div>


                <!-- Status -->

                <div class="col-md-6 mb-4">

                    <label class="form-label">
                        Status
                    </label>

                    <select
                        name="status"
                        class="form-select"
                    >

                        <option
                            value="Active"
                            <?php echo (($_POST['status'] ?? 'Active') == 'Active') ? 'selected' : ''; ?>
                        >
                            Active
                        </option>

                        <option
                            value="Inactive"
                            <?php echo (($_POST['status'] ?? '') == 'Inactive') ? 'selected' : ''; ?>
                        >
                            Inactive
                        </option>

                    </select>

                </div>

            </div>


            <!-- Buttons -->

            <div class="mt-2">

                <button
                    type="submit"
                    class="btn btn-maroon me-2"
                >
                    ➕ Add Student
                </button>

                <a
                    href="students.php"
                    class="btn-secondary-custom"
                >
                    ← Back to Students
                </a>

            </div>


        </form>

    </div>

</div>


</body>

</html>