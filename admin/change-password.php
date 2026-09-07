```php
<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/database.php';

$user_id = $_SESSION['user_id'];

$message = '';
$error = '';


// Change Password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';


    // Check empty fields
    if (
        empty($current_password) ||
        empty($new_password) ||
        empty($confirm_password)
    ) {

        $error = "All fields are required.";

    } elseif ($new_password !== $confirm_password) {

        $error = "New password and confirm password do not match.";

    } elseif (strlen($new_password) < 6) {

        $error = "New password must be at least 6 characters.";

    } else {

        // Get current password
        $sql = "SELECT password
                FROM users
                WHERE id = ?
                LIMIT 1";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "i", $user_id);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $user = mysqli_fetch_assoc($result);


        if (!$user) {

            $error = "User account not found.";

        } elseif (!password_verify($current_password, $user['password'])) {

            $error = "Current password is incorrect.";

        } else {

            // Hash new password
            $hashed_password = password_hash(
                $new_password,
                PASSWORD_DEFAULT
            );


            // Update password
            $update_sql = "UPDATE users
                           SET password = ?
                           WHERE id = ?";

            $update_stmt = mysqli_prepare(
                $conn,
                $update_sql
            );

            mysqli_stmt_bind_param(
                $update_stmt,
                "si",
                $hashed_password,
                $user_id
            );


            if (mysqli_stmt_execute($update_stmt)) {

                $message = "Password changed successfully.";

            } else {

                $error = "Failed to change password.";
            }
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

    <title>Change Password - Student Management System</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #0b0b0b;
            color: #ffffff;
            font-family: Arial, sans-serif;
        }

        /* ================= SIDEBAR ================= */

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

            display: flex;
            flex-direction: column;

            z-index: 1000;
        }

        .sidebar-brand {
            padding: 25px 20px;

            font-size: 21px;
            font-weight: bold;

            color: #D4AF7F;

            text-align: center;

            border-bottom: 1px solid #7A2230;
        }

        .sidebar-menu {
            padding: 20px 12px;
        }

        .sidebar-menu a,
        .sidebar-bottom a {
            display: block;

            padding: 13px 16px;

            margin-bottom: 8px;

            color: #ffffff;
            text-decoration: none;

            border-radius: 7px;

            transition: 0.3s;
        }

        .sidebar-menu a:hover,
        .sidebar-bottom a:hover {
            background: #000000;
            color: #D4AF7F;
        }

        .sidebar-menu a.active {
            background: #5A1622;
            color: #D4AF7F;

            border-left: 4px solid #D4AF7F;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding: 15px 12px;

            border-top: 1px solid #7A2230;
        }

        /* ================= MAIN CONTENT ================= */

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }

        /* ================= TOPBAR ================= */

        .topbar {
            height: 70px;

            background: #0b0b0b;

            border-bottom: 1px solid #3F0D16;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 30px;
        }

        .topbar-title {
            color: #D4AF7F;
            font-size: 20px;
            font-weight: bold;
        }

        .admin-name {
            color: #ffffff;
        }

        .menu-toggle {
            display: none;

            background: #5A1622;
            color: #ffffff;

            border: 1px solid #7A2230;

            padding: 7px 12px;

            border-radius: 6px;

            font-size: 20px;
        }

        /* ================= PAGE ================= */

        .page-content {
            padding: 30px;
        }

        .page-header {
            background: #5A1622;

            padding: 25px;

            border-radius: 12px;

            border: 1px solid #7A2230;

            margin-bottom: 25px;

            transition: 0.3s;
        }

        .page-header:hover {
            background: #000000;
        }

        .page-header h2 {
            color: #D4AF7F;
            margin-bottom: 5px;
        }

        .page-header p {
            color: #dddddd;
            margin-bottom: 0;
        }

        /* ================= BUTTONS ================= */

        .btn-luxury {
            display: inline-block;

            background: #7A2230;

            color: #ffffff;

            border: 1px solid #8B2635;

            padding: 10px 18px;

            border-radius: 7px;

            text-decoration: none;

            transition: 0.3s;
        }

        .btn-luxury:hover {
            background: #000000;
            color: #D4AF7F;
            border-color: #D4AF7F;
        }

        .btn-back {
            display: inline-block;

            background: #3F0D16;

            color: #ffffff;

            border: 1px solid #7A2230;

            padding: 10px 18px;

            border-radius: 7px;

            text-decoration: none;

            transition: 0.3s;
        }

        .btn-back:hover {
            background: #000000;
            color: #D4AF7F;
        }

        /* ================= ALERTS ================= */

        .alert-success-luxury {
            background: #3F0D16;

            color: #D4AF7F;

            border: 1px solid #7A2230;

            border-radius: 8px;

            padding: 14px 18px;

            margin-bottom: 20px;
        }

        .alert-error-luxury {
            background: #3F0D16;

            color: #ffb3b3;

            border: 1px solid #8B2635;

            border-radius: 8px;

            padding: 14px 18px;

            margin-bottom: 20px;
        }

        /* ================= PASSWORD CARD ================= */

        .password-card {
            background: #5A1622;

            border: 1px solid #7A2230;

            border-radius: 12px;

            overflow: hidden;

            transition: 0.3s;
        }

        .password-card:hover {
            background: #000000;
        }

        .password-card-header {
            background: #3F0D16;

            padding: 18px 22px;

            border-bottom: 1px solid #7A2230;
        }

        .password-card-header h5 {
            margin: 0;

            color: #D4AF7F;
        }

        .password-card-body {
            padding: 25px;
        }

        /* ================= FORM ================= */

        .form-label {
            color: #D4AF7F;
            font-weight: bold;

            margin-bottom: 8px;
        }

        .luxury-input {
            width: 100%;

            background: #3F0D16 !important;

            color: #ffffff !important;

            border: 1px solid #7A2230 !important;

            border-radius: 7px;

            padding: 12px 14px;
        }

        .luxury-input:focus {
            background: #000000 !important;

            color: #ffffff !important;

            border-color: #D4AF7F !important;

            box-shadow: 0 0 0 0.15rem rgba(212, 175, 127, 0.15);
        }

        /* ================= PASSWORD INFO ================= */

        .password-info {
            background: #3F0D16;

            border: 1px solid #7A2230;

            border-radius: 8px;

            padding: 15px;

            margin-top: 20px;

            color: #dddddd;
        }

        .password-info strong {
            color: #D4AF7F;
        }

        /* ================= FOOTER ================= */

        .password-card-footer {
            background: #3F0D16;

            border-top: 1px solid #7A2230;

            padding: 18px 25px;
        }

        /* ================= MOBILE ================= */

        @media (max-width: 768px) {

            .sidebar {
                left: -250px;
                transition: 0.3s;
            }

            .sidebar.show {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: inline-block;
            }

            .topbar {
                padding: 0 15px;
            }

            .admin-name {
                font-size: 14px;
            }

            .page-content {
                padding: 20px 15px;
            }

            .page-header {
                padding: 20px;
            }

            .header-buttons {
                margin-top: 15px;
            }

            .header-buttons a {
                display: block;
                text-align: center;
            }

        }

    </style>

</head>


<body>


<!-- ================= SIDEBAR ================= -->

<aside class="sidebar" id="sidebar">

    <div class="sidebar-brand">
        Student Management
    </div>


    <div class="sidebar-menu">

        <a href="dashboard.php">
            🏠 Dashboard
        </a>

        <a href="students.php">
            👨‍🎓 Students
        </a>

        <a href="courses.php">
            📚 Courses
        </a>

        <a href="profile.php">
            👤 My Profile
        </a>

        <a href="change-password.php" class="active">
            🔐 Change Password
        </a>

    </div>


    <div class="sidebar-bottom">

        <a href="../auth/logout.php">
            🚪 Logout
        </a>

    </div>

</aside>


<!-- ================= MAIN CONTENT ================= -->

<div class="main-content">


    <!-- TOPBAR -->

    <div class="topbar">

        <div>

            <button
                class="menu-toggle"
                onclick="toggleSidebar()"
            >
                ☰
            </button>

            <span class="topbar-title ms-2">
                Change Password
            </span>

        </div>


        <div class="admin-name">

            Admin:
            <?php echo htmlspecialchars($_SESSION['name']); ?>

        </div>

    </div>


    <!-- PAGE CONTENT -->

    <div class="page-content">


        <!-- PAGE HEADER -->

        <div class="page-header">

            <div
                class="d-flex justify-content-between align-items-center flex-wrap"
            >

                <div>

                    <h2>
                        🔐 Change Password
                    </h2>

                    <p>
                        Update your admin account password securely.
                    </p>

                </div>


                <div class="header-buttons">

                    <a
                        href="profile.php"
                        class="btn-back"
                    >
                        ← Profile
                    </a>

                </div>

            </div>

        </div>


        <!-- SUCCESS MESSAGE -->

        <?php if (!empty($message)): ?>

            <div class="alert-success-luxury">

                ✅
                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>


        <!-- ERROR MESSAGE -->

        <?php if (!empty($error)): ?>

            <div class="alert-error-luxury">

                ⚠️
                <?php echo htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <!-- PASSWORD CARD -->

        <div class="row">

            <div class="col-lg-7 col-md-9">

                <div class="password-card">


                    <!-- CARD HEADER -->

                    <div class="password-card-header">

                        <h5>
                            🔐 Update Password
                        </h5>

                    </div>


                    <!-- CARD BODY -->

                    <div class="password-card-body">

                        <form method="POST">


                            <div class="mb-4">

                                <label class="form-label">
                                    Current Password
                                </label>

                                <input
                                    type="password"
                                    name="current_password"
                                    class="luxury-input"
                                    required
                                >

                            </div>


                            <div class="mb-4">

                                <label class="form-label">
                                    New Password
                                </label>

                                <input
                                    type="password"
                                    name="new_password"
                                    class="luxury-input"
                                    minlength="6"
                                    required
                                >

                            </div>


                            <div class="mb-4">

                                <label class="form-label">
                                    Confirm New Password
                                </label>

                                <input
                                    type="password"
                                    name="confirm_password"
                                    class="luxury-input"
                                    minlength="6"
                                    required
                                >

                            </div>


                            <button
                                type="submit"
                                class="btn-luxury"
                            >
                                🔐 Change Password
                            </button>


                            <a
                                href="profile.php"
                                class="btn-back ms-2"
                            >
                                Cancel
                            </a>


                        </form>


                        <div class="password-info">

                            <strong>💡 Password Requirement</strong>

                            <br>

                            Your new password must contain at least
                            <strong>6 characters</strong>.

                        </div>


                    </div>


                    <!-- CARD FOOTER -->

                    <div class="password-card-footer">

                        <a
                            href="profile.php"
                            class="btn-back"
                        >
                            ← Back to Profile
                        </a>

                    </div>


                </div>

            </div>

        </div>


    </div>

</div>


<script>

function toggleSidebar() {

    const sidebar = document.getElementById("sidebar");

    sidebar.classList.toggle("show");

}

</script>


</body>

</html>
```
