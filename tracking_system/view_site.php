<?php 
include 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php"); exit();
}

if(!isset($_GET['id'])) {
    header("Location: admin_dash.php"); exit();
}
$site_id = intval($_GET['id']);

$site_res = mysqli_query($conn, "SELECT site_name FROM sites WHERE id = $site_id");
$site_info = mysqli_fetch_assoc($site_res);

$show_data = false;
$selected_folder_id = "";
$display_folder_name = "";

// logic if filter button is clicked
if (isset($_POST['filter_folder']) && !empty($_POST['folder_id'])) {
    $selected_folder_id = intval($_POST['folder_id']);
    
    // retrive folder name to put on report page
    $f_res = mysqli_query($conn, "SELECT folder_name FROM folders WHERE id = $selected_folder_id");
    $f_info = mysqli_fetch_assoc($f_res);
    $display_folder_name = $f_info['folder_name'];
    
    $show_data = true;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tracker | <?php echo $site_info['site_name'] . " - " . $display_folder_name; ?></title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 30px; background: #f4f7f6; color: #333; }
        .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 1200px; margin: auto; }
        
        /* report title */
        .report-header { text-align: center; margin-bottom: 30px; display: none; }
        .report-header h1 { margin: 0; color: #007bff; text-transform: uppercase; font-size: 24px; }
        .report-header h2 { margin: 5px 0; color: #555; font-size: 18px; }

        table { border-collapse: collapse; width: 100%; margin-top: 10px; font-size: 12px; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: left; word-wrap: break-word; }
        th { background: #eee !important; color: black; font-weight: bold; }
        
        .no-print { margin-bottom: 20px; padding: 15px; background: #e9ecef; border-radius: 8px; }
        .btn-print { background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .btn-back { text-decoration: none; color: #007bff; font-weight: bold; margin-bottom: 10px; display: inline-block; }
        
        /* css for print pdf */
        @media print {
            body { background: white; margin: 0; padding: 0; }
            .no-print { display: none; }
            .card { box-shadow: none; border: none; width: 100%; max-width: 100%; padding: 0; }
            .report-header { display: block; } /* display title when print */
            table { font-size: 10px; }
            th { background: #eee !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<div class="card">
    <div class="no-print">
        <a href="admin_dash.php" class="btn-back">← Back to Dashboard</a>
        <h1>Site: <?php echo $site_info['site_name']; ?></h1>
        
        <form method="POST">
            <strong>Select Month:</strong> 
            <select name="folder_id" style="padding: 8px; width: 220px;" required>
                <option value="">-- Choose Month --</option>
                <?php 
                $folders = mysqli_query($conn, "SELECT * FROM folders ORDER BY created_at DESC");
                while($f = mysqli_fetch_assoc($folders)) {
                    $sel = ($selected_folder_id == $f['id']) ? "selected" : "";
                    echo "<option value='{$f['id']}' $sel>{$f['folder_name']}</option>";
                }
                ?>
            </select>
            <button type="submit" name="filter_folder" style="padding: 8px 15px; cursor:pointer; background: #007bff; color: white; border:none; border-radius:4px;">View Data</button>
            
            <?php if($show_data): ?>
                <button type="button" onclick="window.print()" class="btn-print" style="float: right;">Save as PDF</button>
            <?php endif; ?>
        </form>
    </div>

    <?php if($show_data): ?>
        <div class="report-header">
            <h1>GE ISSUE TRACKER REPORT</h1>
            <h2>SITE: <?php echo strtoupper($site_info['site_name']); ?></h2>
            <h3>PERIOD: <?php echo strtoupper($display_folder_name); ?></h3>
            <hr>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="10%">System</th>
                    <th width="12%">Open Time</th>
                    <th width="15%">Issue Title</th>
                    <th>Details</th>
                    <th width="10%">S/N</th>
                    <th width="10%">Engineer</th>
                    <th width="8%">Status</th>
                    <th>Resolution</th>
                    <th width="12%">Close Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM issues 
                        WHERE site_id = $site_id AND folder_id = $selected_folder_id
                        ORDER BY open_time_date DESC";
                        
                $res = mysqli_query($conn, $sql);
                
                if(mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)){
                        echo "<tr>
                            <td>{$row['pacs_ris_ccis']}</td>
                            <td>{$row['open_time_date']}</td>
                            <td>{$row['issue_title']}</td>
                            <td>{$row['issue_details']}</td>
                            <td>{$row['serial_number']}</td>
                            <td>{$row['engineer_in_charge']}</td>
                            <td style='font-weight:bold;'>" . strtoupper($row['issue_status']) . "</td>
                            <td>{$row['resolution']}</td>
                            <td>{$row['close_time_date']}</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' style='text-align:center; padding: 30px;'>No data recorded for this month.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="padding: 40px; text-align: center; background: #f8f9fa; border: 2px dashed #ccc; border-radius: 10px;">
            <p style="color: #777; font-size: 18px;">Please select a month and click "View Data" to generate the report.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>