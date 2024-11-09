<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_check.php";
require_once "../classes/Device.php";
require_once "../includes/helpers.php";

$database = new Database();
$db = $database->getConnection();
$device = new Device($db);

$success_message = $error_message = "";
$device_data = null;

// Get device ID from URL
if (!isset($_GET['id'])) {
    header("Location: devices.php");
    exit;
}

// Get device data
$device_data = $device->getDeviceById($_GET['id']);
if (!$device_data) {
    header("Location: devices.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = $device->update($_GET['id'], $_POST);
    if ($result['success']) {
        $success_message = "Device updated successfully!";
        // Refresh device data
        $device_data = $device->getDeviceById($_GET['id']);
    } else {
        $error_message = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Device - Comprint Services</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include "../templates/sidebar.php"; ?>
    
    <main class="content">
        <?php include "../templates/header.php"; ?>

        <div class="container-fluid p-4">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Edit Device</h5>
                            <a href="devices.php" class="btn btn-secondary btn-sm">Back to Devices</a>
                        </div>
                        <div class="card-body">
                            <?php if ($success_message): ?>
                                <div class="alert alert-success"><?php echo $success_message; ?></div>
                            <?php endif; ?>
                            <?php if ($error_message): ?>
                                <div class="alert alert-danger"><?php echo $error_message; ?></div>
                            <?php endif; ?>

                            <form method="POST" id="updateDeviceForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="device_name" class="form-label">Device Name *</label>
                                            <input type="text" class="form-control" id="device_name" name="device_name" 
                                                   value="<?php echo h($device_data['device_name']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="device_type" class="form-label">Device Type *</label>
                                            <select class="form-select" id="device_type" name="device_type" required>
                                                <option value="Computer" <?php echo $device_data['device_type'] == 'Computer' ? 'selected' : ''; ?>>Computer</option>
                                                <option value="Printer" <?php echo $device_data['device_type'] == 'Printer' ? 'selected' : ''; ?>>Printer</option>
                                                <option value="Network" <?php echo $device_data['device_type'] == 'Network' ? 'selected' : ''; ?>>Network</option>
                                                <option value="Other" <?php echo $device_data['device_type'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="problem_description" class="form-label">Problem Description *</label>
                                            <textarea class="form-control" id="problem_description" name="problem_description" 
                                                      rows="3" required><?php echo h($device_data['problem_description']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="client_name" class="form-label">Client Name *</label>
                                            <input type="text" class="form-control" id="client_name" name="client_name" 
                                                   value="<?php echo h($device_data['client_name']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="client_contact" class="form-label">Client Contact *</label>
                                            <input type="text" class="form-control" id="client_contact" name="client_contact" 
                                                   value="<?php echo h($device_data['client_contact']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status *</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="Pending" <?php echo $device_data['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="In Progress" <?php echo $device_data['status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                                <option value="Completed" <?php echo $device_data['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Computer-specific fields -->
                                    <div class="col-md-12" id="computerFields" style="display: <?php echo $device_data['device_type'] == 'Computer' ? 'block' : 'none'; ?>">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="ram_size" class="form-label">RAM Size</label>
                                                    <input type="text" class="form-control" id="ram_size" name="ram_size" 
                                                           value="<?php echo h($device_data['ram_size'] ?? ''); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="drive_size" class="form-label">Drive Size</label>
                                                    <input type="text" class="form-control" id="drive_size" name="drive_size" 
                                                           value="<?php echo h($device_data['drive_size'] ?? ''); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">Update Device</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show/hide computer-specific fields based on device type
        document.getElementById('device_type').addEventListener('change', function() {
            const computerFields = document.getElementById('computerFields');
            computerFields.style.display = this.value === 'Computer' ? 'block' : 'none';
            
            // Clear computer-specific fields if device type is not Computer
            if (this.value !== 'Computer') {
                document.getElementById('ram_size').value = '';
                document.getElementById('drive_size').value = '';
            }
        });

        // Form validation
        document.getElementById('updateDeviceForm').addEventListener('submit', function(e) {
            const requiredFields = ['device_name', 'device_type', 'problem_description', 
                                  'client_name', 'client_contact', 'status'];
            
            let isValid = true;
            requiredFields.forEach(field => {
                const input = document.getElementById(field);
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    </script>
</body>
</html>