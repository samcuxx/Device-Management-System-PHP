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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $data = [
            'device_name' => $_POST['device_name'],
            'device_type' => $_POST['device_type'],
            'problem_description' => $_POST['problem_description'],
            'client_name' => $_POST['client_name'],
            'client_contact' => $_POST['client_contact'],
            'status' => $_POST['status'],
            'assigned_to' => $_POST['assigned_to'] ?? null
        ];

        // Add conditional fields for computers
        if ($_POST['device_type'] == 'Computer') {
            $data['ram_size'] = $_POST['ram_size'];
            $data['drive_size'] = $_POST['drive_size'];
        }

        if ($device->create($data)) {
            $success_message = "Device added successfully!";
        } else {
            $error_message = "Error adding device.";
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Device - Comprint Services</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>
    <?php include "../templates/sidebar.php"; ?>

    <main class="content">
        <?php include "../templates/header.php"; ?>

        <div class="container-fluid p-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Add New Device</h5>
                </div>
                <div class="card-body">
                    <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                    <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" id="addDeviceForm" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="device_name" class="form-label">Device Name *</label>
                                <input type="text" class="form-control" id="device_name" name="device_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="device_type" class="form-label">Device Type *</label>
                                <select class="form-select" id="device_type" name="device_type" required>
                                    <option value="">Select Type</option>
                                    <option value="Computer">Computer</option>
                                    <option value="Printer">Printer</option>
                                    <option value="Network">Network</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div id="computerFields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ram_size" class="form-label">RAM Size</label>
                                    <input type="text" class="form-control" id="ram_size" name="ram_size">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="drive_size" class="form-label">Drive Size</label>
                                    <input type="text" class="form-control" id="drive_size" name="drive_size">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="problem_description" class="form-label">Problem Description *</label>
                            <textarea class="form-control" id="problem_description" name="problem_description" rows="3"
                                required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="client_name" class="form-label">Client Name *</label>
                                <input type="text" class="form-control" id="client_name" name="client_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="client_contact" class="form-label">Client Contact *</label>
                                <input type="text" class="form-control" id="client_contact" name="client_contact"
                                    required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Pending">Pending</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="device_image" class="form-label">Device Image</label>
                                <input type="file" class="form-control" id="device_image" name="device_image"
                                    accept="image/*">
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Add Device</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
    // Show/hide computer fields based on device type
    document.getElementById('device_type').addEventListener('change', function() {
        const computerFields = document.getElementById('computerFields');
        computerFields.style.display = this.value === 'Computer' ? 'block' : 'none';
    });
    </script>
</body>

</html>