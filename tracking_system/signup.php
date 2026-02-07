<?php 
include 'config.php'; 

if(isset($_POST['register'])){
    // 1. retrieve data from username
    $username = mysqli_real_escape_string($conn, $_POST['reg_user']);
    $email = mysqli_real_escape_string($conn, $_POST['reg_email']);
    $password = mysqli_real_escape_string($conn, $_POST['reg_pass']);

    // 2. check email if exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($check) > 0) {
        echo "<script>alert('This email has been registered!');</script>";
    } else {
        // 3. Insert username into db 
        $sql = "INSERT INTO users (username, email, password, role, status) 
                VALUES ('$username', '$email', '$password', 'engineer', 'pending')";
        
        if(mysqli_query($conn, $sql)) {
            echo "<script>alert('Registration successful! Please wait for admin approval.'); window.location='index.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up | GE Tracking</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; margin: 0; }
        .box { background: white; padding: 40px; width: 350px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-top: 5px solid #3d466a; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #3d466a; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; margin-top: 10px; }
        .login-link { text-align: center; margin-top: 20px; font-size: 14px; }
        .login-link a { color: #007bff; text-decoration: none; font-weight: bold; }
        .hijrah-logo { width: 70px; display: block; margin-left:-10px;}
    </style>
</head>
<body>

<div class="box">
    <img src="Hijrah-Inovatif-Logo.png" alt="Hijrah Inovatif" class="hijrah-logo">
    <h2 style="text-align: center; color: #2c3e50; margin-top: 5px; margin-bottom: auto;">Sign Up</h2>
    <p style="text-align: center; font-size: 13px; color: #787878;">Create an account to access the tracker.</p>
    
    <form method="POST">
        <label>Email Address:</label>
        <input type="email" name="reg_email" placeholder="example@gmail.com" required 
               pattern="[a-z0-0._%+-]+@gmail\.com$" title="Sila gunakan alamat Gmail sahaja">
        
        <label>Full Name / Username:</label>
<input type="text" name="reg_user" placeholder="Enter your name" required>

        <label>Create Password:</label>
        <input type="password" name="reg_pass" placeholder="••••••••" required>
        
        <button type="submit" name="register">Register Account</button>
    </form>

    <div class="login-link">
        Already have an account? <a href="index.php">Back to Login</a>
    </div>
</div>

</body>
</html>