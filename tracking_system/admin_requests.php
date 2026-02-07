<?php 
include 'config.php';

// 1. block access if not admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { 
    header("Location: index.php"); 
    exit(); 
}

// 2. Logic to approve
if(isset($_GET['approve'])) { 
    $id = intval($_GET['approve']); 
    mysqli_query($conn, "UPDATE users SET status='approved' WHERE id=$id"); 
    header("Location: admin_requests.php?msg=approved"); exit();
}

// 3. Logic to remove
if(isset($_GET['cancel'])) { 
    $id = intval($_GET['cancel']); 
    
    // not permenantly deleted, the data will renain in db
    // userr cannot login but their old data still remain
    mysqli_query($conn, "UPDATE users SET status='removed' WHERE id=$id"); 
    
    header("Location: admin_requests.php?msg=removed"); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Access | GE Tracking</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; background-color: #f0f2f5; transition: 0.5s; }
        
        /* SIDEBAR STYLE */
        .sidebar {
            height: 100%; width: 250px; position: fixed; z-index: 1000;
            top: 0; left: 0; background-color: #2c3e50; overflow-x: hidden;
            transition: 0.5s; padding-top: 20px;
        }
        .sidebar a {
            padding: 15px 25px; text-decoration: none; font-size: 17px;
            color: #bdc3c7; display: block; transition: 0.3s; border-bottom: 1px solid #34495e;
        }
        .sidebar a:hover { color: white; background-color: #34495e; padding-left: 35px; }
        .sidebar .closebtn { position: absolute; top: 10px; right: 25px; font-size: 36px; background: none; border: none; color: white; cursor: pointer; }
        .hijrahsidebar-logo { width: 100px; display: block; margin:auto;}
        .sidebar-logo { 
            width: 70px;            /* Your preferred size */
            display: block; 
            position: absolute;     /* Pins the logo relative to the sidebar */
            bottom: 30px;           /* Distance from the very bottom */
            left: 50%;              /* Moves it to the middle */
            transform: translateX(-50%); /* Perfectly centers it horizontally */
        }

        /* MAIN CONTENT STYLE */
        #main { transition: margin-left .5s; padding: 30px; margin-left: 250px; }
        .header-container { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; }
        .openbtn { 
            font-size: 18px; cursor: pointer; background-color: #2c3e50; color: white; 
            padding: 10px 15px; border: none; border-radius: 5px; position: fixed; 
            top: 15px; left: 15px; display: none; z-index: 1100; 
        }
        .main-header-logo { height: 50px; margin-right: 15px; vertical-align: middle; }

        /* CARD & TABLE STYLE */
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 30px; border: 1px solid #eee; }
        h3 { color: #2c3e50; margin-top: 0; border-bottom: 2px solid #f0f2f5; padding-bottom: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; color: #333; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }
        
        /* BUTTON STYLE */
        .btn-approve { color: #28a745; font-weight: bold; text-decoration: none; border: 1px solid #28a745; padding: 6px 12px; border-radius: 4px; font-size: 12px; transition: 0.3s; }
        .btn-approve:hover { background: #28a745; color: white; }
        .btn-reject { color: #dc3545; font-weight: bold; text-decoration: none; border: 1px solid #dc3545; padding: 6px 12px; border-radius: 4px; font-size: 12px; margin-left: 5px; transition: 0.3s; }
        .btn-reject:hover { background: #dc3545; color: white; }
        .btn-revoke { color: #bbb; text-decoration: none; font-size: 12px; font-weight: bold; }
        .btn-revoke:hover { color: #dc3545; text-decoration: underline; }

        .status-badge { background: #e1f7e7; color: #1e7e34; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
    </style>
</head>
<body>

<div id="mySidebar" class="sidebar">
    <button class="closebtn" onclick="closeNav()">×</button>
    <div style="padding: 10px 25px; text-align: center;">
        <img src="Hijrah-Inovatif-Logo.png" alt="Hijrah Inovatif" class="hijrahsidebar-logo">
        <h4 style="color: white; margin: 5px 0 0 0;">Issue Tracker</h4>
    </div>
    <hr style="border: 0.5px solid #34495e; margin: 20px 0;">
    <a href="admin_dash.php">Dashboard</a>
    <a href="admin_requests.php" style="color: white; background: #34495e;">Access Requests</a>
    <a href="admin_folders.php">Manage Folders</a>
    <a href="logout.php" style="color: #ff7675; margin-top: 50px;">Logout</a>
    <img src="GE-healthcare-logo_front.png" alt="GE Logo" class="sidebar-logo">
</div>

<button class="openbtn" id="openBtn" onclick="openNav()">☰ Menu</button>

<div id="main">
    <div class="header-container" id="headerContainer">
        <div style="display: flex; align-items: center;">
            <img src="Hijrah-Inovatif-Logo.png" alt="Hijrah Inovatif" class="main-header-logo">
            <h1 style="margin: 0; font-size: 24px;">Access Requests</h1>
        </div>
    </div>
    <hr style="border: 0; border-top: 1px solid #ddd; margin: 15px 0 25px 0;">

    <div class="card">
        <h3>Pending Requests</h3>
        <table>
            <thead>
                <tr>
                    <th>Engineer Details</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $pending = mysqli_query($conn, "SELECT * FROM users WHERE status='pending' AND role='engineer' ORDER BY id DESC");
                if(mysqli_num_rows($pending) > 0) {
                    while($u = mysqli_fetch_assoc($pending)){
                        echo "<tr>
                        <td>
                            <strong>{$u['username']}</strong><br>
                            <span style='color:#666; font-size:13px;'>{$u['email']}</span>
                        </td>
                        <td style='text-align: right;'>
                            <a href='?approve={$u['id']}' class='btn-approve'>Approve</a> 
                            <a href='?cancel={$u['id']}' class='btn-reject' onclick='return confirm(\"Reject this user?\")'>Reject</a>
                        </td>
                    </tr>";
                    }
                } else {
                    echo "<tr><td colspan='2' style='text-align:center; padding: 30px; color: #999;'>No pending requests at the moment.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3>Active Users</h3>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email Address</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $approved = mysqli_query($conn, "SELECT * FROM users WHERE status='approved' AND role='engineer' ORDER BY username ASC");
                if(mysqli_num_rows($approved) > 0) {
                    while($u = mysqli_fetch_assoc($approved)){
                        echo "<tr>
                            <td><strong>{$u['username']}</strong></td>
                            <td style='color: #555;'>{$u['email']}</td>
                            <td><span class='status-badge'>Active</span></td>
                            <td style='text-align: right;'>
                                <a href='?cancel={$u['id']}' class='btn-revoke' onclick='return confirm(\"Revoke access for this user?\")'>Remove Account</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align:center; padding: 30px; color: #999;'>No approved engineers found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function openNav() {
    document.getElementById("mySidebar").style.width = "250px";
    document.getElementById("main").style.marginLeft = "250px";
    document.getElementById("openBtn").style.display = "none";
    document.getElementById("headerContainer").style.paddingLeft = "0";
}
function closeNav() {
    document.getElementById("mySidebar").style.width = "0";
    document.getElementById("main").style.marginLeft= "0";
    document.getElementById("openBtn").style.display = "block";
    document.getElementById("headerContainer").style.paddingLeft = "85px";
}
</script>

</body>
</html>