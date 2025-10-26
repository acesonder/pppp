<?php
require_once '../includes/config.php';

if (!is_logged_in() || !is_admin()) {
    redirect('../login.php');
}

$user = get_current_user();
$db = Database::getInstance()->getConnection();

// Get system statistics
$stmt = $db->query("SELECT COUNT(*) as total FROM users");
$total_users = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM tasks");
$total_tasks = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM messages");
$total_messages = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM activity_log WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
$recent_activity = $stmt->fetch()['total'];

// Get recent users
$stmt = $db->query("SELECT id, username, email, full_name, role, status, created_at FROM users ORDER BY created_at DESC LIMIT 10");
$recent_users = $stmt->fetchAll();

// Get recent activity
$stmt = $db->query("
    SELECT al.*, u.username, u.full_name 
    FROM activity_log al 
    LEFT JOIN users u ON al.user_id = u.id 
    ORDER BY al.created_at DESC 
    LIMIT 20
");
$activity_logs = $stmt->fetchAll();

// Get system health
$db_size = $db->query("
    SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb 
    FROM information_schema.TABLES 
    WHERE table_schema = '" . DB_NAME . "'
")->fetch()['size_mb'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - CRUD Web App</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="main-content">
            <?php include '../includes/topbar.php'; ?>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1 class="page-title"><i class="fas fa-shield-alt"></i> Admin Panel</h1>
                    <div class="breadcrumb">
                        <i class="fas fa-home"></i> Dashboard / Admin
                    </div>
                </div>

                <!-- System Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $total_users; ?></h3>
                            <p>Total Users</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $total_tasks; ?></h3>
                            <p>Total Tasks</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $total_messages; ?></h3>
                            <p>Total Messages</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon danger">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $recent_activity; ?></h3>
                            <p>Activity (24h)</p>
                        </div>
                    </div>
                </div>

                <!-- Admin Tools -->
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-tools"></i> System Tools
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-2">
                            <a href="users.php" class="btn btn-primary">
                                <i class="fas fa-users"></i> Manage Users
                            </a>
                            <a href="diagnostics.php" class="btn btn-primary">
                                <i class="fas fa-stethoscope"></i> Diagnostics
                            </a>
                            <a href="settings.php" class="btn btn-primary">
                                <i class="fas fa-cog"></i> System Settings
                            </a>
                            <a href="backup.php" class="btn btn-primary">
                                <i class="fas fa-database"></i> Backup
                            </a>
                        </div>
                    </div>
                </div>

                <!-- System Health -->
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-heartbeat"></i> System Health
                    </div>
                    <div class="card-body">
                        <div class="widget-grid">
                            <div class="widget">
                                <h4>Database Status</h4>
                                <p><i class="fas fa-check-circle" style="color: var(--secondary-color);"></i> Connected</p>
                                <p>Size: <?php echo $db_size; ?> MB</p>
                            </div>
                            <div class="widget">
                                <h4>Server Info</h4>
                                <p>PHP Version: <?php echo phpversion(); ?></p>
                                <p>Server: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
                            </div>
                            <div class="widget">
                                <h4>Disk Usage</h4>
                                <p>Available: <?php echo round(disk_free_space('.') / 1024 / 1024 / 1024, 2); ?> GB</p>
                                <p>Total: <?php echo round(disk_total_space('.') / 1024 / 1024 / 1024, 2); ?> GB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-user-plus"></i> Recent Users
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_users as $u): ?>
                                    <tr>
                                        <td><?php echo $u['id']; ?></td>
                                        <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                                        <td><span class="badge alert-info"><?php echo ucfirst($u['role']); ?></span></td>
                                        <td><span class="badge alert-success"><?php echo ucfirst($u['status']); ?></span></td>
                                        <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Activity Log -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-history"></i> Recent Activity
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Description</th>
                                        <th>IP Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($activity_logs as $log): ?>
                                    <tr>
                                        <td><?php echo date('M d, H:i', strtotime($log['created_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($log['full_name'] ?? 'System'); ?></td>
                                        <td><code><?php echo htmlspecialchars($log['action']); ?></code></td>
                                        <td><?php echo htmlspecialchars($log['description']); ?></td>
                                        <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>
