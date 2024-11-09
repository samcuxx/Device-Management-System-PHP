<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<nav id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <img src="../assets/img/logo.png" alt="Comprint Services" class="img-fluid">
    </div>

    <ul class="sidebar-nav">
        <li class="sidebar-item <?php echo $current_page == 'dashboard' ? 'active' : ''; ?>">
            <a href="dashboard.php" class="sidebar-link">
                <i class="bx bxs-dashboard"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="sidebar-header small text-uppercase text-muted">
            <span>Device Management</span>
        </li>

        <li class="sidebar-item <?php echo $current_page == 'devices' ? 'active' : ''; ?>">
            <a href="devices.php" class="sidebar-link">
                <i class="bx bx-devices"></i>
                <span>Devices</span>
            </a>
        </li>

        <li class="sidebar-item <?php echo $current_page == 'add_device' ? 'active' : ''; ?>">
            <a href="add_device.php" class="sidebar-link">
                <i class="bx bx-plus-circle"></i>
                <span>Add Device</span>
            </a>
        </li>

        <?php if ($_SESSION['role'] == 'admin'): ?>
        <li class="sidebar-header small text-uppercase text-muted">
            <span>Administration</span>
        </li>

        <li class="sidebar-item <?php echo $current_page == 'employees' ? 'active' : ''; ?>">
            <a href="employees.php" class="sidebar-link">
                <i class="bx bx-user"></i>
                <span>Employees</span>
            </a>
        </li>

        <li class="sidebar-item <?php echo $current_page == 'reports' ? 'active' : ''; ?>">
            <a href="reports.php" class="sidebar-link">
                <i class="bx bx-file"></i>
                <span>Reports</span>
            </a>
        </li>

        <li class="sidebar-item <?php echo $current_page == 'settings' ? 'active' : ''; ?>">
            <a href="settings.php" class="sidebar-link">
                <i class="bx bx-cog"></i>
                <span>Settings</span>
            </a>
        </li>
        <?php endif; ?>

        <li class="sidebar-header small text-uppercase text-muted">
            <span>Account</span>
        </li>

        <li class="sidebar-item">
            <a href="profile.php" class="sidebar-link">
                <i class="bx bx-user-circle"></i>
                <span>Profile</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="logout.php" class="sidebar-link text-danger">
                <i class="bx bx-log-out"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <small class="text-muted">© 2024 Comprint Services</small>
    </div>
</nav>