<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_check.php";
require_once "../classes/Device.php";
require_once "../includes/helpers.php";

$database = new Database();
$db = $database->getConnection();
$device = new Device($db);

// Get dashboard statistics
$total_devices = $device->getTotalDevices();
$pending_repairs = $device->getDevicesByStatus('Pending');
$completed_repairs = $device->getDevicesByStatus('Completed');
$recent_devices = $device->getRecentDevices(5);
$device_types_stats = $device->getDeviceTypeStats();
$status_stats = $device->getStatusStats();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Comprint Services</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>
    <?php include "../templates/sidebar.php"; ?>

    <main class="content">
        <?php include "../templates/header.php"; ?>

        <div class="container-fluid p-4">
            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Total Devices</h5>
                                </div>
                                <div class="col-auto">
                                    <div class="stat text-primary">
                                        <i class="bx bx-devices"></i>
                                    </div>
                                </div>
                            </div>
                            <h1 class="mt-1 mb-3"><?php echo $total_devices; ?></h1>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Pending Repairs</h5>
                                </div>
                                <div class="col-auto">
                                    <div class="stat text-warning">
                                        <i class="bx bx-time"></i>
                                    </div>
                                </div>
                            </div>
                            <h1 class="mt-1 mb-3"><?php echo $pending_repairs; ?></h1>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Completed Repairs</h5>
                                </div>
                                <div class="col-auto">
                                    <div class="stat text-success">
                                        <i class="bx bx-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <h1 class="mt-1 mb-3"><?php echo $completed_repairs; ?></h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add this before the charts -->
            <script id="statusData" type="application/json">
            <?php echo json_encode($status_stats); ?>
            </script>
            <script id="typeData" type="application/json">
            <?php echo json_encode($device_types_stats); ?>
            </script>

            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Device Status Distribution</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Device Types Distribution</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="typeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Devices Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Devices</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Device ID</th>
                                    <th>Device Name</th>
                                    <th>Client</th>
                                    <th>Status</th>
                                    <th>Date Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_devices as $device): ?>
                                <tr>
                                    <td>#<?php echo $device['id']; ?></td>
                                    <td><?php echo htmlspecialchars($device['device_name']); ?></td>
                                    <td><?php echo htmlspecialchars($device['client_name']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo getStatusColor($device['status']); ?>">
                                            <?php echo $device['status']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($device['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/dashboard-charts.js"></script>
</body>

</html>