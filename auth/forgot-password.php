<?php

include '../config/database.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {

        $error = "Email is required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } else {

        $sql = "SELECT id
                FROM users
                WHERE email = ?
                LIMIT 1";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $email);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 1) {

            $user = mysqli_fetch_assoc($result);

            $user_id = $user['id'];

            // Generate secure reset token
            $token = bin2hex(random_bytes(32));

            // Token expires after 30 minutes
           $expires_at = date(
    'Y-m-d H:i:s',
    time() + (30 * 60)
);

            // Remove old tokens for this user
            $delete_sql = "DELETE FROM password_resets
                           WHERE user_id = ?";

           $delete_stmt = mysqli_prepare($conn, $delete_sql);

if (!$delete_stmt) {
    die("Delete Token Prepare Failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($delete_stmt, "i", $user_id);

mysqli_stmt_execute($delete_stmt);


            // Save new token
            $insert_sql = "INSERT INTO password_resets
                           (user_id, token, expires_at)
                           VALUES (?, ?, ?)";

            $insert_stmt = mysqli_prepare(
                $conn,
                $insert_sql
            );

            mysqli_stmt_bind_param(
                $insert_stmt,
                "iss",
                $user_id,
                $token,
                $expires_at
            );

            mysqli_stmt_execute($insert_stmt);

            $message = "If this email exists, a password reset request has been created.";
            $reset_link = "reset-password.php?token=" . urlencode($token);

echo "<p>DEBUG TOKEN: " . htmlspecialchars($token) . "</p>";

        } else {

            // Same message to avoid revealing whether an account exists
            $message = "If this email exists, a password reset request has been created.";
        }
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

    <title>Forgot Password</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>


<body class="bg-light">


<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-5">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-dark text-white">

                    <h4 class="mb-0">
                        Forgot Password
                    </h4>

                </div>


                <div class="card-body">

                    <p class="text-muted">
                        Enter your registered email address
                        to request a password reset.
                    </p>


                   <?php if (!empty($message)): ?>

    <div class="alert alert-success">

        <?php echo htmlspecialchars($message); ?>

    </div>

<?php endif; ?>


<?php if (!empty($reset_link)): ?>

    <div class="alert alert-warning">

        <strong>Development Testing:</strong>

        <p class="mb-2">
            Use the following link to test password reset:
        </p>

        <a
            href="<?php echo htmlspecialchars($reset_link); ?>"
            class="btn btn-warning btn-sm"
        >
            Open Reset Password
        </a>

    </div>

<?php endif; ?>


                    <?php if (!empty($error)): ?>

                        <div class="alert alert-danger">

                            <?php echo htmlspecialchars($error); ?>

                        </div>

                    <?php endif; ?>


                    <form method="POST">

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


                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Request Password Reset
                        </button>

                    </form>


                    <div class="text-center mt-3">

                        <a href="login.php">
                            ← Back to Login
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>