<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_check.php";
require_once "../classes/User.php";

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Employee ID is required']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$employee = $user->getUserById($_GET['id']);

if ($employee) {
    echo json_encode(['success' => true, 'data' => $employee]);
} else {
    echo json_encode(['success' => false, 'message' => 'Employee not found']);
} 