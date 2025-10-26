<?php
require_once '../includes/config.php';

if (!is_logged_in() || !is_admin()) {
    redirect('../login.php');
}

$user = get_current_user();
$db = Database::getInstance()->getConnection();

// System diagnostics
$diagnostics = [];

// PHP Configuration
$diagnostics['php'] = [
    'version' => phpversion(),
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'display_errors' => ini_get('display_errors') ? 'On' : 'Off',
];

// Database diagnostics
try {
    $stmt = $db->query("SELECT VERSION() as version");
    $mysql_version = $stmt->fetch()['version'];
    $db_status = 'Connected';
    $db_error = null;
} catch (Exception $e) {
    $mysql_version = 'Unknown';
    $db_status = 'Error';
    $db_error = $e->getMessage();
}

$diagnostics['database'] = [
    'status' => $db_status,
    'version' => $mysql_version,
    'error' => $db_error,
    'host' => DB_HOST,
    'name' => DB_NAME,
];

// Check table existence
$required_tables = ['users', 'tasks', 'messages', 'calendar_events', 'user_preferences', 'activity_log', 'system_settings'];
$table_status = [];

foreach ($required_tables as $table) {
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch()['count'];
        $table_status[$table] = ['exists' => true, 'count' => $count];
    } catch (Exception $e) {
        $table_status[$table] = ['exists' => false, 'error' => $e->getMessage()];
    }
}

// Server info
$diagnostics['server'] = [
    'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'os' => PHP_OS,
    'hostname' => gethostname(),
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
];

// Disk space
$diagnostics['disk'] = [
    'free_space' => round(disk_free_space('.') / 1024 / 1024 / 1024, 2) . ' GB',
    'total_space' => round(disk_total_space('.') / 1024 / 1024 / 1024, 2) . ' GB',
];

// PHP Extensions
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'session'];
$extension_status = [];

foreach ($required_extensions as $ext) {
    $extension_status[$ext] = extension_loaded($ext);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostics - Admin Panel</title>
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
                    <h1 class="page-title"><i class="fas fa-stethoscope"></i> System Diagnostics</h1>
                    <div class="breadcrumb">
                        <i class="fas fa-home"></i> Dashboard / Admin / Diagnostics
                    </div>
                </div>

                <!-- PHP Configuration -->
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-code"></i> PHP Configuration
                    </div>
                    <div class="card-body">
                        <table>
                            <?php foreach ($diagnostics['php'] as $key => $value): ?>
                            <tr>
                                <td><strong><?php echo ucwords(str_replace('_', ' ', $key)); ?></strong></td>
                                <td><?php echo htmlspecialchars($value); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>

                <!-- Database Status -->
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-database"></i> Database Status
                    </div>
                    <div class="card-body">
                        <table>
                            <?php foreach ($diagnostics['database'] as $key => $value): ?>
                            <?php if ($value): ?>
                            <tr>
                                <td><strong><?php echo ucwords(str_replace('_', ' ', $key)); ?></strong></td>
                                <td>
                                    <?php if ($key === 'status'): ?>
                                        <?php if ($value === 'Connected'): ?>
                                            <span class="badge alert-success"><i class="fas fa-check"></i> <?php echo $value; ?></span>
                                        <?php else: ?>
                                            <span class="badge alert-error"><i class="fas fa-times"></i> <?php echo $value; ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($value); ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>

                <!-- Table Status -->
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-table"></i> Database Tables
                    </div>
                    <div class="card-body">
                        <table>
                            <thead>
                                <tr>
                                    <th>Table Name</th>
                                    <th>Status</th>
                                    <th>Record Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($table_status as $table => $status): ?>
                                <tr>
                                    <td><code><?php echo $table; ?></code></td>
                                    <td>
                                        <?php if ($status['exists']): ?>
                                            <span class="badge alert-success"><i class="fas fa-check"></i> Exists</span>
                                        <?php else: ?>
                                            <span class="badge alert-error"><i class="fas fa-times"></i> Missing</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $status['exists'] ? $status['count'] : 'N/A'; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- PHP Extensions -->
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-puzzle-piece"></i> PHP Extensions
                    </div>
                    <div class="card-body">
                        <table>
                            <thead>
                                <tr>
                                    <th>Extension</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($extension_status as $ext => $loaded): ?>
                                <tr>
                                    <td><code><?php echo $ext; ?></code></td>
                                    <td>
                                        <?php if ($loaded): ?>
                                            <span class="badge alert-success"><i class="fas fa-check"></i> Loaded</span>
                                        <?php else: ?>
                                            <span class="badge alert-error"><i class="fas fa-times"></i> Not Loaded</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Server Info -->
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-server"></i> Server Information
                    </div>
                    <div class="card-body">
                        <table>
                            <?php foreach ($diagnostics['server'] as $key => $value): ?>
                            <tr>
                                <td><strong><?php echo ucwords(str_replace('_', ' ', $key)); ?></strong></td>
                                <td><?php echo htmlspecialchars($value); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>

                <!-- Disk Space -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-hdd"></i> Disk Space
                    </div>
                    <div class="card-body">
                        <table>
                            <?php foreach ($diagnostics['disk'] as $key => $value): ?>
                            <tr>
                                <td><strong><?php echo ucwords(str_replace('_', ' ', $key)); ?></strong></td>
                                <td><?php echo htmlspecialchars($value); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>
