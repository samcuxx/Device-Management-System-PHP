<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_check.php";
require_once "../classes/Device.php";

header('Content-Type: application/json');

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id'])) {
        throw new Exception('Device ID is required');
    }

    $database = new Database();
    $db = $database->getConnection();
    $device = new Device($db);

    if ($device->delete($input['id'])) {
        echo json_encode([
            'success' => true,
            'message' => 'Device deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete device');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 