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

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Attachment ID is required']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $device = new Device($db);
    
    // Get attachment details before deletion
    $attachment = $device->getAttachmentById($data['id']);
    if (!$attachment) {
        throw new RuntimeException('Attachment not found');
    }
    
    // Delete file from storage
    $uploader = new UploadHandler();
    if (!$uploader->deleteFile($attachment['file_name'])) {
        throw new RuntimeException('Failed to delete file from storage');
    }
    
    // Delete from database
    if ($device->deleteAttachment($data['id'])) {
        echo json_encode(['success' => true, 'message' => 'Attachment deleted successfully']);
    } else {
        throw new RuntimeException('Failed to delete attachment from database');
    }
    
} catch (RuntimeException $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 