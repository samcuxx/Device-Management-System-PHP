<?php
require_once "../config/database.php";
require_once "../classes/User.php";

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

// Set user properties
$user->username = "SamCux";
$user->password = "SamCux123456789";
$user->full_name = "SamCux";
$user->email = "samcux@comprint.com"; // Adding a required email
$user->role = "admin";
$user->contact_number = "0531114854";

// Create the user
if($user->create()) {
    echo "User created successfully!";
} else {
    echo "Unable to create user.";
} 