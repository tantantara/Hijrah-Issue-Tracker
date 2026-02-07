<?php 
include 'config.php';

if(!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'engineer' && $_SESSION['role'] != 'admin')) {
    header("Location: index.php"); exit();
}

// 2. retrieve the existed data
if(!isset($_GET['id'])) {
    header("Location: index.php"); exit();
}

$id = intval($_GET['id']);
$q = mysqli_query($conn, "SELECT i.*, f.folder_name, s.site_name 
                          FROM issues i 
                          JOIN folders f ON i.folder_id = f.id 
                          JOIN sites s ON i.site_id = s.id 
                          WHERE i.id = $id");
$issue = mysqli_fetch_assoc($q);

if(!$issue) {
    echo "Issue not found!";
    exit();
}

//redirect path based on role
$redirect_page = ($_SESSION['role'] == 'admin') ? 'admin_view_folder.php' : 'engineer_select_site.php';
$back_url = $redirect_page . "?folder_id=" . $issue['folder_id'] . "&site_id=" . $issue['site_id'];

// 3. Logic to update issue
if(isset($_POST['update_issue'])){
    $pacs    = mysqli_real_escape_string($conn, $_POST['pacs']);
    $otime   = $_POST['otime'];
    $title   = mysqli_real_escape_string($conn, $_POST['title']);
    $details = mysqli_real_escape_string($conn, $_POST['details']);
    $sn      = mysqli_real_escape_string($conn, $_POST['sn']);
    $eic     = mysqli_real_escape_string($conn, $_POST['eic']);
    $status  = $_POST['status'];
    $res     = mysqli_real_escape_string($conn, $_POST['res']);
    
    // Handle close time (kalau kosong simpan NULL)
    $ctime   = !empty($_POST['ctime']) ? "'".$_POST['ctime']."'" : "NULL";

    $update_q = "UPDATE issues SET 
                    pacs_ris_ccis = '$pacs', 
                    open_time_date = '$otime', 
                    issue_title = '$title', 
                    issue_details = '$details', 
                    serial_number = '$sn', 
                    engineer_in_charge = '$eic', 
                    issue_status = '$status', 
                    resolution = '$res', 
                    close_time_date = $ctime 
                 WHERE id = $id";

    if(mysqli_query($conn, $update_q)){
        header("Location: " . $back_url);
        exit();
    } else {
        echo "Error updating: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Issue | GE Tracking</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 40px; margin: 0; }
        .container { max-width: 700px; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); margin: auto; }
        
        /* Header Info Style matching Add Issue */
        .header-info { background: #e9ecef; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #ffc107; }
        
        h2 { margin: 0; color: #2c3e50; }
        label { font-weight: bold; display: block; margin-top: 15px; color: #34495e; font-size: 14px; }
        input, select, textarea { width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-size: 14px; }
        textarea { height: 100px; resize: vertical; }
        
        .btn-update { background: #007bff; color: white; border: none; padding: 15px; width: 100%; border-radius: 6px; cursor: pointer; font-size: 16px; margin-top: 25px; font-weight: bold; transition: 0.3s; }
        .btn-update:hover { background: #0056b3; }
        
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: #888; text-decoration: none; font-size: 14px; }
        .btn-cancel:hover { color: #dc3545; }
        
        .row { display: flex; gap: 15px; }
        .row > div { flex: 1; }

        /* Status colors */
        .opt-open { color: #d63031; font-weight: bold; }
        .opt-close { color: #27ae60; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Issue Report</h2>
    
    <div class="header-info">
        <p style="margin:0;">Site: <strong><?php echo $issue['site_name']; ?></strong></p>
        <p style="margin:5px 0 0 0;">Folder: <strong><?php echo $issue['folder_name']; ?></strong></p>
    </div>
    
    <form method="POST">
        <label>PACS / RIS / CCIS:</label>
        <input type="text" name="pacs" value="<?php echo $issue['pacs_ris_ccis']; ?>" required>

        <div class="row">
            <div>
                <label>Open Time & Date:</label>
                <input type="datetime-local" name="otime" value="<?php echo date('Y-m-d\TH:i', strtotime($issue['open_time_date'])); ?>" required>
            </div>
            <div>
                <label>Status:</label>
                <select name="status">
                    <option value="open" <?php if($issue['issue_status'] == 'open') echo 'selected'; ?>>Open</option>
                    <option value="close" <?php if($issue['issue_status'] == 'close') echo 'selected'; ?>>Close</option>
                </select>
            </div>
        </div>

        <label>Issue Title:</label>
        <input type="text" name="title" value="<?php echo $issue['issue_title']; ?>" required>

        <label>Issue Details:</label>
        <textarea name="details"><?php echo $issue['issue_details']; ?></textarea>

        <div class="row">
            <div>
                <label>Serial Number:</label>
                <input type="text" name="sn" value="<?php echo $issue['serial_number']; ?>" placeholder="S/N">
            </div>
            <div>
                <label>Engineer In Charge:</label>
                <input type="text" name="eic" value="<?php echo $issue['engineer_in_charge']; ?>" required>
            </div>
        </div>

        <label>Resolution:</label>
        <textarea name="res" placeholder="Describe the fix..."><?php echo $issue['resolution']; ?></textarea>

        <label>Close Time & Date (Optional):</label>
        <input type="datetime-local" name="ctime" value="<?php echo $issue['close_time_date'] ? date('Y-m-d\TH:i', strtotime($issue['close_time_date'])) : ''; ?>">

        <button type="submit" name="update_issue" class="btn-update">Update</button>
        
        <a href="<?php echo $back_url; ?>" class="btn-cancel">Cancel & Go Back</a>
    </form>
</div>

</body>
</html>