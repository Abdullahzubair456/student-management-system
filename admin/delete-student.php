```php
<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/database.php';


// Check student ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: students.php");
    exit;
}

$id = intval($_GET['id']);


// Check if student exists
$check_sql = "SELECT id FROM students WHERE id = $id LIMIT 1";

$check_result = mysqli_query($conn, $check_sql);

if (!$check_result) {
    die("Query Failed: " . mysqli_error($conn));
}


// Student does not exist
if (mysqli_num_rows($check_result) == 0) {
    header("Location: students.php?error=not_found");
    exit;
}


// Delete student
$delete_sql = "DELETE FROM students WHERE id = $id";

if (mysqli_query($conn, $delete_sql)) {

    header("Location: students.php?deleted=1");
    exit;

} else {

    die("Delete Failed: " . mysqli_error($conn));
}

?>
```
