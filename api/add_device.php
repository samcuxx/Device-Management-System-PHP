<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_check.php";
require_once "../classes/Device.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $device = new Device($db);
    
    // Prepare device data
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

    // Create device
    if ($device->create($data)) {
        $device_id = $db->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Device added successfully',
            'device_id' => $device_id
        ]);
    } else {
        throw new RuntimeException('Failed to add device');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 