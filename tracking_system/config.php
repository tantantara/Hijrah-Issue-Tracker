<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['HTTP_HOST'] == 'localhost') {
    // Setting for laptop (XAMPP)
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "tracking_system";
}

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>