<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <i class="fas fa-cube"></i>
            <span>CRUD Web App</span>
        </div>
    </div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Dashboard
        </a></li>
        <li><a href="tasks.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'tasks.php' ? 'active' : ''; ?>">
            <i class="fas fa-tasks"></i> Tasks
        </a></li>
        <li><a href="calendar.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'calendar.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-alt"></i> Calendar
        </a></li>
        <li><a href="messages.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'messages.php' ? 'active' : ''; ?>">
            <i class="fas fa-envelope"></i> Messages
        </a></li>
        <li><a href="profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : ''; ?>">
            <i class="fas fa-user"></i> Profile
        </a></li>
        <?php if (is_admin()): ?>
        <li><a href="admin/index.php" class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? 'active' : ''; ?>">
            <i class="fas fa-cog"></i> Admin Panel
        </a></li>
        <?php endif; ?>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</aside>
