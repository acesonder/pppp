<?php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Get user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get user's widgets
$widgetStmt = $conn->prepare("SELECT * FROM dashboard_widgets WHERE user_id = ? AND is_visible = 1 ORDER BY widget_position");
$widgetStmt->execute([$_SESSION['user_id']]);
$widgets = $widgetStmt->fetchAll();

// Get recent tasks
$taskStmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$taskStmt->execute([$_SESSION['user_id']]);
$recent_tasks = $taskStmt->fetchAll();

// Get upcoming events
$eventStmt = $conn->prepare("SELECT * FROM calendar_events WHERE user_id = ? AND event_date >= CURDATE() ORDER BY event_date, event_time LIMIT 5");
$eventStmt->execute([$_SESSION['user_id']]);
$upcoming_events = $eventStmt->fetchAll();

// Get unread messages count
$messageStmt = $conn->prepare("SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0");
$messageStmt->execute([$_SESSION['user_id']]);
$unread_messages = $messageStmt->fetch()['count'];

// Show welcome tour for new users
$show_welcome = isset($_GET['welcome']) && $_GET['welcome'] == 1;

$page_title = 'Dashboard';
$body_class = 'dashboard-page';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
.dashboard-container {
    max-width: 1400px;
    margin: 30px auto;
    padding: 0 20px;
}

.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: var(--border-radius);
    margin-bottom: 30px;
}

.dashboard-header h1 {
    font-size: 32px;
    margin-bottom: 10px;
}

.dashboard-header p {
    opacity: 0.9;
}

.quick-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: var(--transition);
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--box-shadow-lg);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.stat-icon.blue { background: rgba(74, 144, 226, 0.1); color: #4a90e2; }
.stat-icon.green { background: rgba(39, 174, 96, 0.1); color: #27ae60; }
.stat-icon.orange { background: rgba(243, 156, 18, 0.1); color: #f39c12; }
.stat-icon.red { background: rgba(231, 76, 60, 0.1); color: #e74c3c; }

.stat-content h3 {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-content p {
    color: var(--gray-600);
    font-size: 14px;
}

.widgets-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
}

.widget {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
}

.widget-header {
    padding: 20px;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.widget-header h3 {
    font-size: 18px;
    font-weight: 600;
}

.widget-header .widget-actions {
    display: flex;
    gap: 10px;
}

.widget-actions button {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--gray-500);
    font-size: 16px;
    transition: var(--transition);
}

.widget-actions button:hover {
    color: var(--primary-color);
}

.widget-body {
    padding: 20px;
}

.task-item, .event-item, .message-item {
    padding: 15px;
    border-bottom: 1px solid var(--gray-200);
    transition: var(--transition);
}

.task-item:last-child, .event-item:last-child, .message-item:last-child {
    border-bottom: none;
}

.task-item:hover, .event-item:hover, .message-item:hover {
    background-color: var(--gray-100);
}

.task-item h4, .event-item h4 {
    font-size: 16px;
    margin-bottom: 5px;
}

.task-item p, .event-item p {
    color: var(--gray-600);
    font-size: 14px;
}

.task-status {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.task-status.pending { background: #fff3cd; color: #856404; }
.task-status.in_progress { background: #d1ecf1; color: #0c5460; }
.task-status.completed { background: #d4edda; color: #155724; }

.priority-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.priority-badge.low { background: #e9ecef; color: #495057; }
.priority-badge.medium { background: #fff3cd; color: #856404; }
.priority-badge.high { background: #f8d7da; color: #721c24; }
.priority-badge.urgent { background: #721c24; color: white; }

.weather-widget {
    text-align: center;
    padding: 30px 20px;
}

.weather-icon {
    font-size: 64px;
    margin-bottom: 20px;
}

.temperature {
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 10px;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: var(--gray-500);
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}

/* Welcome Tour */
.welcome-tour {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.8);
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.tour-content {
    background: white;
    border-radius: var(--border-radius);
    max-width: 600px;
    width: 100%;
    padding: 40px;
    text-align: center;
}

.tour-content h2 {
    font-size: 28px;
    margin-bottom: 20px;
}

.tour-content p {
    color: var(--gray-600);
    margin-bottom: 30px;
    line-height: 1.6;
}

.tour-features {
    text-align: left;
    margin-bottom: 30px;
}

.tour-features li {
    padding: 10px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.tour-features i {
    color: var(--success-color);
}

@media (max-width: 768px) {
    .widgets-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- Welcome Tour -->
<?php if($show_welcome): ?>
<div class="welcome-tour" id="welcomeTour">
    <div class="tour-content">
        <h2>Welcome to <?php echo APP_NAME; ?>! 🎉</h2>
        <p>Thank you for joining us! Let's take a quick tour of your new dashboard.</p>
        
        <ul class="tour-features">
            <li><i class="fas fa-check-circle"></i> Manage your tasks and projects</li>
            <li><i class="fas fa-check-circle"></i> Schedule events on your calendar</li>
            <li><i class="fas fa-check-circle"></i> Chat with team members using our messenger</li>
            <li><i class="fas fa-check-circle"></i> Customize your dashboard widgets</li>
            <li><i class="fas fa-check-circle"></i> Track your progress with analytics</li>
            <li><i class="fas fa-check-circle"></i> Access everything on any device</li>
        </ul>
        
        <button onclick="closeTour()" class="btn btn-primary btn-lg">Get Started</button>
    </div>
</div>
<?php endif; ?>

<div class="dashboard-container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h1>Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>! 👋</h1>
        <p>Here's what's happening with your projects today</p>
    </div>

    <!-- Quick Stats -->
    <div class="quick-stats">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count($recent_tasks); ?></h3>
                <p>Active Tasks</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count($upcoming_events); ?></h3>
                <p>Upcoming Events</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $unread_messages; ?></h3>
                <p>Unread Messages</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon red">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h3>85%</h3>
                <p>Productivity</p>
            </div>
        </div>
    </div>

    <!-- Widgets Grid -->
    <div class="widgets-grid">
        <!-- Tasks Widget -->
        <div class="widget">
            <div class="widget-header">
                <h3><i class="fas fa-tasks"></i> Recent Tasks</h3>
                <div class="widget-actions">
                    <button onclick="location.href='tasks.php'" title="View all"><i class="fas fa-external-link-alt"></i></button>
                    <button onclick="location.href='tasks.php?action=create'" title="Add new"><i class="fas fa-plus"></i></button>
                </div>
            </div>
            <div class="widget-body">
                <?php if(count($recent_tasks) > 0): ?>
                    <?php foreach($recent_tasks as $task): ?>
                        <div class="task-item">
                            <h4><?php echo htmlspecialchars($task['title']); ?></h4>
                            <p><?php echo htmlspecialchars(substr($task['description'], 0, 100)); ?></p>
                            <div style="display: flex; gap: 10px; margin-top: 10px;">
                                <span class="task-status <?php echo $task['status']; ?>">
                                    <?php echo str_replace('_', ' ', ucfirst($task['status'])); ?>
                                </span>
                                <span class="priority-badge <?php echo $task['priority']; ?>">
                                    <?php echo ucfirst($task['priority']); ?>
                                </span>
                                <?php if($task['due_date']): ?>
                                    <span style="color: var(--gray-600); font-size: 14px;">
                                        <i class="fas fa-clock"></i> <?php echo date('M d, Y', strtotime($task['due_date'])); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-tasks"></i>
                        <p>No tasks yet. Create your first task!</p>
                        <a href="tasks.php?action=create" class="btn btn-primary btn-sm">Create Task</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Calendar Widget -->
        <div class="widget">
            <div class="widget-header">
                <h3><i class="fas fa-calendar"></i> Upcoming Events</h3>
                <div class="widget-actions">
                    <button onclick="location.href='calendar.php'" title="View all"><i class="fas fa-external-link-alt"></i></button>
                    <button onclick="location.href='calendar.php?action=create'" title="Add new"><i class="fas fa-plus"></i></button>
                </div>
            </div>
            <div class="widget-body">
                <?php if(count($upcoming_events) > 0): ?>
                    <?php foreach($upcoming_events as $event): ?>
                        <div class="event-item">
                            <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                            <p style="color: var(--gray-600); font-size: 14px;">
                                <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                                <?php if($event['event_time']): ?>
                                    at <?php echo date('h:i A', strtotime($event['event_time'])); ?>
                                <?php endif; ?>
                            </p>
                            <?php if($event['location']): ?>
                                <p style="color: var(--gray-600); font-size: 14px;">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar"></i>
                        <p>No upcoming events. Schedule one now!</p>
                        <a href="calendar.php?action=create" class="btn btn-primary btn-sm">Add Event</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Weather Widget -->
        <div class="widget">
            <div class="widget-header">
                <h3><i class="fas fa-cloud-sun"></i> Weather</h3>
                <div class="widget-actions">
                    <button onclick="refreshWeather()" title="Refresh"><i class="fas fa-sync-alt"></i></button>
                </div>
            </div>
            <div class="widget-body weather-widget">
                <div class="weather-icon">⛅</div>
                <div class="temperature">22°C</div>
                <p style="color: var(--gray-600);">Partly Cloudy</p>
                <p style="color: var(--gray-500); font-size: 14px;">New York, USA</p>
            </div>
        </div>

        <!-- Inbox Widget -->
        <div class="widget">
            <div class="widget-header">
                <h3><i class="fas fa-inbox"></i> Inbox</h3>
                <div class="widget-actions">
                    <button onclick="location.href='messenger.php'" title="View all"><i class="fas fa-external-link-alt"></i></button>
                </div>
            </div>
            <div class="widget-body">
                <?php if($unread_messages > 0): ?>
                    <div class="empty-state">
                        <i class="fas fa-envelope"></i>
                        <p>You have <?php echo $unread_messages; ?> unread message<?php echo $unread_messages > 1 ? 's' : ''; ?></p>
                        <a href="messenger.php" class="btn btn-primary btn-sm">View Messages</a>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <p>All caught up! No new messages.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function closeTour() {
    document.getElementById('welcomeTour').style.display = 'none';
}

function refreshWeather() {
    showAlert('Weather data refreshed!', 'success');
}
</script>

<?php include 'includes/footer.php'; ?>
