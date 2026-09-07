<?php

session_start();

include '../config/database.php';

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {

        $message = "Email and password are required.";
        $message_type = "danger";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email, password, role
             FROM users
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                // Create session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                // Redirect according to role
                if ($user['role'] === 'admin') {

                    header("Location: ../admin/dashboard.php");
                    exit;

                } else {

                    header("Location: ../student/dashboard.php");
                    exit;
                }

            } else {

                $message = "Invalid email or password.";
                $message_type = "danger";
            }

        } else {

            $message = "Invalid email or password.";
            $message_type = "danger";
        }

        $stmt->close();
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

    <title>Login - Student Management System</title>

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
                                Welcome Back
                            </h2>

                            <p class="text-muted">
                                Login to your account
                            </p>

                        </div>


                        <?php if (!empty($message)): ?>

                            <div class="alert alert-<?php echo $message_type; ?>">
                                <?php echo htmlspecialchars($message); ?>
                            </div>

                        <?php endif; ?>


                        <form method="POST">

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
                            <div class="mb-4">

                                <label class="form-label">
                                    Password
                                </label>

                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="Enter your password"
                                    required
                                >

                        </div>
                            <div class="text-end mb-3">

                             <a href="forgot-password.php">
                               Forgot Password?
                             </a>

                        </div>


                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                            >
                                Login
                            </button>

                        </form>


                        <div class="text-center mt-4">

                            <p class="mb-0">

                                Don't have an account?

                                <a href="register.php">
                                    Register
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