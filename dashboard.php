<?php
require_once 'includes/config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user = get_current_user();
$db = Database::getInstance()->getConnection();

// Get statistics
$stmt = $db->prepare("SELECT COUNT(*) as total FROM tasks WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$total_tasks = $stmt->fetch()['total'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM tasks WHERE user_id = ? AND status = 'pending'");
$stmt->execute([$_SESSION['user_id']]);
$pending_tasks = $stmt->fetch()['total'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM calendar_events WHERE user_id = ? AND start_datetime >= NOW()");
$stmt->execute([$_SESSION['user_id']]);
$upcoming_events = $stmt->fetch()['total'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM messages WHERE recipient_id = ? AND is_read = 0");
$stmt->execute([$_SESSION['user_id']]);
$unread_messages = $stmt->fetch()['total'];

// Get recent tasks
$stmt = $db->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$_SESSION['user_id']]);
$recent_tasks = $stmt->fetchAll();

// Get recent messages
$stmt = $db->prepare("
    SELECT m.*, u.username, u.full_name, u.avatar 
    FROM messages m 
    JOIN users u ON m.sender_id = u.id 
    WHERE m.recipient_id = ? 
    ORDER BY m.created_at DESC 
    LIMIT 5
");
$stmt->execute([$_SESSION['user_id']]);
$recent_messages = $stmt->fetchAll();

// Get upcoming events
$stmt = $db->prepare("
    SELECT * FROM calendar_events 
    WHERE user_id = ? AND start_datetime >= NOW() 
    ORDER BY start_datetime ASC 
    LIMIT 5
");
$stmt->execute([$_SESSION['user_id']]);
$upcoming_events_list = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CRUD Web App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <i class="fas fa-cube"></i>
                    <span>CRUD Web App</span>
                </div>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="tasks.php"><i class="fas fa-tasks"></i> Tasks</a></li>
                <li><a href="calendar.php"><i class="fas fa-calendar-alt"></i> Calendar</a></li>
                <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <?php if (is_admin()): ?>
                <li><a href="admin/index.php"><i class="fas fa-cog"></i> Admin Panel</a></li>
                <?php endif; ?>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="topbar">
                <div class="topbar-left">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search...">
                    </div>
                </div>
                <div class="topbar-right">
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <?php if ($upcoming_events > 0): ?>
                        <span class="badge"><?php echo $upcoming_events; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="message-icon">
                        <i class="fas fa-envelope"></i>
                        <?php if ($unread_messages > 0): ?>
                        <span class="badge"><?php echo $unread_messages; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="user-menu">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                        </div>
                        <span><?php echo htmlspecialchars($user['full_name']); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <div class="page-header">
                    <h1 class="page-title">Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>!</h1>
                    <div class="breadcrumb">
                        <i class="fas fa-home"></i> Dashboard
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $total_tasks; ?></h3>
                            <p>Total Tasks</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $pending_tasks; ?></h3>
                            <p>Pending Tasks</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $upcoming_events; ?></h3>
                            <p>Upcoming Events</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon danger">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $unread_messages; ?></h3>
                            <p>Unread Messages</p>
                        </div>
                    </div>
                </div>

                <!-- Widgets Grid -->
                <div class="widget-grid">
                    <!-- Tasks Widget -->
                    <div class="widget">
                        <div class="widget-header">
                            <h3 class="widget-title">Recent Tasks</h3>
                            <a href="tasks.php" class="btn btn-small btn-primary">View All</a>
                        </div>
                        <div class="widget-body">
                            <?php if (count($recent_tasks) > 0): ?>
                            <ul class="task-list">
                                <?php foreach ($recent_tasks as $task): ?>
                                <li class="task-item">
                                    <input type="checkbox" class="task-checkbox" 
                                           <?php echo $task['status'] === 'completed' ? 'checked' : ''; ?>>
                                    <div class="task-content">
                                        <div class="task-title"><?php echo htmlspecialchars($task['title']); ?></div>
                                        <div class="task-meta">
                                            <span class="task-priority <?php echo $task['priority']; ?>">
                                                <?php echo ucfirst($task['priority']); ?>
                                            </span>
                                            <?php if ($task['due_date']): ?>
                                            <span> • Due: <?php echo date('M d', strtotime($task['due_date'])); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php else: ?>
                            <p class="text-center" style="padding: 2rem; color: #999;">No tasks yet</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Calendar Widget -->
                    <div class="widget">
                        <div class="widget-header">
                            <h3 class="widget-title">Upcoming Events</h3>
                            <a href="calendar.php" class="btn btn-small btn-primary">View Calendar</a>
                        </div>
                        <div class="widget-body">
                            <?php if (count($upcoming_events_list) > 0): ?>
                            <ul class="task-list">
                                <?php foreach ($upcoming_events_list as $event): ?>
                                <li class="task-item">
                                    <i class="fas fa-calendar" style="color: <?php echo $event['color']; ?>"></i>
                                    <div class="task-content">
                                        <div class="task-title"><?php echo htmlspecialchars($event['title']); ?></div>
                                        <div class="task-meta">
                                            <?php echo date('M d, Y g:i A', strtotime($event['start_datetime'])); ?>
                                        </div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php else: ?>
                            <p class="text-center" style="padding: 2rem; color: #999;">No upcoming events</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Weather Widget -->
                    <div class="widget">
                        <div class="widget-header">
                            <h3 class="widget-title">Weather</h3>
                            <i class="fas fa-sync-alt widget-icon" style="cursor: pointer;" onclick="loadWeather()"></i>
                        </div>
                        <div class="widget-body weather-widget" id="weatherWidget">
                            <div class="weather-icon">
                                <i class="fas fa-sun"></i>
                            </div>
                            <div class="weather-temp">72°F</div>
                            <div class="weather-condition">Sunny</div>
                            <div class="weather-details">
                                <div class="weather-detail">
                                    <i class="fas fa-wind"></i>
                                    <span>12 mph</span>
                                </div>
                                <div class="weather-detail">
                                    <i class="fas fa-tint"></i>
                                    <span>45%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Messages Widget -->
                    <div class="widget">
                        <div class="widget-header">
                            <h3 class="widget-title">Recent Messages</h3>
                            <a href="messages.php" class="btn btn-small btn-primary">View All</a>
                        </div>
                        <div class="widget-body messenger-widget">
                            <div class="messenger-list">
                                <?php if (count($recent_messages) > 0): ?>
                                    <?php foreach ($recent_messages as $msg): ?>
                                    <div class="messenger-item <?php echo !$msg['is_read'] ? 'unread' : ''; ?>">
                                        <div class="messenger-avatar">
                                            <?php echo strtoupper(substr($msg['full_name'], 0, 1)); ?>
                                        </div>
                                        <div class="messenger-content">
                                            <div class="messenger-name"><?php echo htmlspecialchars($msg['full_name']); ?></div>
                                            <div class="messenger-message"><?php echo htmlspecialchars(substr($msg['message'], 0, 50)); ?>...</div>
                                        </div>
                                        <div class="messenger-time">
                                            <?php echo date('g:i A', strtotime($msg['created_at'])); ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                <p class="text-center" style="padding: 2rem; color: #999;">No messages yet</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/tour.js"></script>
</body>
</html>
