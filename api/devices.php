<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_check.php";
require_once "../classes/Device.php";

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();
$device = new Device($db);

// Get parameters from DataTables
$draw = $_POST['draw'] ?? 1;
$start = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;
$search = $_POST['search']['value'] ?? '';
$order_column = $_POST['order'][0]['column'] ?? 1;
$order_dir = $_POST['order'][0]['dir'] ?? 'asc';

// Get total count and filtered data
$total_records = $device->getTotalCount();
$filtered_records = $device->getFilteredCount($search);
$devices = $device->getDevicesForDataTable($start, $length, $search, $order_column, $order_dir);

// Format data for DataTables
$data = array_map(function($device) {
    return [
        'id' => $device['id'],
        'device_name' => htmlspecialchars($device['device_name']),
        'device_type' => htmlspecialchars($device['device_type']),
        'client_name' => htmlspecialchars($device['client_name']),
        'status' => $device['status'],
        'created_at' => $device['created_at'],
        // Add any other fields you want to include in the export
    ];
}, $devices);

echo json_encode([
    'draw' => intval($draw),
    'recordsTotal' => $total_records,
    'recordsFiltered' => $filtered_records,
    'data' => $data
]); 