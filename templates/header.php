<?php
require_once "../classes/User.php";
$user = new User($db);
$user_data = $user->getUserById($_SESSION['user_id']);
?>
<style>
.text-gradient {
    background: white;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 700;
}
</style>
<header class="header"
    style="background: var(--primary-gradient); padding: 0.75rem 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <div class="d-flex align-items-center justify-content-between w-100">
        <div class="d-flex align-items-center">
            <button class="btn btn-link sidebar-toggle d-md-none text-white me-3">
                <i class="bx bx-menu fs-4"></i>
            </button>

            <div class="header-search d-none d-md-block">
                <h1 class="text-gradient fw-bold">Comprint Services</h1>
            </div>
        </div>

        <div class="header-right d-flex align-items-center">
            <div class="dropdown">
                <button class="btn btn-link dropdown-toggle text-white d-flex align-items-center gap-2" type="button"
                    id="userDropdown" data-bs-toggle="dropdown" style="text-decoration: none;">
                    <div class="d-flex align-items-center bg-white bg-opacity-25 rounded-circle p-2">
                        <i class="bx bx-user text-white"></i>
                    </div>
                    <span class="fw-medium me-2"><?php echo h($user_data['full_name']); ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="userDropdown">
                    <li>
                        <a class="dropdown-item d-flex align-items-center py-2" href="profile.php">
                            <i class="bx bx-user-circle me-2"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center py-2" href="settings.php">
                            <i class="bx bx-cog me-2"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center py-2 text-danger" href="logout.php">
                            <i class="bx bx-log-out me-2"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
</ul>
</div>
</div>
</header>
</header>