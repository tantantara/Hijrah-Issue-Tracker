<?php 
include 'config.php';

// 1. block access if not admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { 
    header("Location: index.php"); 
    exit(); 
}

// 2. retrieve folder id
if(!isset($_GET['folder_id'])) {
    header("Location: admin_folders.php");
    exit();
}

$folder_id = intval($_GET['folder_id']);
$f_res = mysqli_query($conn, "SELECT folder_name FROM folders WHERE id = $folder_id");
$f_info = mysqli_fetch_assoc($f_res);

// 3. get site id
$site_id = isset($_GET['site_id']) ? intval($_GET['site_id']) : 0;
$site_name = "Select a Site Location";

if($site_id > 0) {
    $s_res = mysqli_query($conn, "SELECT site_name FROM sites WHERE id = $site_id");
    $s_info = mysqli_fetch_assoc($s_res);
    $site_name = $s_info['site_name'] ?? "Unknown Site";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $f_info['folder_name'] . " - " . $site_name; ?></title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; height: 100vh; background: #f0f2f5; }
        
        /* Sidebar */
        .site-sidebar { width: 210px; background: #2c3e50; color: white; padding: 20px; overflow-y: auto; flex-shrink: 0; display: flex; flex-direction: column; }
        .site-sidebar h3 { border-bottom: 1px solid #455a64; padding-bottom: 10px; font-size: 18px; color: #ecf0f1; }
        .site-link { display: block; padding: 12px; color: #bdc3c7; text-decoration: none; border-radius: 6px; margin-bottom: 5px; transition: 0.3s; font-size: 14px; }
        .site-link:hover, .site-link.active { background: #34495e; color: white; font-weight: bold; padding-left: 20px; }
        .back-btn { background: #34495e; color: #00d2ff; padding: 10px; text-decoration: none; border-radius: 5px; margin-bottom: 20px; text-align: center; font-size: 13px; font-weight: bold; }

        /* Content Area */
        .content { flex-grow: 1; padding: 30px; overflow-y: auto; }
        .header-box { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        
        /* Report Card */
        .report-card { 
            background: white; 
            padding: 25px; 
            border-radius: 12px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); 
            margin-right: 80px; 
            position: relative; 
        }
        
        /* Table Style */
        table { border-collapse: collapse; width: 100%; margin-top: 10px; font-size: 12px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #f8f9fa; color: #333; text-transform: uppercase; }
        tr:hover { background: #fdfdfd; }

        .status-open { color: #d63031; font-weight: bold; }
        .status-close { color: #27ae60; font-weight: bold; }
        
        /* Tombol Save as PDF Style */
        .btn-print { background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 14px; margin-left: 10px; }

        /* ==========================================
            PRINT / SAVE AS PDF OPTIMIZATION
           ========================================== */
        @media print {
            .site-sidebar, .btn-print, .back-btn, .header-box { display: none !important; }
            body { background: white; margin: 0; padding: 0; }
            .content { padding: 0; overflow: visible; width: 100%; }
            .report-card { box-shadow: none; border: none; padding: 0; margin-right: 0; width: 100%; }
            .print-header { display: block !important; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; text-align: center; }
            table { width: 100% !important; border-collapse: collapse !important; font-size: 9pt !important; }
            th, td { border: 1px solid #000 !important; padding: 6px !important; word-wrap: break-word; }
            th { background-color: #f2f2f2 !important; -webkit-print-color-adjust: exact; }
            .status-open { color: #d63031 !important; -webkit-print-color-adjust: exact; }
            .status-close { color: #27ae60 !important; -webkit-print-color-adjust: exact; }
            @page {
                size: landscape;
                margin: 0.5cm 1cm; 
            }
        }
    </style>
</head>
<body>

<div class="site-sidebar">
    <a href="admin_folders.php" class="back-btn">← Back to Dashboard</a>
    <h3>📍 Sites Location</h3>
    <?php 
    $sites = mysqli_query($conn, "SELECT * FROM sites ORDER BY site_name ASC");
    while($s = mysqli_fetch_assoc($sites)) {
        $active = ($site_id == $s['id']) ? "active" : "";
        echo "<a href='admin_view_folder.php?folder_id=$folder_id&site_id={$s['id']}' class='site-link $active'>📍 {$s['site_name']}</a>";
    }
    ?>
</div>

<div class="content">
    <?php if($site_id > 0): ?>
        <div class="header-box">
            <div>
                <h1 style="margin:0; color:#2c3e50; font-size: 28px;"><?php echo $site_name; ?></h1>
                <p style="margin:5px 0; color: #7f8c8d;">Folder: <strong><?php echo $f_info['folder_name']; ?></strong></p>
            </div>
            <button onclick="printReport()" class="btn-print">📄 Save as PDF</button>
        </div>

        <div class="report-card">
            <div class="print-header" style="display:none;">
                <h1 style="margin:0; font-size: 20px;">WEEKLY ISSUE TRACKER</h1>
                <p style="font-size: 14px; margin: 5px 0;">
                    <strong>SITE:</strong> <?php echo strtoupper($site_name); ?> | 
                    <strong>FOLDER:</strong> <?php echo strtoupper($f_info['folder_name']); ?>
                </p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>System</th>
                        <th>Open Time</th>
                        <th>Issue Title</th>
                        <th width="20%">Details</th>
                        <th>S/N</th>
                        <th>Engineer</th>
                        <th>Status</th>
                        <th>Resolution</th>
                        <th>Close Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $issues = mysqli_query($conn, "SELECT * FROM issues WHERE folder_id = $folder_id AND site_id = $site_id ORDER BY open_time_date DESC");
                    if(mysqli_num_rows($issues) > 0) {
                        while($row = mysqli_fetch_assoc($issues)) {
                            $status_class = ($row['issue_status'] == 'open') ? 'status-open' : 'status-close';
                            echo "<tr>
                                    <td>{$row['pacs_ris_ccis']}</td>
                                    <td>".date('d/m/Y H:i', strtotime($row['open_time_date']))."</td>
                                    <td><strong>{$row['issue_title']}</strong></td>
                                    <td><small>{$row['issue_details']}</small></td>
                                    <td>{$row['serial_number']}</td>
                                    <td>{$row['engineer_in_charge']}</td>
                                    <td><span class='$status_class'>".strtoupper($row['issue_status'])."</span></td>
                                    <td><small>{$row['resolution']}</small></td>
                                    <td>".($row['close_time_date'] ? date('d/m/Y H:i', strtotime($row['close_time_date'])) : '-')."</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' style='text-align:center; padding:50px; color: #999;'>No issues found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="text-align:center; margin-top:300px; color:#bdc3c7;">
            <h3>Please select a site from the left menu.</h3>
        </div>
    <?php endif; ?>
</div>

<script>
function printReport() {
    // Membiarkan document.title kekal supaya browser guna Folder & Site sebagai nama fail PDF
    window.print();
}
</script>

</body>
</html>