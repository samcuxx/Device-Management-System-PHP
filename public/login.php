<?php
session_start();
require_once "../config/database.php";
require_once "../classes/User.php";

if(isset($_POST['login'])) {
    $database = new Database();
    $db = $database->getConnection();
    
    $user = new User($db);
    $login = $user->login($_POST['username'], $_POST['password']);
    
    if($login) {
        $_SESSION['user_id'] = $login['id'];
        $_SESSION['role'] = $login['role'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprint Services - Login</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
    .login-page {
        background-image: url('../assets/img/loginimage.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    .login-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
    }
    </style>
</head>

<body>
    <div class="login-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="login-card card shadow-lg">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <img src="../assets/img/logo.png" alt="Comprint Services" class="login-logo">
                                <div class="login-welcome">
                                    <h4>Welcome Back!</h4>
                                    <p>Please sign in to continue</p>
                                </div>
                            </div>

                            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'logged_out'): ?>
                            <div class="alert alert-success py-2">
                                <i class="bx bx-check-circle me-2"></i>
                                You have been successfully logged out.
                            </div>
                            <?php endif; ?>

                            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'session_expired'): ?>
                            <div class="alert alert-warning py-2">
                                <i class="bx bx-time me-2"></i>
                                Your session has expired. Please login again.
                            </div>
                            <?php endif; ?>

                            <?php if(isset($error)): ?>
                            <div class="alert alert-danger py-2">
                                <i class="bx bx-error-circle me-2"></i>
                                <?php echo $error; ?>
                            </div>
                            <?php endif; ?>

                            <form method="POST" action="" class="login-form">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bx bx-user text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bx bx-lock-alt text-muted"></i>
                                        </span>
                                        <input type="password" class="form-control" id="password" name="password"
                                            required>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                        <label class="form-check-label" for="remember">Remember me</label>
                                    </div>
                                    <a href="forgot-password.php" class="text-primary text-decoration-none small">Forgot
                                        Password?</a>
                                </div>
                                <button type="submit" name="login" class="btn btn-primary w-100">
                                    <i class="bx bx-log-in me-2"></i>Sign In
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>