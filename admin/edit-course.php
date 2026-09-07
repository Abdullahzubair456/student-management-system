<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/database.php';

$error = "";


// Check Course ID

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: courses.php");
    exit;
}

$id = intval($_GET['id']);


// Update Course

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $course_name = trim($_POST['course_name']);
    $course_code = trim($_POST['course_code']);


    if (empty($course_name) || empty($course_code)) {

        $error = "Please fill in all fields.";

    } else {

        // Check duplicate course code
        // Ignore the current course

        $check_sql = "SELECT id
                      FROM courses
                      WHERE course_code = ?
                      AND id != ?
                      LIMIT 1";

        $check_stmt = mysqli_prepare($conn, $check_sql);

        mysqli_stmt_bind_param(
            $check_stmt,
            "si",
            $course_code,
            $id
        );

        mysqli_stmt_execute($check_stmt);

        $check_result = mysqli_stmt_get_result($check_stmt);


        if (mysqli_num_rows($check_result) > 0) {

            $error = "This course code already exists.";

        } else {

            // Update

            $update_sql = "UPDATE courses
                           SET course_name = ?,
                               course_code = ?
                           WHERE id = ?";

            $stmt = mysqli_prepare($conn, $update_sql);

            mysqli_stmt_bind_param(
                $stmt,
                "ssi",
                $course_name,
                $course_code,
                $id
            );


            if (mysqli_stmt_execute($stmt)) {

                header("Location: view-course.php?id=" . $id . "&updated=1");
                exit;

            } else {

                $error = "Course update failed: " . mysqli_error($conn);

            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($check_stmt);
    }
}


// Get Current Course

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

    <title>Edit Course</title>

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

            <h2>Edit Course</h2>

            <p class="text-muted mb-0">
                Update course information.
            </p>

        </div>


        <a
            href="courses.php"
            class="btn btn-secondary"
        >
            ← Back to Courses
        </a>

    </div>


    <!-- Error -->

    <?php if (!empty($error)): ?>

        <div class="alert alert-danger">

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>


    <!-- Form -->

    <div class="card shadow-sm">


        <div class="card-header bg-dark text-white">

            <h5 class="mb-0">
                Course Information
            </h5>

        </div>


        <div class="card-body">


            <form method="POST">


                <div class="row g-4">


                    <!-- Course ID -->

                    <div class="col-md-6">

                        <label class="form-label fw-bold">
                            Course ID
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="<?php echo $course['id']; ?>"
                            readonly
                        >

                        <small class="text-muted">
                            Course ID cannot be changed.
                        </small>

                    </div>


                    <!-- Course Name -->

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
                            value="<?php echo htmlspecialchars($course['course_name']); ?>"
                            required
                        >

                    </div>


                    <!-- Course Code -->

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
                            value="<?php echo htmlspecialchars($course['course_code']); ?>"
                            required
                        >

                        <small class="text-muted">
                            Course code must be unique.
                        </small>

                    </div>


                </div>


                <!-- Buttons -->

                <div class="mt-4">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Update Course
                    </button>


                    <a
                        href="view-course.php?id=<?php echo $course['id']; ?>"
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

