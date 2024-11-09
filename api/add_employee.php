<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_check.php";
require_once "../classes/User.php";

header('Content-Type: application/json');

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);

    // Set user properties
    $user->username = $_POST['username'];
    $user->password = $_POST['password'];
    $user->full_name = $_POST['full_name'];
    $user->email = $_POST['email'];
    $user->role = $_POST['role'];
    $user->contact_number = $_POST['contact_number'] ?? null;

    // Validate required fields
    if (empty($user->username) || empty($user->password) || empty($user->full_name) || empty($user->email) || empty($user->role)) {
        throw new Exception('All required fields must be filled');
    }

    // Validate password length
    if (strlen($_POST['password']) < 8) {
        throw new Exception('Password must be at least 8 characters long');
    }

    // Create the user
    if ($user->create()) {
        echo json_encode([
            'success' => true,
            'message' => 'Employee added successfully'
        ]);
    } else {
        throw new Exception('Error adding employee');
    }
} catch (PDOException $e) {
    if ($e->getCode() == 23000) { // Duplicate entry
        echo json_encode([
            'success' => false,
            'message' => 'Username or email already exists'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 