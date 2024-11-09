<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_check.php";
require_once "../classes/User.php";
require_once "../includes/helpers.php";

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$success_message = $error_message = "";

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_company'])) {
        // Update company settings
        $settings = [
            'company_name' => $_POST['company_name'],
            'company_address' => $_POST['company_address'],
            'company_phone' => $_POST['company_phone'],
            'company_email' => $_POST['company_email']
        ];
        
        if (updateSettings($db, 'company_settings', $settings)) {
            $success_message = "Company settings updated successfully.";
        } else {
            $error_message = "Error updating company settings.";
        }
    } elseif (isset($_POST['update_system'])) {
        // Update system settings
        $settings = [
            'default_pagination' => $_POST['default_pagination'],
            'date_format' => $_POST['date_format'],
            'timezone' => $_POST['timezone']
        ];
        
        if (updateSettings($db, 'system_settings', $settings)) {
            $success_message = "System settings updated successfully.";
        } else {
            $error_message = "Error updating system settings.";
        }
    }
}

// Get current settings
$company_settings = getSettings($db, 'company_settings');
$system_settings = getSettings($db, 'system_settings');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Comprint Services</title>
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
                <!-- Company Settings -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Company Settings</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">Company Name</label>
                                    <input type="text" class="form-control" id="company_name" name="company_name" 
                                           value="<?php echo h($company_settings['company_name'] ?? ''); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="company_address" class="form-label">Address</label>
                                    <textarea class="form-control" id="company_address" name="company_address" 
                                              rows="3"><?php echo h($company_settings['company_address'] ?? ''); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="company_phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="company_phone" name="company_phone" 
                                           value="<?php echo h($company_settings['company_phone'] ?? ''); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="company_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="company_email" name="company_email" 
                                           value="<?php echo h($company_settings['company_email'] ?? ''); ?>">
                                </div>
                                <button type="submit" name="update_company" class="btn btn-primary">Update Company Settings</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- System Settings -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">System Settings</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="default_pagination" class="form-label">Default Pagination</label>
                                    <select class="form-select" id="default_pagination" name="default_pagination">
                                        <option value="10" <?php echo ($system_settings['default_pagination'] ?? '') == '10' ? 'selected' : ''; ?>>10</option>
                                        <option value="25" <?php echo ($system_settings['default_pagination'] ?? '') == '25' ? 'selected' : ''; ?>>25</option>
                                        <option value="50" <?php echo ($system_settings['default_pagination'] ?? '') == '50' ? 'selected' : ''; ?>>50</option>
                                        <option value="100" <?php echo ($system_settings['default_pagination'] ?? '') == '100' ? 'selected' : ''; ?>>100</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="date_format" class="form-label">Date Format</label>
                                    <select class="form-select" id="date_format" name="date_format">
                                        <option value="Y-m-d" <?php echo ($system_settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : ''; ?>>YYYY-MM-DD</option>
                                        <option value="d/m/Y" <?php echo ($system_settings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : ''; ?>>DD/MM/YYYY</option>
                                        <option value="m/d/Y" <?php echo ($system_settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : ''; ?>>MM/DD/YYYY</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="timezone" class="form-label">Timezone</label>
                                    <select class="form-select" id="timezone" name="timezone">
                                        <?php foreach (timezone_identifiers_list() as $timezone): ?>
                                            <option value="<?php echo $timezone; ?>" 
                                                    <?php echo ($system_settings['timezone'] ?? '') == $timezone ? 'selected' : ''; ?>>
                                                <?php echo $timezone; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" name="update_system" class="btn btn-primary">Update System Settings</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Backup & Maintenance -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Backup & Maintenance</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Database Backup</h6>
                                    <p class="text-muted">Create a backup of your database</p>
                                    <a href="backup.php" class="btn btn-primary">Create Backup</a>
                                </div>
                                <div class="col-md-6">
                                    <h6>System Maintenance</h6>
                                    <p class="text-muted">Clear system cache and temporary files</p>
                                    <a href="maintenance.php" class="btn btn-warning">Run Maintenance</a>
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