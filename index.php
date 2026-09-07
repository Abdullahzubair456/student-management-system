<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Management System</title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-dark navbar-dark">
        <div class="container">

            <a class="navbar-brand fw-bold" href="index.php">
                Student Management System
            </a>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            Home
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="auth/login.php">
                            Login
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="btn btn-primary ms-lg-2" href="auth/register.php">
                            Register
                        </a>
                    </li>

                </ul>

            </div>
        </div>
    </nav>


    <!-- Hero Section -->
    <section class="hero-section">

        <div class="container">

            <div class="row align-items-center min-vh-75">

                <div class="col-lg-7">

                    <h1 class="display-4 fw-bold">
                        Student Management System
                    </h1>

                    <p class="lead text-muted mt-3">
                        A simple and efficient platform to manage
                        students, courses and academic information.
                    </p>

                    <div class="mt-4">

                        <a href="auth/login.php"
                           class="btn btn-primary btn-lg me-2">
                            Login
                        </a>

                        <a href="auth/register.php"
                           class="btn btn-outline-dark btn-lg">
                            Create Account
                        </a>

                    </div>

                </div>

                <div class="col-lg-5 text-center mt-5 mt-lg-0">

                    <div class="hero-card shadow">
                        <h2>🎓</h2>

                        <h4 class="mt-3">
                            Manage Students Easily
                        </h4>

                        <p class="text-muted">
                            Manage student records, courses and
                            accounts from one place.
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- Features -->
    <section class="py-5 bg-light">

        <div class="container">

            <div class="text-center mb-5">

                <h2 class="fw-bold">
                    System Features
                </h2>

                <p class="text-muted">
                    Everything you need to manage student records.
                </p>

            </div>

            <div class="row g-4">

                <div class="col-md-4">

                    <div class="card feature-card h-100 shadow-sm">

                        <div class="card-body text-center p-4">

                            <div class="feature-icon">
                                👨‍🎓
                            </div>

                            <h4 class="mt-3">
                                Student Management
                            </h4>

                            <p class="text-muted">
                                Add, view, update and delete
                                student records easily.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="card feature-card h-100 shadow-sm">

                        <div class="card-body text-center p-4">

                            <div class="feature-icon">
                                📚
                            </div>

                            <h4 class="mt-3">
                                Course Management
                            </h4>

                            <p class="text-muted">
                                Manage courses and assign students
                                to their respective courses.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="card feature-card h-100 shadow-sm">

                        <div class="card-body text-center p-4">

                            <div class="feature-icon">
                                📊
                            </div>

                            <h4 class="mt-3">
                                Admin Dashboard
                            </h4>

                            <p class="text-muted">
                                View important student statistics
                                from a single dashboard.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4">

        <div class="container">

            <p class="mb-0">
                © 2026 Student Management System.
                All Rights Reserved.
            </p>

        </div>

    </footer>


    <!-- Bootstrap JS -->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>

</body>

</html>