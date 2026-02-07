<?php 
include 'config.php';

// 1. block if not engineer
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'engineer') {
    header("Location: index.php");
    exit();
}

// 2. search folder logic
$search = "";
if(isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Engineer Dashboard | GE Tracking</title>
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
        .header-container { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; transition: padding-left 0.5s; }
        .openbtn { font-size: 18px; cursor: pointer; background-color: #2c3e50; color: white; padding: 10px 15px; border: none; border-radius: 5px; position: fixed; top: 15px; left: 15px; display: none; z-index: 1100; }
        .main-header-logo { height: 50px; margin-right: 15px; vertical-align: middle; }

         /* SEARCH BAR STYLE */
        .search-box { 
            box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 25px;
            display: flex; gap: 10px;
        }
        .search-box input {
            flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 6px; outline: none;
        }
        .search-btn {
            background: #697f95ff; color: white; border: none; padding: 10px 20px; 
            border-radius: 6px; cursor: pointer; font-weight: normal;
        }

        /* FOLDER LIST STYLE */
        .folder-list-item {
            background: white;
            padding: 15px 25px;
            margin-bottom: 10px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
            border: 1px solid #eee;
            transition: 0.3s;
            text-decoration: none;
            color: inherit;
        }
        .folder-list-item:hover {
            transform: translateX(10px);
            border-left: 5px solid #007bff;
            background: #fdfdfd;
        }
        .folder-name { font-weight: 600; color: #2c3e50; font-size: 16px; }
        .view-text { color: #007bff; font-size: 13px; font-weight: bold; }
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
    <a href="engineer_dash.php" style="color: white; background: #34495e;">Dashboard</a>
    <a href="logout.php" style="color: #ff7675; margin-top: 50px;">Logout</a>
    <img src="GE-healthcare-logo_front.png" alt="GE Logo" class="sidebar-logo">
</div>

<button class="openbtn" id="openBtn" onclick="openNav()">☰ Menu</button>

<div id="main">
    <div class="header-container" id="headerContainer">
        <div style="display: flex; align-items: center;">
            <img src="Hijrah-Inovatif-Logo.png" alt="Hijrah Inovatif" class="main-header-logo">
            <h1 style="margin: 0; font-size: 24px;">Dashboard</h1>
        </div>
    </div>
    <hr style="border: 0; border-top: 1px solid #ddd; margin: 15px 0 25px 0;">

    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Search folders..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="search-btn">Search</button>
        <?php if($search != ""): ?>
            <a href="engineer_dash.php" style="padding: 10px; color: #e74c3c; text-decoration: none;">Clear</a>
        <?php endif; ?>
    </form>

    <h3 style="color: #007bff; margin-bottom: 20px;">Active Folders</h3>

    <div class="folder-container">
        <?php 
        // SQL Filter: Hanya tunjuk folder yang tidak disembunyikan (is_hidden = 0)
        $sql = "SELECT * FROM folders WHERE is_hidden = 0";
        if($search != "") {
            $sql .= " AND folder_name LIKE '%$search%'";
        }
        $sql .= " ORDER BY id DESC";
        
        $res = mysqli_query($conn, $sql);
        if(mysqli_num_rows($res) > 0) {
            while($f = mysqli_fetch_assoc($res)) {
                // this folder will bring engineer to page choose/add issue
                echo "<a href='engineer_select_site.php?folder_id={$f['id']}' class='folder-list-item'>
                        <span class='folder-name'> {$f['folder_name']}</span>
                      </a>";
            }
        } else {
            echo "<div class='card' style='text-align:center; color:#666;'>No active folders found.</div>";
        }
        ?>
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