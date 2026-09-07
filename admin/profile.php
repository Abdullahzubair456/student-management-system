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


// Get admin information
$sql = "SELECT id, name, email, role
        FROM users
        WHERE id = ?
        LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$admin = mysqli_fetch_assoc($result);

if (!$admin) {
    die("Admin account not found.");
}


// Update name
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');

    if (empty($name)) {

        $error = "Name is required.";

    } else {

        $update_sql = "UPDATE users
                       SET name = ?
                       WHERE id = ?";

        $update_stmt = mysqli_prepare($conn, $update_sql);

        mysqli_stmt_bind_param(
            $update_stmt,
            "si",
            $name,
            $user_id
        );

        if (mysqli_stmt_execute($update_stmt)) {

            $_SESSION['name'] = $name;

            $admin['name'] = $name;

            $message = "Profile updated successfully.";

        } else {

            $error = "Failed to update profile.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Profile - Student Management System</title>

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

        /* ================= PROFILE CARD ================= */

        .profile-card {
            background: #5A1622;

            border: 1px solid #7A2230;

            border-radius: 12px;

            overflow: hidden;

            transition: 0.3s;
        }

        .profile-card:hover {
            background: #000000;
        }

        .profile-card-header {
            background: #3F0D16;

            padding: 18px 22px;

            border-bottom: 1px solid #7A2230;
        }

        .profile-card-header h5 {
            margin: 0;

            color: #D4AF7F;
        }

        .profile-card-body {
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

        .luxury-input::placeholder {
            color: #bbbbbb;
        }

        .readonly-input {
            opacity: 0.8;
            cursor: not-allowed;
        }

        /* ================= PROFILE INFO ================= */

        .profile-info-box {
            background: #3F0D16;

            border: 1px solid #7A2230;

            border-radius: 8px;

            padding: 13px 15px;

            color: #ffffff;
        }

        .profile-info-box span {
            color: #D4AF7F;
            font-weight: bold;
        }

        /* ================= FOOTER ================= */

        .profile-card-footer {
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
                margin-bottom: 8px;
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

        <a href="profile.php" class="active">
            👤 My Profile
        </a>

        <a href="change-password.php">
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
                My Profile
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
                        👤 Admin Profile
                    </h2>

                    <p>
                        Manage your account information.
                    </p>

                </div>


                <div class="header-buttons">

                    <a
                        href="dashboard.php"
                        class="btn-back"
                    >
                        ← Dashboard
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


        <!-- PROFILE CARD -->

        <div class="row">

            <div class="col-lg-8">

                <div class="profile-card">


                    <!-- CARD HEADER -->

                    <div class="profile-card-header">

                        <h5>
                            👤 Account Information
                        </h5>

                    </div>


                    <!-- CARD BODY -->

                    <div class="profile-card-body">


                        <div class="mb-4">

                            <label class="form-label">
                                User ID
                            </label>

                            <div class="profile-info-box">

                                <span>
                                    #
                                </span>

                                <?php
                                echo htmlspecialchars($admin['id']);
                                ?>

                            </div>

                        </div>


                        <div class="mb-4">

                            <label class="form-label">
                                Email
                            </label>

                            <div class="profile-info-box">

                                <?php
                                echo htmlspecialchars($admin['email']);
                                ?>

                            </div>

                        </div>


                        <form method="POST">


                            <div class="mb-4">

                                <label class="form-label">
                                    Name
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="luxury-input"
                                    value="<?php echo htmlspecialchars($admin['name']); ?>"
                                    required
                                >

                            </div>


                            <div class="mb-4">

                                <label class="form-label">
                                    Role
                                </label>

                                <div class="profile-info-box">

                                    <?php
                                    echo htmlspecialchars($admin['role']);
                                    ?>

                                </div>

                            </div>


                            <div>

                                <button
                                    type="submit"
                                    class="btn-luxury"
                                >
                                    💾 Update Profile
                                </button>


                                <a
                                    href="change-password.php"
                                    class="btn-back ms-2"
                                >
                                    🔐 Change Password
                                </a>

                            </div>


                        </form>


                    </div>


                    <!-- CARD FOOTER -->

                    <div class="profile-card-footer">

                        <a
                            href="dashboard.php"
                            class="btn-back"
                        >
                            ← Back to Dashboard
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
