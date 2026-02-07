<?php 
session_start(); // Don't forget this at the very top!
include 'config.php'; 
$error_msg = "";

if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // 1. First, search only by Email to see if the account exists
    $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    
    if(mysqli_num_rows($res) > 0){
        $user = mysqli_fetch_assoc($res);
        
        // 2. Now, check if the password matches
        if($user['password'] == $password) { // Use password_verify() if you hashed them!
            
            // 3. Finally, check the account status/role
            if($user['role'] == 'admin' || $user['status'] == 'approved'){
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];

                if($user['role'] == 'admin') {
                    header("Location: admin_dash.php");
                    exit();
                } else {
                    header("Location: engineer_dash.php");
                    exit();
                }
            } else {
                // Account exists and password is correct, but not approved
                $error_msg = "Your account is still pending for approval.";
            }
        } else {
            // Email exists, but password was wrong
            $error_msg = "Incorrect password.";
        }
    } else {
        // Email does not exist in the database at all
        $error_msg = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Login</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; margin: 0; }
        .box { background: white; padding: 40px; width: 350px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #0ea442; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; margin-top: 10px; }
        .msg { color: #d63031; background: #fab1a0; padding: 10px; border-radius: 5px; font-size: 14px; text-align: center; }
        .signup-link { text-align: center; margin-top: 20px; font-size: 14px; }
        .signup-link a { color: #007bff; text-decoration: none; font-weight: bold; }
        .hijrah-logo { width: 70px; display: block; margin-left:0;}
    </style>
</head>
<body>

<div class="box">
    <img src="Hijrah-Inovatif-Logo.png" alt="Hijrah Inovatif" class="hijrah-logo">
    <h2 style="text-align: center; color: #2c3e50;">Login</h2>
    <?php if($error_msg != "") echo "<p class='msg'>$error_msg</p>"; ?>
    
    <form method="POST">
        <label>Email Address:</label>
        <input type="email" name="email" placeholder="example@gmail.com" required>
        <label>Password:</label>
        <input type="password" name="password" placeholder="••••••••" required>
        <button type="submit" name="login">Login</button>
    </form>

    <div class="signup-link">
        Don't have an account? <a href="signup.php">Sign Up Here</a>
    </div>
</div>

</body>
</html>
