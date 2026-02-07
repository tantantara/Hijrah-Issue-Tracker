<?php 
include 'config.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { header("Location: index.php"); exit(); }
?>
<div class="main-content">
    <h1>View Issue Trackers</h1>
    <p>Pilih site di bawah untuk melihat laporan penuh.</p>
    
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
        <?php
        $sites = mysqli_query($conn, "SELECT * FROM sites ORDER BY site_name ASC");
        while($s = mysqli_fetch_assoc($sites)){
            echo "<a href='view_site.php?id={$s['id']}' style='padding: 30px; background: white; border: 1px solid #007bff; border-radius: 8px; text-align: center; text-decoration: none; color: #007bff; font-weight: bold;'>
                    {$s['site_name']}
                  </a>";
        }
        ?>
    </div>
</div>