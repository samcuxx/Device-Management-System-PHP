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

    // Validate employee_id
    if (!isset($_POST['employee_id']) || empty($_POST['employee_id'])) {
        throw new Exception('Employee ID is required');
    }

    // Prepare update data
    $update_data = [
        'full_name' => $_POST['full_name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'role' => $_POST['role'] ?? '',
        'contact_number' => $_POST['contact_number'] ?? null
    ];

    // Validate required fields
    if (empty($update_data['full_name']) || empty($update_data['email']) || empty($update_data['role'])) {
        throw new Exception('Full name, email, and role are required');
    }

    // Add password only if it's provided
    if (!empty($_POST['password'])) {
        if (strlen($_POST['password']) < 8) {
            throw new Exception('Password must be at least 8 characters long');
        }
        $update_data['password'] = $_POST['password'];
    }

    // Update the user
    if ($user->update($_POST['employee_id'], $update_data)) {
        echo json_encode([
            'success' => true,
            'message' => 'Employee updated successfully'
        ]);
    } else {
        throw new Exception('Error updating employee');
    }
} catch (PDOException $e) {
    if ($e->getCode() == 23000) { // Duplicate entry
        echo json_encode([
            'success' => false,
            'message' => 'Email already exists'
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