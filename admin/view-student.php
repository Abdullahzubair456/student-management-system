
<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/database.php';


if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: students.php");
    exit;
}

$id = intval($_GET['id']);


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
        ON students.course_id = courses.id
        WHERE students.id = $id
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}


if (mysqli_num_rows($result) == 0) {
    die("Student not found.");
}

$student = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>View Student - <?php echo htmlspecialchars($student['student_id']); ?></title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>


<body class="bg-light">


<!-- Navbar -->

<nav class="navbar navbar-dark bg-dark">

    <div class="container">

        <a class="navbar-brand" href="dashboard.php">
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


    <!-- Page Header -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2>Student Details</h2>

            <p class="text-muted mb-0">
                View complete information about this student.
            </p>

        </div>


        <div>

            <a
                href="students.php"
                class="btn btn-secondary"
            >
                ← Back to Students
            </a>

            <a
                href="edit-student.php?id=<?php echo $student['id']; ?>"
                class="btn btn-warning"
            >
                Edit Student
            </a>

        </div>

    </div>


    <!-- Student Information Card -->

    <div class="card shadow-sm">

        <div class="card-header bg-dark text-white">

            <h5 class="mb-0">
                Student Information
            </h5>

        </div>


        <div class="card-body">


            <div class="row g-4">


                <!-- Student ID -->

                <div class="col-md-6">

                    <label class="form-label fw-bold">
                        Student ID
                    </label>

                    <div class="form-control bg-light">

                        <?php
                        echo htmlspecialchars($student['student_id']);
                        ?>

                    </div>

                </div>


                <!-- Full Name -->

                <div class="col-md-6">

                    <label class="form-label fw-bold">
                        Full Name
                    </label>

                    <div class="form-control bg-light">

                        <?php

                        echo htmlspecialchars(
                            $student['first_name'] . ' ' . $student['last_name']
                        );

                        ?>

                    </div>

                </div>


                <!-- First Name -->

                <div class="col-md-6">

                    <label class="form-label fw-bold">
                        First Name
                    </label>

                    <div class="form-control bg-light">

                        <?php
                        echo htmlspecialchars($student['first_name']);
                        ?>

                    </div>

                </div>


                <!-- Last Name -->

                <div class="col-md-6">

                    <label class="form-label fw-bold">
                        Last Name
                    </label>

                    <div class="form-control bg-light">

                        <?php
                        echo htmlspecialchars($student['last_name']);
                        ?>

                    </div>

                </div>


                <!-- Email -->

                <div class="col-md-6">

                    <label class="form-label fw-bold">
                        Email
                    </label>

                    <div class="form-control bg-light">

                        <?php
                        echo htmlspecialchars($student['email']);
                        ?>

                    </div>

                </div>


                <!-- Phone -->

                <div class="col-md-6">

                    <label class="form-label fw-bold">
                        Phone
                    </label>

                    <div class="form-control bg-light">

                        <?php
                        echo htmlspecialchars($student['phone']);
                        ?>

                    </div>

                </div>


                <!-- Gender -->

                <div class="col-md-6">

                    <label class="form-label fw-bold">
                        Gender
                    </label>

                    <div class="form-control bg-light">

                        <?php
                        echo htmlspecialchars($student['gender']);
                        ?>

                    </div>

                </div>


                <!-- Status -->

                <div class="col-md-6">

                    <label class="form-label fw-bold">
                        Status
                    </label>

                    <div class="form-control bg-light">

                        <?php if ($student['status'] === 'Active'): ?>

                            <span class="badge bg-success">
                                Active
                            </span>

                        <?php else: ?>

                            <span class="badge bg-secondary">
                                Inactive
                            </span>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- Course -->

                <div class="col-md-6">

                    <label class="form-label fw-bold">
                        Course
                    </label>

                    <div class="form-control bg-light">

                        <?php if ($student['course_name']): ?>

                            <?php
                            echo htmlspecialchars($student['course_name']);
                            ?>

                        <?php else: ?>

                            <span class="text-muted">
                                Not Assigned
                            </span>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- Course Code -->

                <div class="col-md-6">

                    <label class="form-label fw-bold">
                        Course Code
                    </label>

                    <div class="form-control bg-light">

                        <?php if ($student['course_code']): ?>

                            <?php
                            echo htmlspecialchars($student['course_code']);
                            ?>

                        <?php else: ?>

                            <span class="text-muted">
                                N/A
                            </span>

                        <?php endif; ?>

                    </div>

                </div>


            </div>


        </div>


        <!-- Card Footer -->

        <div class="card-footer bg-white">

            <a
                href="students.php"
                class="btn btn-secondary"
            >
                ← Back
            </a>

            <a
                href="edit-student.php?id=<?php echo $student['id']; ?>"
                class="btn btn-warning"
            >
                Edit Student
            </a>

        </div>


    </div>


</div>


</body>

</html>

