<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if session has expired (optional)
$session_lifetime = 3600; // 1 hour
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_lifetime)) {
    session_unset();
    session_destroy();
    header("Location: login.php?msg=session_expired");
    exit;
}
$_SESSION['last_activity'] = time(); 