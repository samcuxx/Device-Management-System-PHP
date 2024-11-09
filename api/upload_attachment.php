<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_check.php";
require_once "../classes/Device.php";
require_once "../includes/upload_handler.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_FILES['file']) || !isset($_POST['device_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $device = new Device($db);
    $uploader = new UploadHandler();
    
    // Upload file
    $upload_result = $uploader->uploadFile($_FILES['file'], $_POST['device_id']);
    
    // Add to database
    if ($device->addAttachment(
        $_POST['device_id'],
        $upload_result['filename'],
        $upload_result['filepath']
    )) {
        echo json_encode([
            'success' => true,
            'message' => 'File uploaded successfully',
            'data' => $upload_result
        ]);
    } else {
        // Delete uploaded file if database insert fails
        $uploader->deleteFile($upload_result['filename']);
        throw new RuntimeException('Failed to save file information to database');
    }
    
} catch (RuntimeException $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 