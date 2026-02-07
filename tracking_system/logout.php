<?php
session_start();
session_unset(); // delete all variable session
session_destroy(); 

header("Location: index.php"); // returbn to login page
exit();
?>