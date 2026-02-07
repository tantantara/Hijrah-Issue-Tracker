<?php 
include 'config.php';

// 1. deny if not admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// 2. add new site
if(isset($_POST['add_site'])) {
    $name = mysqli_real_escape_string($conn, $_POST['site_name']);
    mysqli_query($conn, "INSERT INTO sites (site_name) VALUES ('$name')");
    header("Location: admin_dash.php");
    exit();
}

// 3. create new folder
if(isset($_POST['add_folder'])) {
    $fname = mysqli_real_escape_string($conn, $_POST['folder_name']);
    mysqli_query($conn, "INSERT INTO folders (folder_name) VALUES ('$fname')");
    header("Location: admin_dash.php");
    exit();
}

// 4. delete (Site/Folder)
if(isset($_GET['delete_site'])) {
    $id = intval($_GET['delete_site']);
    mysqli_query($conn, "DELETE FROM sites WHERE id=$id");
    header("Location: admin_dash.php");
}
if(isset($_GET['delete_folder'])) {
    $id = intval($_GET['delete_folder']);
    mysqli_query($conn, "DELETE FROM folders WHERE id=$id");
    header("Location: admin_dash.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | GE</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; background-color: #f0f2f5; transition: 0.5s; }
        
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
        .sidebar .closebtn {
            position: absolute; top: 10px; right: 25px; font-size: 36px; 
            background: none; border: none; color: white; cursor: pointer;
        }

        #main { transition: margin-left .5s; padding: 30px; margin-left: 250px; }
        .header-container {
            display: flex; justify-content: space-between; align-items: center;
            padding: 10px 0; transition: transform 0.5s, padding-left 0.5s;
        }

        .openbtn {
            font-size: 18px; cursor: pointer; background-color: #2c3e50;
            color: white; padding: 10px 15px; border: none; border-radius: 5px;
            position: fixed; top: 15px; left: 15px; display: none; z-index: 1100;
        }

        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 25px; width: 100%; box-sizing: border-box; }

        input[type="text"] { padding: 12px; width: 100%; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 10px; box-sizing: border-box; }
        .btn-save { background-color: #007bff; color: white; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; font-weight: bold; width: 100%; }
        
        .hijrahsidebar-logo { width: 100px; display: block; margin:auto;}
        .sidebar-logo { 
            width: 70px;            /* Your preferred size */
            display: block; 
            position: absolute;     /* Pins the logo relative to the sidebar */
            bottom: 30px;           /* Distance from the very bottom */
            left: 50%;              /* Moves it to the middle */
            transform: translateX(-50%); /* Perfectly centers it horizontally */
        }
        .main-header-logo { height: 50px; margin-right: 15px; vertical-align: middle; }

        .list-item { 
            display: flex; justify-content: space-between; background: #f8f9fa; 
            padding: 10px 15px; margin-bottom: 5px; border-radius: 6px; border-left: 4px solid #007bff;
        }
        .btn-delete { color: #ff7675; text-decoration: none; font-size: 13px; font-weight: bold; }

        .grid-container {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px;
        }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 13px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; color: #555; }
        .badge-open { background: #fee2e2; color: #dc2626; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px; }
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
    <a href="admin_dash.php" style="color: white; background: #34495e;">Dashboard</a>
    <a href="admin_requests.php">Access Requests</a>
    <a href="admin_folders.php" >Manage Folders</a>
    <a href="logout.php" style="color: #ff7675; margin-top: 50px;">Logout</a>
    <img src="GE-healthcare-logo_front.png" alt="GE Logo" class="sidebar-logo">
</div>

<button class="openbtn" id="openBtn" onclick="openNav()">☰ Menu</button>

<div id="main">
    <div class="header-container" id="headerContainer">
        <div style="display: flex; align-items: center;">
            <img src="Hijrah-Inovatif-Logo.png" alt="Hijrah Inovatif" class="main-header-logo">
            <h1 style="margin: 0; font-size: 24px; color: #2c3e50;">Dashboard</h1>
        </div>
        <div style="text-align: right;">
            <small style="color: #666;">Logged in as</small><br>
            <strong>Admin</strong>
        </div>
    </div>
    <hr style="border: 0; border-top: 1px solid #ddd; margin: 15px 0 25px 0;">

    <div class="card" style="border-top: 4px solid #dc2626;">
        <h3 style="margin-top:0; color: #dc2626;">Open Issues</h3>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Site</th>
                        <th>Folder</th>
                        <th>Issue Title</th>
                        <th>System</th>
                        <th>Open Since</th>
                        <th>Engineer</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $q_active = "SELECT i.*, s.site_name, f.folder_name 
                                 FROM issues i
                                 JOIN sites s ON i.site_id = s.id
                                 JOIN folders f ON i.folder_id = f.id
                                 WHERE i.issue_status = 'open'
                                 ORDER BY i.open_time_date DESC";
                    
                    $res_active = mysqli_query($conn, $q_active);
                    
                    if(mysqli_num_rows($res_active) > 0) {
                        while($row = mysqli_fetch_assoc($res_active)) {
                            echo "<tr>
                                    <td><strong>{$row['site_name']}</strong></td>
                                    <td><small>{$row['folder_name']}</small></td>
                                    <td>{$row['issue_title']}</td>
                                    <td>{$row['pacs_ris_ccis']}</td>
                                    <td>" . date('d/m/Y H:i', strtotime($row['open_time_date'])) . "</td>
                                    <td>{$row['engineer_in_charge']}</td>
                                    <td><span class='badge-open'>OPEN</span></td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' style='text-align:center; padding:30px; color:#999;'>All issues are closed. Yay!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid-container">
        <div class="card">
            <h3 style="margin-top:0;">+ Add Site Location</h3>
            <h6 style="margin-top:0; font-weight:normal; color:gray;">
                        Noted that deleting a site will REMOVE ALL its data including issues
            </h6>
            <form method="POST">
                <input type="text" name="site_name" placeholder="Site name" required>
                <button type="submit" name="add_site" class="btn-save">Save Site</button>
            </form>
            <h4 style="margin: 20px 0 10px 0; color: #555; border-bottom: 2px solid #eee; padding-bottom: 5px;">Active Sites</h4>
            <div style="max-height: 250px; overflow-y: auto;">
                <?php 
                $sites = mysqli_query($conn, "SELECT * FROM sites ORDER BY site_name ASC");
                while($s = mysqli_fetch_assoc($sites)){
                    echo "<div class='list-item'>
                            <span>{$s['site_name']}</span>
                            <a href='?delete_site={$s['id']}' class='btn-delete' onclick='return confirm(\"Delete this site? The data from it will be removed too\")'>Remove</a>
                          </div>";
                }
                ?>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-top:0;">+ Create New Folder</h3>
            <form method="POST">
                <input type="text" name="folder_name" placeholder="Folder name" required>
                <button type="submit" name="add_folder" class="btn-save" style="background-color: #17a2b8;">Create Folder</button>
            </form>
            <p style="font-size: 12px; color: #777; margin-top: 15px;">* Folder created here will be available for engineers to categorize issues.</p>
        </div>
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