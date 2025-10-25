<?php
$db = Database::getInstance()->getConnection();

// Get unread message count
$stmt = $db->prepare("SELECT COUNT(*) as total FROM messages WHERE recipient_id = ? AND is_read = 0");
$stmt->execute([$_SESSION['user_id']]);
$unread_messages = $stmt->fetch()['total'];

// Get upcoming events count
$stmt = $db->prepare("SELECT COUNT(*) as total FROM calendar_events WHERE user_id = ? AND start_datetime >= NOW()");
$stmt->execute([$_SESSION['user_id']]);
$upcoming_events = $stmt->fetch()['total'];

$current_user = get_current_user();
?>
<div class="topbar">
    <div class="topbar-left">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search...">
        </div>
    </div>
    <div class="topbar-right">
        <div class="notification-icon" title="Notifications">
            <i class="fas fa-bell"></i>
            <?php if ($upcoming_events > 0): ?>
            <span class="badge"><?php echo $upcoming_events; ?></span>
            <?php endif; ?>
        </div>
        <div class="message-icon" title="Messages" onclick="window.location.href='messages.php'" style="cursor: pointer;">
            <i class="fas fa-envelope"></i>
            <?php if ($unread_messages > 0): ?>
            <span class="badge"><?php echo $unread_messages; ?></span>
            <?php endif; ?>
        </div>
        <div class="user-menu">
            <div class="user-avatar">
                <?php echo strtoupper(substr($current_user['full_name'], 0, 1)); ?>
            </div>
            <span><?php echo htmlspecialchars($current_user['full_name']); ?></span>
            <i class="fas fa-chevron-down"></i>
            <div class="user-dropdown">
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                <?php if (is_admin()): ?>
                <a href="admin/index.php"><i class="fas fa-shield-alt"></i> Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>
</div>
