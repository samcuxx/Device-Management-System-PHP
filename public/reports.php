<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_check.php";
require_once "../classes/Device.php";
require_once "../includes/helpers.php";

$database = new Database();
$db = $database->getConnection();
$device = new Device($db);

// Get date range from request
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Get report data
$report_data = $device->getReportData($start_date, $end_date);
$status_summary = $device->getStatusSummary($start_date, $end_date);
$type_summary = $device->getTypeSummary($start_date, $end_date);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Comprint Services</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="../assets/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include "../templates/sidebar.php"; ?>
    
    <main class="content">
        <?php include "../templates/header.php"; ?>

        <div class="container-fluid p-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Generate Report</h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="<?php echo $start_date; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="<?php echo $end_date; ?>">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Generate Report</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Status Summary</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Device Types Summary</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="typeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detailed Report</h5>
                </div>
                <div class="card-body">
                    <table id="reportTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Device ID</th>
                                <th>Device Name</th>
                                <th>Type</th>
                                <th>Client</th>
                                <th>Status</th>
                                <th>Date Received</th>
                                <th>Completion Date</th>
                                <th>Duration (Days)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report_data as $row): ?>
                            <tr>
                                <td><?php echo h($row['id']); ?></td>
                                <td><?php echo h($row['device_name']); ?></td>
                                <td><?php echo h($row['device_type']); ?></td>
                                <td><?php echo h($row['client_name']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getStatusColor($row['status']); ?>">
                                        <?php echo h($row['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo formatDate($row['created_at']); ?></td>
                                <td><?php echo $row['completion_date'] ? formatDate($row['completion_date']) : '-'; ?></td>
                                <td><?php echo $row['duration']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/jquery.dataTables.min.js"></script>
    <script src="../assets/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#reportTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        text: 'Export to Excel',
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'pdf',
                        text: 'Export to PDF',
                        className: 'btn btn-danger',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    }
                ]
            });
        });

        // Initialize Charts
        const statusData = <?php echo json_encode($status_summary); ?>;
        const typeData = <?php echo json_encode($type_summary); ?>;

        new Chart(document.getElementById('statusChart'), {
            type: 'pie',
            data: {
                labels: statusData.map(item => item.status),
                datasets: [{
                    data: statusData.map(item => item.count),
                    backgroundColor: [
                        '#ffc107', // Pending
                        '#17a2b8', // In Progress
                        '#28a745'  // Completed
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        new Chart(document.getElementById('typeChart'), {
            type: 'bar',
            data: {
                labels: typeData.map(item => item.device_type),
                datasets: [{
                    label: 'Number of Devices',
                    data: typeData.map(item => item.count),
                    backgroundColor: '#007bff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html> 