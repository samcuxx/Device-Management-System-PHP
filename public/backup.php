<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_check.php";

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Database configuration
$database = new Database();
$db = $database->getConnection();
$host = $database->getHost();
$db_name = $database->getDbName();
$username = $database->getUsername();
$password = $database->getPassword();

// Backup directory
$backup_dir = "../backups/";
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

// Generate backup filename
$backup_file = $backup_dir . $db_name . '_' . date('Y-m-d_H-i-s') . '.sql';

// Command for mysqldump
$command = sprintf(
    'mysqldump --host=%s --user=%s --password=%s %s > %s',
    escapeshellarg($host),
    escapeshellarg($username),
    escapeshellarg($password),
    escapeshellarg($db_name),
    escapeshellarg($backup_file)
);

// Execute backup
exec($command, $output, $return_var);

if ($return_var === 0) {
    // Backup successful
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($backup_file));
    header('Content-Length: ' . filesize($backup_file));
    readfile($backup_file);
    unlink($backup_file); // Delete the file after download
    exit;
} else {
    // Backup failed
    header("Location: settings.php?error=backup_failed");
    exit;
} 