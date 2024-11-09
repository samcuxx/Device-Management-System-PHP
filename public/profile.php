<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_check.php";
require_once "../classes/User.php";
require_once "../includes/helpers.php";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$success_message = $error_message = "";
$user_data = $user->getUserById($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $update_data = [
            'full_name' => $_POST['full_name'],
            'email' => $_POST['email'],
            'contact_number' => $_POST['contact_number']
        ];

        // Only update password if a new one is provided
        if (!empty($_POST['new_password'])) {
            // Verify current password first
            if ($user->verifyPassword($_SESSION['user_id'], $_POST['current_password'])) {
                $update_data['password'] = $_POST['new_password'];
            } else {
                throw new Exception('Current password is incorrect');
            }
        }

        if ($user->update($_SESSION['user_id'], $update_data)) {
            $success_message = "Profile updated successfully!";
            $user_data = $user->getUserById($_SESSION['user_id']); // Refresh user data
        } else {
            $error_message = "Error updating profile.";
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Comprint Services</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include "../templates/sidebar.php"; ?>
    
    <main class="content">
        <?php include "../templates/header.php"; ?>

        <div class="container-fluid p-4">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Profile Settings</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($success_message): ?>
                                <div class="alert alert-success" role="alert">
                                    <i class="bx bx-check-circle me-2"></i>
                                    <?php echo $success_message; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($error_message): ?>
                                <div class="alert alert-danger" role="alert">
                                    <i class="bx bx-error-circle me-2"></i>
                                    <?php echo $error_message; ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="" class="needs-validation" novalidate>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" 
                                               value="<?php echo h($user_data['username']); ?>" disabled>
                                        <small class="text-muted">Username cannot be changed</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="role" class="form-label">Role</label>
                                        <input type="text" class="form-control" id="role" 
                                               value="<?php echo ucfirst(h($user_data['role'])); ?>" disabled>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="<?php echo h($user_data['full_name']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo h($user_data['email']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="contact_number" class="form-label">Contact Number</label>
                                    <input type="text" class="form-control" id="contact_number" name="contact_number" 
                                           value="<?php echo h($user_data['contact_number']); ?>">
                                </div>

                                <hr class="my-4">

                                <h6 class="mb-3">Change Password</h6>
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" 
                                               minlength="8">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" 
                                               name="confirm_password">
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-save me-2"></i>Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Account Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-xl bg-primary bg-opacity-10 text-primary rounded-circle">
                                        <i class="bx bx-user-circle fs-2"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1"><?php echo h($user_data['full_name']); ?></h6>
                                    <small class="text-muted"><?php echo ucfirst(h($user_data['role'])); ?></small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="small text-muted">Account Created</label>
                                <div><?php echo formatDate($user_data['created_at']); ?></div>
                            </div>

                            <div class="mb-3">
                                <label class="small text-muted">Last Login</label>
                                <div><?php echo isset($user_data['last_login']) ? formatDate($user_data['last_login']) : 'N/A'; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            if (this.value !== newPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });

        // Form validation
        (function() {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html> 