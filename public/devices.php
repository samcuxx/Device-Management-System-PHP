<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_check.php";
require_once "../classes/Device.php";
require_once "../includes/helpers.php";

$database = new Database();
$db = $database->getConnection();
$device = new Device($db);

// Get all devices
$devices = $device->getAllDevices(); // Add this method to Device class
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devices - Comprint Services</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
    .search-box {
        max-width: 300px;
        margin-bottom: 1.5rem;
    }

    .search-box input {
        border-radius: 20px;
        padding: 0.5rem 1rem 0.5rem 2.5rem;
        border: 1px solid #dee2e6;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%236c757d" width="18px" height="18px"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>') no-repeat left 1rem center;
    }

    .search-box input:focus {
        outline: none;
        border-color: #2193b0;
        box-shadow: 0 0 0 0.2rem rgba(33, 147, 176, 0.25);
    }

    /* Print-specific styles */
    @media print {

        .sidebar,
        .header,
        .btn-group,
        .dataTables_filter,
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }

        .content {
            margin: 0 !important;
            padding: 0 !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .table {
            width: 100% !important;
            margin: 0 !important;
        }

        .badge {
            border: 1px solid #000 !important;
            padding: 2px 6px !important;
        }

        /* Ensure table content doesn't break across pages */
        tr {
            page-break-inside: avoid;
        }

        /* Add header and footer */
        @page {
            margin: 2cm;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }
    }
    </style>
    <!-- Add DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Add DataTables Buttons CSS -->
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
</head>

<body>
    <?php include "../templates/sidebar.php"; ?>

    <main class="content">
        <?php include "../templates/header.php"; ?>

        <div class="container-fluid p-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Devices</h5>
                    <div>
                        <button id="printButton" class="btn btn-secondary me-2">
                            <i class="bx bx-printer"></i> Print
                        </button>
                        <a href="add_device.php" class="btn btn-primary">
                            <i class="bx bx-plus"></i> Add Device
                        </a>
                    </div>
                </div>
                <div class="card-body" data-date="<?php echo date('Y-m-d H:i:s'); ?>">
                    <div class="search-box">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search devices...">
                    </div>

                    <div class="table-responsive">
                        <table class="table" id="devicesTable">
                            <thead>
                                <tr>
                                    <th>Device ID</th>
                                    <th>Device Name</th>
                                    <th>Type</th>
                                    <th>Client</th>
                                    <th>Status</th>
                                    <th>Date Received</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($devices as $device): ?>
                                <tr>
                                    <td><?php echo h($device['id']); ?></td>
                                    <td><?php echo h($device['device_name']); ?></td>
                                    <td><?php echo h($device['device_type']); ?></td>
                                    <td><?php echo h($device['client_name']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo getStatusColor($device['status']); ?>">
                                            <?php echo h($device['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo formatDate($device['created_at']); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="edit_device.php?id=<?php echo $device['id']; ?>"
                                                class="btn btn-primary">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger delete-device"
                                                data-id="<?php echo $device['id']; ?>">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add all required scripts -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <!-- Add export plugins -->
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

    <!-- Your custom JS -->
    <script src="../assets/js/devices.js"></script>

    <!-- Add this modal HTML just before the closing </body> tag -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this device?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>