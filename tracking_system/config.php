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
} else {
    // Setting for online(InfinityFree)
    $host = "sql110.infinityfree.com";
    $user = "if0_40933375";
    $pass = "higetrack123"; 
    $db   = "if0_40933375_tracking_system";
}

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
