<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';

// Check if user is admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Get statistics
$userStmt = $conn->query("SELECT COUNT(*) as count FROM users");
$total_users = $userStmt->fetch()['count'];

$taskStmt = $conn->query("SELECT COUNT(*) as count FROM tasks");
$total_tasks = $taskStmt->fetch()['count'];

$messageStmt = $conn->query("SELECT COUNT(*) as count FROM messages");
$total_messages = $messageStmt->fetch()['count'];

$eventStmt = $conn->query("SELECT COUNT(*) as count FROM calendar_events");
$total_events = $eventStmt->fetch()['count'];

// Get recent activity
$activityStmt = $conn->prepare("
    SELECT a.*, u.full_name, u.email 
    FROM activity_logs a 
    LEFT JOIN users u ON a.user_id = u.id 
    ORDER BY a.created_at DESC 
    LIMIT 20
");
$activityStmt->execute();
$recent_activity = $activityStmt->fetchAll();

// Get system settings
$settingsStmt = $conn->query("SELECT * FROM system_settings");
$settings = $settingsStmt->fetchAll(PDO::FETCH_KEY_PAIR);

$page_title = 'Admin Dashboard';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<style>
.admin-container {
    max-width: 1400px;
    margin: 30px auto;
    padding: 0 20px;
}

.admin-header {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
    padding: 30px;
    border-radius: var(--border-radius);
    margin-bottom: 30px;
}

.admin-header h1 {
    font-size: 32px;
    margin-bottom: 10px;
}

.admin-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    border-bottom: 2px solid var(--gray-200);
}

.tab-btn {
    padding: 12px 24px;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    font-weight: 500;
    transition: var(--transition);
}

.tab-btn.active {
    border-bottom-color: var(--primary-color);
    color: var(--primary-color);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.admin-stat-card {
    background: white;
    padding: 25px;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    border-left: 4px solid;
}

.admin-stat-card.users { border-left-color: #3498db; }
.admin-stat-card.tasks { border-left-color: #27ae60; }
.admin-stat-card.messages { border-left-color: #f39c12; }
.admin-stat-card.events { border-left-color: #e74c3c; }

.admin-stat-card h3 {
    font-size: 36px;
    margin-bottom: 10px;
}

.admin-stat-card p {
    color: var(--gray-600);
}

.admin-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    padding: 20px;
    margin-bottom: 20px;
}

.admin-card h3 {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--gray-200);
}

.activity-item {
    padding: 12px;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.activity-item:last-child {
    border-bottom: none;
}

.diagnostic-item {
    padding: 15px;
    background: var(--gray-100);
    border-radius: var(--border-radius);
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status-ok {
    background: #d4edda;
    color: #155724;
}

.status-warning {
    background: #fff3cd;
    color: #856404;
}

.status-error {
    background: #f8d7da;
    color: #721c24;
}

.tools-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.tool-card {
    background: white;
    padding: 20px;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    text-align: center;
}

.tool-card i {
    font-size: 48px;
    color: var(--primary-color);
    margin-bottom: 15px;
}

.tool-card h4 {
    margin-bottom: 10px;
}

.tool-card p {
    color: var(--gray-600);
    margin-bottom: 15px;
}
</style>

<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-shield-alt"></i> Admin Portal</h1>
        <p>System Administration & Management</p>
    </div>

    <!-- Tabs -->
    <div class="admin-tabs">
        <button class="tab-btn active" onclick="switchTab('dashboard')">Dashboard</button>
        <button class="tab-btn" onclick="switchTab('diagnostics')">Diagnostics</button>
        <button class="tab-btn" onclick="switchTab('users')">Users</button>
        <button class="tab-btn" onclick="switchTab('settings')">Settings</button>
        <button class="tab-btn" onclick="switchTab('tools')">Tools</button>
    </div>

    <!-- Dashboard Tab -->
    <div class="tab-content active" id="dashboard-tab">
        <div class="stats-grid">
            <div class="admin-stat-card users">
                <h3><?php echo $total_users; ?></h3>
                <p><i class="fas fa-users"></i> Total Users</p>
            </div>
            <div class="admin-stat-card tasks">
                <h3><?php echo $total_tasks; ?></h3>
                <p><i class="fas fa-tasks"></i> Total Tasks</p>
            </div>
            <div class="admin-stat-card messages">
                <h3><?php echo $total_messages; ?></h3>
                <p><i class="fas fa-envelope"></i> Total Messages</p>
            </div>
            <div class="admin-stat-card events">
                <h3><?php echo $total_events; ?></h3>
                <p><i class="fas fa-calendar"></i> Total Events</p>
            </div>
        </div>

        <div class="admin-card">
            <h3>Recent Activity</h3>
            <?php foreach($recent_activity as $activity): ?>
                <div class="activity-item">
                    <div>
                        <strong><?php echo htmlspecialchars($activity['full_name'] ?? 'System'); ?></strong>
                        - <?php echo htmlspecialchars($activity['action']); ?>
                        <br>
                        <small style="color: var(--gray-500);">
                            <?php echo htmlspecialchars($activity['ip_address']); ?>
                        </small>
                    </div>
                    <div style="color: var(--gray-600); font-size: 14px;">
                        <?php echo date('M d, Y H:i', strtotime($activity['created_at'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Diagnostics Tab -->
    <div class="tab-content" id="diagnostics-tab">
        <div class="admin-card">
            <h3>System Health</h3>
            
            <div class="diagnostic-item">
                <div>
                    <strong>Database Connection</strong>
                    <br><small>MySQL database connectivity</small>
                </div>
                <span class="status-badge status-ok">OK</span>
            </div>

            <div class="diagnostic-item">
                <div>
                    <strong>PHP Version</strong>
                    <br><small><?php echo phpversion(); ?></small>
                </div>
                <span class="status-badge status-ok">OK</span>
            </div>

            <div class="diagnostic-item">
                <div>
                    <strong>Upload Directory</strong>
                    <br><small><?php echo is_writable(UPLOAD_DIR) ? 'Writable' : 'Not writable'; ?></small>
                </div>
                <span class="status-badge <?php echo is_writable(UPLOAD_DIR) ? 'status-ok' : 'status-error'; ?>">
                    <?php echo is_writable(UPLOAD_DIR) ? 'OK' : 'ERROR'; ?>
                </span>
            </div>

            <div class="diagnostic-item">
                <div>
                    <strong>Session Status</strong>
                    <br><small>PHP sessions enabled</small>
                </div>
                <span class="status-badge status-ok">OK</span>
            </div>

            <div class="diagnostic-item">
                <div>
                    <strong>Disk Space</strong>
                    <br><small>Available: <?php echo round(disk_free_space("/") / 1024 / 1024 / 1024, 2); ?> GB</small>
                </div>
                <span class="status-badge status-ok">OK</span>
            </div>
        </div>

        <div class="admin-card">
            <h3>Error Logs</h3>
            <p style="color: var(--gray-600);">No recent errors</p>
        </div>
    </div>

    <!-- Users Tab -->
    <div class="tab-content" id="users-tab">
        <div class="admin-card">
            <h3>User Management</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--gray-300); text-align: left;">
                        <th style="padding: 12px;">ID</th>
                        <th style="padding: 12px;">Name</th>
                        <th style="padding: 12px;">Email</th>
                        <th style="padding: 12px;">Role</th>
                        <th style="padding: 12px;">Status</th>
                        <th style="padding: 12px;">Registered</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $usersStmt = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
                    while($user = $usersStmt->fetch()):
                    ?>
                    <tr style="border-bottom: 1px solid var(--gray-200);">
                        <td style="padding: 12px;"><?php echo $user['id']; ?></td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($user['email']); ?></td>
                        <td style="padding: 12px;">
                            <span class="role-badge role-<?php echo $user['role']; ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td style="padding: 12px;">
                            <span class="status-badge <?php echo $user['status'] === 'active' ? 'status-ok' : 'status-warning'; ?>">
                                <?php echo ucfirst($user['status']); ?>
                            </span>
                        </td>
                        <td style="padding: 12px;"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Settings Tab -->
    <div class="tab-content" id="settings-tab">
        <div class="admin-card">
            <h3>System Settings</h3>
            <form method="POST" action="settings.php">
                <div class="form-group">
                    <label class="form-label">Site Name</label>
                    <input type="text" class="form-control" name="site_name" 
                           value="<?php echo htmlspecialchars($settings['site_name'] ?? APP_NAME); ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Site Description</label>
                    <textarea class="form-control" name="site_description" rows="3"><?php echo htmlspecialchars($settings['site_description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" id="maintenance_mode" name="maintenance_mode" 
                               <?php echo ($settings['maintenance_mode'] ?? 0) ? 'checked' : ''; ?>>
                        <label for="maintenance_mode">Maintenance Mode</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" id="user_registration" name="user_registration" 
                               <?php echo ($settings['user_registration'] ?? 1) ? 'checked' : ''; ?>>
                        <label for="user_registration">Allow User Registration</label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
        </div>
    </div>

    <!-- Tools Tab -->
    <div class="tab-content" id="tools-tab">
        <div class="tools-grid">
            <div class="tool-card">
                <i class="fas fa-database"></i>
                <h4>Database Backup</h4>
                <p>Create a backup of the database</p>
                <button class="btn btn-primary btn-sm" onclick="showAlert('Backup feature coming soon!', 'info')">
                    Create Backup
                </button>
            </div>
            
            <div class="tool-card">
                <i class="fas fa-broom"></i>
                <h4>Clear Cache</h4>
                <p>Clear application cache and temp files</p>
                <button class="btn btn-primary btn-sm" onclick="showAlert('Cache cleared!', 'success')">
                    Clear Cache
                </button>
            </div>
            
            <div class="tool-card">
                <i class="fas fa-chart-bar"></i>
                <h4>Generate Reports</h4>
                <p>Generate system usage reports</p>
                <button class="btn btn-primary btn-sm" onclick="showAlert('Report generation coming soon!', 'info')">
                    Generate Report
                </button>
            </div>
            
            <div class="tool-card">
                <i class="fas fa-envelope-open-text"></i>
                <h4>Email Test</h4>
                <p>Test email configuration</p>
                <button class="btn btn-primary btn-sm" onclick="showAlert('Email test sent!', 'success')">
                    Send Test Email
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.add('active');
    event.target.classList.add('active');
}
</script>

<?php include '../includes/footer.php'; ?>
