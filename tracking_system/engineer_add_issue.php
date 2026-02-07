<?php 
include 'config.php';

// 1. Block if not engineer
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'engineer') {
    header("Location: index.php"); exit();
}

// 2. get data folder and site from url
$f_id = isset($_GET['folder_id']) ? intval($_GET['folder_id']) : 0;
$s_id = isset($_GET['site_id']) ? intval($_GET['site_id']) : 0;

// retrieve info for header displays
$f_res = mysqli_query($conn, "SELECT folder_name FROM folders WHERE id = $f_id");
$f_info = mysqli_fetch_assoc($f_res);

$s_res = mysqli_query($conn, "SELECT site_name FROM sites WHERE id = $s_id");
$s_info = mysqli_fetch_assoc($s_res);

if(!$f_info || !$s_info) {
    die("Invalid folder or site selection.");
}

// 3. logic to store new issues
if(isset($_POST['save_issue'])){
    $pacs    = mysqli_real_escape_string($conn, $_POST['pacs']);
    $otime   = $_POST['otime'];
    $title   = mysqli_real_escape_string($conn, $_POST['title']);
    $details = mysqli_real_escape_string($conn, $_POST['details']);
    $sn      = mysqli_real_escape_string($conn, $_POST['sn']);
    $eic     = mysqli_real_escape_string($conn, $_POST['eic']);
    $status  = $_POST['status'];
    $res     = mysqli_real_escape_string($conn, $_POST['res']);
    $ctime   = !empty($_POST['ctime']) ? $_POST['ctime'] : NULL;
    $uid     = $_SESSION['user_id'];

    //if ctime zeo, put as null in db
    if($ctime) {
        $q = "INSERT INTO issues (folder_id, site_id, pacs_ris_ccis, open_time_date, issue_title, issue_details, serial_number, engineer_in_charge, issue_status, resolution, close_time_date, created_by_user_id) 
              VALUES ('$f_id', '$s_id', '$pacs', '$otime', '$title', '$details', '$sn', '$eic', '$status', '$res', '$ctime', '$uid')";
    } else {
        $q = "INSERT INTO issues (folder_id, site_id, pacs_ris_ccis, open_time_date, issue_title, issue_details, serial_number, engineer_in_charge, issue_status, resolution, created_by_user_id) 
              VALUES ('$f_id', '$s_id', '$pacs', '$otime', '$title', '$details', '$sn', '$eic', '$status', '$res', '$uid')";
    }
    
    if(mysqli_query($conn, $q)){
        header("Location: engineer_select_site.php?folder_id=$f_id&site_id=$s_id");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Issue | GE Tracking</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 40px; margin: 0; }
        .container { max-width: 700px; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); margin: auto; }
        .header-info { background: #e9ecef; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #007bff; }
        h2 { margin: 0; color: #2c3e50; }
        label { font-weight: bold; display: block; margin-top: 15px; color: #34495e; font-size: 14px; }
        input, select, textarea { width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-size: 14px; }
        textarea { height: 100px; resize: vertical; }
        .btn-save { background: #007bff; color: white; border: none; padding: 15px; width: 100%; border-radius: 6px; cursor: pointer; font-size: 16px; margin-top: 25px; font-weight: bold; transition: 0.3s; }
        .btn-save:hover { background: #0056b3; }
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: #888; text-decoration: none; font-size: 14px; }
        .btn-cancel:hover { color: #dc3545; }
        .row { display: flex; gap: 15px; }
        .row > div { flex: 1; }
    </style>
</head>
<body>

<div class="container">
    <h2>Add New Issue Report</h2>
    <div class="header-info">
        <p style="margin:0;">Site: <strong><?php echo $s_info['site_name']; ?></strong></p>
        <p style="margin:5px 0 0 0;">Folder: <strong><?php echo $f_info['folder_name']; ?></strong></p>
    </div>
    
    <form method="POST">
        <label>PACS / RIS / CCIS:</label>
        <input type="text" name="pacs" placeholder="Enter system name..." required>

        <div class="row">
            <div>
                <label>Open Time & Date:</label>
                <input type="datetime-local" name="otime" required value="<?php echo date('Y-m-d\TH:i'); ?>">
            </div>
            <div>
                <label>Status:</label>
                <select name="status">
                    <option value="open">Open</option>
                    <option value="close">Close</option>
                </select>
            </div>
        </div>

        <label>Issue Title:</label>
        <input type="text" name="title" placeholder="Short summary of the issue" required>

        <label>Issue Details:</label>
        <textarea name="details" placeholder="Explain the problem in detail..."></textarea>

        <div class="row">
            <div>
                <label>Serial Number:</label>
                <input type="text" name="sn" placeholder="S/N">
            </div>
            <div>
                <label>Engineer In Charge:</label>
                <input type="text" name="eic" value="<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>" required>
            </div>
        </div>

        <label>Resolution:</label>
        <textarea name="res" placeholder="What was done to fix it?"></textarea>

        <label>Close Time & Date (Optional):</label>
        <input type="datetime-local" name="ctime">

        <button type="submit" name="save_issue" class="btn-save">Add</button>
        <a href="engineer_select_site.php?folder_id=<?php echo $f_id; ?>&site_id=<?php echo $s_id; ?>" class="btn-cancel">Cancel & Go Back</a>
    </form>
</div>

</body>
</html>