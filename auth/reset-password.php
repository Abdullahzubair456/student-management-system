<?php

include '../config/database.php';

$message = '';
$error = '';

$token = $_GET['token'] ?? '';

if (empty($token)) {
    $error = "Invalid or missing reset token.";
}

$user_id = null;


/* Check token */
if (!empty($token)) {

    // Current PHP time
    $current_time = date('Y-m-d H:i:s');

    $sql = "SELECT user_id
            FROM password_resets
            WHERE token = ?
            AND expires_at > ?
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die("Token Check Failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ss",
        $token,
        $current_time
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {

        $reset = mysqli_fetch_assoc($result);

        $user_id = $reset['user_id'];

    } else {

        $error = "This reset link is invalid or has expired.";
    }

    mysqli_stmt_close($stmt);
}


/* Reset password */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id !== null) {

    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($new_password) || empty($confirm_password)) {

        $error = "Both password fields are required.";

    } elseif (strlen($new_password) < 6) {

        $error = "Password must be at least 6 characters.";

    } elseif ($new_password !== $confirm_password) {

        $error = "Passwords do not match.";

    } else {

        /* Hash new password */
        $hashed_password = password_hash(
            $new_password,
            PASSWORD_DEFAULT
        );


        /* Update password */
        $update_sql = "UPDATE users
                       SET password = ?
                       WHERE id = ?";

        $update_stmt = mysqli_prepare(
            $conn,
            $update_sql
        );

        if (!$update_stmt) {
            die("Password Update Failed: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param(
            $update_stmt,
            "si",
            $hashed_password,
            $user_id
        );

        if (mysqli_stmt_execute($update_stmt)) {

            /* Delete used token */
            $delete_sql = "DELETE FROM password_resets
                           WHERE token = ?";

            $delete_stmt = mysqli_prepare(
                $conn,
                $delete_sql
            );

            if ($delete_stmt) {

                mysqli_stmt_bind_param(
                    $delete_stmt,
                    "s",
                    $token
                );

                mysqli_stmt_execute($delete_stmt);

                mysqli_stmt_close($delete_stmt);
            }


            $message = "Your password has been reset successfully.";

            $user_id = null;

        } else {

            $error = "Something went wrong. Please try again.";
        }

        mysqli_stmt_close($update_stmt);
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

    <title>Reset Password - Student Management System</title>

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
                        Reset Password
                    </h4>

                </div>


                <div class="card-body">


                    <?php if (!empty($message)): ?>

                        <div class="alert alert-success">

                            <?php echo htmlspecialchars($message); ?>

                        </div>


                        <div class="text-center">

                            <a
                                href="login.php"
                                class="btn btn-primary"
                            >
                                Go to Login
                            </a>

                        </div>


                    <?php elseif (!empty($error)): ?>

                        <div class="alert alert-danger">

                            <?php echo htmlspecialchars($error); ?>

                        </div>


                        <div class="text-center">

                            <a href="forgot-password.php">
                                Request a new reset link
                            </a>

                        </div>


                    <?php else: ?>


                        <p class="text-muted">
                            Enter your new password below.
                        </p>


                        <form method="POST">


                            <div class="mb-3">

                                <label class="form-label">
                                    New Password
                                </label>

                                <input
                                    type="password"
                                    name="new_password"
                                    class="form-control"
                                    placeholder="Enter new password"
                                    minlength="6"
                                    required
                                >

                            </div>


                            <div class="mb-4">

                                <label class="form-label">
                                    Confirm Password
                                </label>

                                <input
                                    type="password"
                                    name="confirm_password"
                                    class="form-control"
                                    placeholder="Confirm new password"
                                    minlength="6"
                                    required
                                >

                            </div>


                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                            >
                                Reset Password
                            </button>


                        </form>


                    <?php endif; ?>


                </div>

            </div>

        </div>

    </div>

</div>


</body>

</html>
