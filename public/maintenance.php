<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_check.php";
require_once "../includes/helpers.php";

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$success_message = $error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (isset($_POST['clear_logs'])) {
            // Clear old device history logs (older than 6 months)
            $query = "DELETE FROM device_history WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH)";
            $stmt = $db->prepare($query);
            $stmt->execute();
            
            $success_message = "System logs cleared successfully.";
        }
        
        if (isset($_POST['optimize_tables'])) {
            // Get all tables
            $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($tables as $table) {
                $db->query("OPTIMIZE TABLE " . $table);
            }
            
            $success_message = "Database tables optimized successfully.";
        }
        
        if (isset($_POST['clear_temp'])) {
            // Clear temporary files
            $temp_dir = "../temp/";
            if (is_dir($temp_dir)) {
                $files = glob($temp_dir . '*');
                foreach ($files as $file) {
                    if (is_file($file) && time() - filemtime($file) > 86400) { // Older than 24 hours
                        unlink($file);
                    }
                }
            }
            
            $success_message = "Temporary files cleared successfully.";
        }
        
    } catch (Exception $e) {
        $error_message = "Error during maintenance: " . $e->getMessage();
    }
}

// Get system stats
$stats = [
    'device_history_count' => $db->query("SELECT COUNT(*) FROM device_history")->fetchColumn(),
    'old_logs_count' => $db->query("SELECT COUNT(*) FROM device_history WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH)")->fetchColumn(),
    'db_size' => $db->query("SELECT SUM(data_length + index_length) FROM information_schema.tables WHERE table_schema = DATABASE()")->fetchColumn(),
    'temp_files_count' => is_dir("../temp/") ? count(glob("../temp/*")) : 0
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance - Comprint Services</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include "../templates/sidebar.php"; ?>
    
    <main class="content">
        <?php include "../templates/header.php"; ?>

        <div class="container-fluid p-4">
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="row">
                <!-- System Stats -->
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">System Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <h6>Total Device History Logs</h6>
                                    <p class="h3"><?php echo number_format($stats['device_history_count']); ?></p>
                                </div>
                                <div class="col-md-3">
                                    <h6>Old Logs (>6 months)</h6>
                                    <p class="h3"><?php echo number_format($stats['old_logs_count']); ?></p>
                                </div>
                                <div class="col-md-3">
                                    <h6>Database Size</h6>
                                    <p class="h3"><?php echo formatFileSize($stats['db_size']); ?></p>
                                </div>
                                <div class="col-md-3">
                                    <h6>Temporary Files</h6>
                                    <p class="h3"><?php echo number_format($stats['temp_files_count']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Maintenance Actions -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Maintenance Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Clear System Logs</h5>
                                            <p class="text-muted">Remove old device history logs (older than 6 months)</p>
                                            <form method="POST" onsubmit="return confirm('Are you sure you want to clear old logs?');">
                                                <button type="submit" name="clear_logs" class="btn btn-warning">
                                                    Clear Logs
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Optimize Database</h5>
                                            <p class="text-muted">Optimize database tables for better performance</p>
                                            <form method="POST" onsubmit="return confirm('Are you sure you want to optimize database tables?');">
                                                <button type="submit" name="optimize_tables" class="btn btn-primary">
                                                    Optimize Tables
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Clear Temporary Files</h5>
                                            <p class="text-muted">Remove temporary files older than 24 hours</p>
                                            <form method="POST" onsubmit="return confirm('Are you sure you want to clear temporary files?');">
                                                <button type="submit" name="clear_temp" class="btn btn-danger">
                                                    Clear Files
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html> 