<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/database.php';


// Get course ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: courses.php?error=invalid_id");
    exit;
}

$id = (int) $_GET['id'];


// Check if course exists
$check_sql = "SELECT id FROM courses WHERE id = ? LIMIT 1";

$check_stmt = mysqli_prepare($conn, $check_sql);

mysqli_stmt_bind_param($check_stmt, "i", $id);

mysqli_stmt_execute($check_stmt);

$check_result = mysqli_stmt_get_result($check_stmt);


if (mysqli_num_rows($check_result) === 0) {

    header("Location: courses.php?error=not_found");
    exit;
}


// Delete course
$delete_sql = "DELETE FROM courses WHERE id = ?";

$stmt = mysqli_prepare($conn, $delete_sql);

mysqli_stmt_bind_param($stmt, "i", $id);


if (mysqli_stmt_execute($stmt)) {

    header("Location: courses.php?deleted=1");
    exit;

} else {

    header("Location: courses.php?error=delete_failed");
    exit;
}