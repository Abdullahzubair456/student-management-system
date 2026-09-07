<?php

include '../config/database.php';

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check empty fields
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {

        $message = "All fields are required.";
        $message_type = "danger";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "danger";

    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";
        $message_type = "danger";

    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";
        $message_type = "danger";

    } else {

        // Check if email already exists
        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "This email is already registered.";
            $message_type = "danger";

        } else {

            // Hash password
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Insert user
            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password, role)
                 VALUES (?, ?, ?, 'student')"
            );

            $stmt->bind_param(
                "sss",
                $name,
                $email,
                $hashed_password
            );

            if ($stmt->execute()) {

                $message = "Registration successful! You can now login.";
                $message_type = "success";

            } else {

                $message = "Something went wrong. Please try again.";
                $message_type = "danger";
            }

            $stmt->close();
        }

        $check->close();
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

    <title>Register - Student Management System</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body class="bg-light">

    <div class="container">

        <div class="row justify-content-center align-items-center min-vh-100">

            <div class="col-md-6 col-lg-5">

                <div class="card shadow border-0">

                    <div class="card-body p-4 p-md-5">

                        <div class="text-center mb-4">

                            <h2 class="fw-bold">
                                Create Account
                            </h2>

                            <p class="text-muted">
                                Register as a student
                            </p>

                        </div>


                        <?php if (!empty($message)): ?>

                            <div class="alert alert-<?php echo $message_type; ?>">
                                <?php echo htmlspecialchars($message); ?>
                            </div>

                        <?php endif; ?>


                        <form method="POST">

                            <!-- Name -->
                            <div class="mb-3">

                                <label class="form-label">
                                    Full Name
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    placeholder="Enter your full name"
                                    required
                                >

                            </div>


                            <!-- Email -->
                            <div class="mb-3">

                                <label class="form-label">
                                    Email Address
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    placeholder="Enter your email"
                                    required
                                >

                            </div>


                            <!-- Password -->
                            <div class="mb-3">

                                <label class="form-label">
                                    Password
                                </label>

                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="Enter password"
                                    required
                                >

                            </div>


                            <!-- Confirm Password -->
                            <div class="mb-4">

                                <label class="form-label">
                                    Confirm Password
                                </label>

                                <input
                                    type="password"
                                    name="confirm_password"
                                    class="form-control"
                                    placeholder="Confirm password"
                                    required
                                >

                            </div>


                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                            >
                                Create Account
                            </button>

                        </form>


                        <div class="text-center mt-4">

                            <p class="mb-0">

                                Already have an account?

                                <a href="login.php">
                                    Login
                                </a>

                            </p>

                        </div>


                        <div class="text-center mt-3">

                            <a href="../index.php"
                               class="text-decoration-none">

                                ← Back to Home

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</body>

</html>