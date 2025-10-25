<?php
// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_data = null;

if($is_logged_in) {
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../config/database.php';
    
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_data = $stmt->fetch();
}
?>

<!-- Navigation Bar -->
<nav class="navbar <?php echo $is_logged_in ? 'logged-in' : ''; ?>">
    <div class="navbar-container">
        <!-- Logo -->
        <div class="navbar-brand">
            <a href="<?php echo APP_URL; ?>/<?php echo $is_logged_in ? 'dashboard.php' : 'index.php'; ?>">
                <i class="fas fa-project-diagram"></i>
                <span class="brand-text"><?php echo APP_NAME; ?></span>
            </a>
        </div>

        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <?php if($is_logged_in): ?>
        <!-- Search Bar (for logged-in users) -->
        <div class="navbar-search">
            <input type="text" id="globalSearch" placeholder="Search...">
            <i class="fas fa-search"></i>
        </div>

        <!-- Navigation Links -->
        <ul class="navbar-menu">
            <li><a href="<?php echo APP_URL; ?>/dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="<?php echo APP_URL; ?>/tasks.php" class="nav-link"><i class="fas fa-tasks"></i> Tasks</a></li>
            <li><a href="<?php echo APP_URL; ?>/calendar.php" class="nav-link"><i class="fas fa-calendar"></i> Calendar</a></li>
            <?php if($user_data && $user_data['role'] === 'admin'): ?>
            <li><a href="<?php echo APP_URL; ?>/admin/index.php" class="nav-link"><i class="fas fa-cog"></i> Admin</a></li>
            <?php endif; ?>
        </ul>

        <!-- Right Side Icons -->
        <div class="navbar-actions">
            <!-- Notifications -->
            <div class="navbar-action-item" id="notificationDropdown">
                <button class="action-btn" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="badge" id="notificationCount">0</span>
                </button>
                <div class="dropdown-menu notifications-dropdown">
                    <div class="dropdown-header">
                        <h4>Notifications</h4>
                        <a href="#" class="mark-all-read">Mark all as read</a>
                    </div>
                    <div class="dropdown-body" id="notificationList">
                        <p class="no-notifications">No new notifications</p>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div class="navbar-action-item" id="messageDropdown">
                <button class="action-btn" aria-label="Messages">
                    <i class="fas fa-envelope"></i>
                    <span class="badge" id="messageCount">0</span>
                </button>
                <div class="dropdown-menu messages-dropdown">
                    <div class="dropdown-header">
                        <h4>Messages</h4>
                        <a href="<?php echo APP_URL; ?>/messenger.php">View all</a>
                    </div>
                    <div class="dropdown-body" id="messageList">
                        <p class="no-messages">No new messages</p>
                    </div>
                </div>
            </div>

            <!-- Profile -->
            <div class="navbar-action-item" id="profileDropdown">
                <button class="profile-btn" aria-label="Profile menu">
                    <img src="<?php echo APP_URL; ?>/uploads/avatars/<?php echo $user_data['avatar']; ?>" 
                         alt="Profile" class="profile-avatar" 
                         onerror="this.src='<?php echo APP_URL; ?>/assets/img/default-avatar.png'">
                    <span class="profile-name"><?php echo htmlspecialchars($user_data['full_name']); ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu profile-dropdown">
                    <div class="profile-info">
                        <img src="<?php echo APP_URL; ?>/uploads/avatars/<?php echo $user_data['avatar']; ?>" 
                             alt="Profile" onerror="this.src='<?php echo APP_URL; ?>/assets/img/default-avatar.png'">
                        <div>
                            <h4><?php echo htmlspecialchars($user_data['full_name']); ?></h4>
                            <p><?php echo htmlspecialchars($user_data['email']); ?></p>
                            <span class="role-badge role-<?php echo $user_data['role']; ?>">
                                <?php echo ucfirst($user_data['role']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="<?php echo APP_URL; ?>/profile.php" class="dropdown-item">
                        <i class="fas fa-user"></i> My Profile
                    </a>
                    <a href="<?php echo APP_URL; ?>/settings.php" class="dropdown-item">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                    <a href="#" class="dropdown-item" id="toggleDarkMode">
                        <i class="fas fa-moon"></i> <span id="darkModeText">Dark Mode</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="<?php echo APP_URL; ?>/logout.php" class="dropdown-item logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- Guest Navigation -->
        <ul class="navbar-menu">
            <li><a href="<?php echo APP_URL; ?>/index.php" class="nav-link"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="#features" class="nav-link"><i class="fas fa-star"></i> Features</a></li>
            <li><a href="#about" class="nav-link"><i class="fas fa-info-circle"></i> About</a></li>
        </ul>
        
        <div class="navbar-actions">
            <a href="<?php echo APP_URL; ?>/login.php" class="btn btn-outline">Login</a>
            <a href="<?php echo APP_URL; ?>/register.php" class="btn btn-primary">Get Started</a>
        </div>
        <?php endif; ?>
    </div>
</nav>

<!-- Mobile Sidebar (for logged-in users on mobile) -->
<?php if($is_logged_in): ?>
<div class="mobile-sidebar" id="mobileSidebar">
    <div class="mobile-sidebar-header">
        <h3><?php echo APP_NAME; ?></h3>
        <button class="close-sidebar" id="closeSidebar" aria-label="Close menu">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="mobile-sidebar-body">
        <ul class="mobile-menu">
            <li><a href="<?php echo APP_URL; ?>/dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="<?php echo APP_URL; ?>/tasks.php"><i class="fas fa-tasks"></i> Tasks</a></li>
            <li><a href="<?php echo APP_URL; ?>/calendar.php"><i class="fas fa-calendar"></i> Calendar</a></li>
            <li><a href="<?php echo APP_URL; ?>/messenger.php"><i class="fas fa-envelope"></i> Messages</a></li>
            <li><a href="<?php echo APP_URL; ?>/profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="<?php echo APP_URL; ?>/settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <?php if($user_data && $user_data['role'] === 'admin'): ?>
            <li><a href="<?php echo APP_URL; ?>/admin/index.php"><i class="fas fa-shield-alt"></i> Admin Panel</a></li>
            <?php endif; ?>
            <li><a href="<?php echo APP_URL; ?>/logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
</div>
<div class="mobile-sidebar-overlay" id="mobileSidebarOverlay"></div>
<?php endif; ?>
