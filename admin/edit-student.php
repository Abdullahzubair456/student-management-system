```php
<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/database.php';


// Check student ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: students.php");
    exit;
}

$id = intval($_GET['id']);


// Update student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $phone      = trim($_POST['phone']);
    $gender     = trim($_POST['gender']);
    $status     = trim($_POST['status']);
    $course_id  = intval($_POST['course_id']);


    $update_sql = "UPDATE students SET
                    first_name = ?,
                    last_name = ?,
                    email = ?,
                    phone = ?,
                    gender = ?,
                    status = ?,
                    course_id = ?
                   WHERE id = ?";


    $stmt = mysqli_prepare($conn, $update_sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ssssssii",
        $first_name,
        $last_name,
        $email,
        $phone,
        $gender,
        $status,
        $course_id,
        $id
    );


    if (mysqli_stmt_execute($stmt)) {

        header("Location: view-student.php?id=" . $id . "&updated=1");
        exit;

    } else {

        $error = "Student update failed: " . mysqli_error($conn);

    }

    mysqli_stmt_close($stmt);
}


// Get student information
$sql = "SELECT * FROM students WHERE id = $id LIMIT 1";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}


if (mysqli_num_rows($result) == 0) {
    die("Student not found.");
}


$student = mysqli_fetch_assoc($result);


// Get courses
$course_sql = "SELECT id, course_name, course_code
               FROM courses
               ORDER BY course_name ASC";

$course_result = mysqli_query($conn, $course_sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Student</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>


<body class="bg-light">


<!-- Navbar -->

<nav class="navbar navbar-dark bg-dark">

    <div class="container">

        <a
            class="navbar-brand"
            href="dashboard.php"
        >
            Student Management System
        </a>


        <div>

            <span class="text-white me-3">

                Admin:
                <?php echo htmlspecialchars($_SESSION['name']); ?>

            </span>


            <a
                href="../auth/logout.php"
                class="btn btn-danger btn-sm"
            >
                Logout
            </a>

        </div>

    </div>

</nav>


<!-- Main Content -->

<div class="container py-5">


    <!-- Header -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2>Edit Student</h2>

            <p class="text-muted mb-0">
                Update student information.
            </p>

        </div>


        <a
            href="students.php"
            class="btn btn-secondary"
        >
            ← Back to Students
        </a>

    </div>


    <!-- Error -->

    <?php if (isset($error)): ?>

        <div class="alert alert-danger">

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>


    <!-- Edit Form -->

    <div class="card shadow-sm">

        <div class="card-header bg-dark text-white">

            <h5 class="mb-0">
                Student Information
            </h5>

        </div>


        <div class="card-body">

            <form method="POST">


                <div class="row g-4">


                    <!-- Student ID -->

                    <div class="col-md-6">

                        <label class="form-label fw-bold">
                            Student ID
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="<?php echo htmlspecialchars($student['student_id']); ?>"
                            readonly
                        >

                        <small class="text-muted">
                            Student ID cannot be changed.
                        </small>

                    </div>


                    <!-- First Name -->

                    <div class="col-md-6">

                        <label class="form-label fw-bold">
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

                        <label class="form-label fw-bold">
                            Last Name
                        </label>

                        <input
                            type="text"
                            name="last_name"
                            class="form-control"
                            value="<?php echo htmlspecialchars($student['last_name']); ?>"
                            required
                        >

                    </div>


                    <!-- Email -->

                    <div class="col-md-6">

                        <label class="form-label fw-bold">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="<?php echo htmlspecialchars($student['email']); ?>"
                            required
                        >

                    </div>


                    <!-- Phone -->

                    <div class="col-md-6">

                        <label class="form-label fw-bold">
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

                    <div class="col-md-6">

                        <label class="form-label fw-bold">
                            Gender
                        </label>

                        <select
                            name="gender"
                            class="form-select"
                            required
                        >

                            <option value="Male"
                                <?php echo ($student['gender'] === 'Male') ? 'selected' : ''; ?>>
                                Male
                            </option>

                            <option value="Female"
                                <?php echo ($student['gender'] === 'Female') ? 'selected' : ''; ?>>
                                Female
                            </option>

                        </select>

                    </div>


                    <!-- Course -->

                    <div class="col-md-6">

                        <label class="form-label fw-bold">
                            Course
                        </label>

                        <select
                            name="course_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Select Course
                            </option>


                            <?php while ($course = mysqli_fetch_assoc($course_result)): ?>

                                <option
                                    value="<?php echo $course['id']; ?>"
                                    <?php
                                    echo ($student['course_id'] == $course['id'])
                                        ? 'selected'
                                        : '';
                                    ?>
                                >

                                    <?php
                                    echo htmlspecialchars($course['course_name']);
                                    ?>

                                    -

                                    <?php
                                    echo htmlspecialchars($course['course_code']);
                                    ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>


                    <!-- Status -->

                    <div class="col-md-6">

                        <label class="form-label fw-bold">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select"
                            required
                        >

                            <option
                                value="Active"
                                <?php echo ($student['status'] === 'Active') ? 'selected' : ''; ?>
                            >
                                Active
                            </option>

                            <option
                                value="Inactive"
                                <?php echo ($student['status'] === 'Inactive') ? 'selected' : ''; ?>
                            >
                                Inactive
                            </option>

                        </select>

                    </div>


                </div>


                <!-- Buttons -->

                <div class="mt-4">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Update Student
                    </button>


                    <a
                        href="view-student.php?id=<?php echo $student['id']; ?>"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </a>

                </div>


            </form>

        </div>

    </div>


</div>


</body>

</html>
```
