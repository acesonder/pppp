<?php
/**
 * PPPP Setup Verification Script
 * Run this file to check if your installation is correct
 */

$errors = [];
$warnings = [];
$success = [];

// Check PHP version
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    $success[] = "✅ PHP version: " . PHP_VERSION;
} else {
    $errors[] = "❌ PHP version must be 7.4 or higher. Current: " . PHP_VERSION;
}

// Check required extensions
$required_extensions = ['pdo', 'pdo_mysql', 'session', 'json', 'mbstring'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        $success[] = "✅ Extension '$ext' is loaded";
    } else {
        $errors[] = "❌ Extension '$ext' is required but not loaded";
    }
}

// Check config file
if (file_exists('config/config.php')) {
    $success[] = "✅ Configuration file exists";
    require_once 'config/config.php';
    
    // Check database connection
    try {
        require_once 'config/database.php';
        $db = new Database();
        $conn = $db->getConnection();
        $success[] = "✅ Database connection successful";
        
        // Check if tables exist
        $tables = ['users', 'tasks', 'calendar_events', 'messages', 'dashboard_widgets'];
        foreach ($tables as $table) {
            $stmt = $conn->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                $success[] = "✅ Table '$table' exists";
            } else {
                $errors[] = "❌ Table '$table' does not exist";
            }
        }
        
        // Check if admin user exists
        $stmt = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
        $result = $stmt->fetch();
        if ($result['count'] > 0) {
            $success[] = "✅ Admin user exists";
        } else {
            $warnings[] = "⚠️ No admin user found. Run demo-data.sql";
        }
        
    } catch (Exception $e) {
        $errors[] = "❌ Database connection failed: " . $e->getMessage();
    }
} else {
    $errors[] = "❌ Configuration file not found";
}

// Check directories
$directories = [
    'uploads' => 'Uploads directory',
    'uploads/avatars' => 'Avatars directory',
    'assets/css' => 'CSS directory',
    'assets/js' => 'JS directory'
];

foreach ($directories as $dir => $name) {
    if (is_dir($dir)) {
        $success[] = "✅ $name exists";
        if (is_writable($dir)) {
            $success[] = "✅ $name is writable";
        } else {
            $warnings[] = "⚠️ $name is not writable";
        }
    } else {
        $errors[] = "❌ $name does not exist";
    }
}

// Check important files
$files = [
    'index.php' => 'Landing page',
    'login.php' => 'Login page',
    'dashboard.php' => 'Dashboard',
    'tasks.php' => 'Tasks page',
    'calendar.php' => 'Calendar page',
    'messenger.php' => 'Messenger',
    'admin/index.php' => 'Admin portal'
];

foreach ($files as $file => $name) {
    if (file_exists($file)) {
        $success[] = "✅ $name exists";
    } else {
        $errors[] = "❌ $name not found";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPPP Setup Verification</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 32px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        
        .message {
            padding: 10px 15px;
            margin-bottom: 8px;
            border-radius: 5px;
            line-height: 1.5;
        }
        
        .success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
        }
        
        .error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        
        .summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-top: 30px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .summary-item:last-child {
            margin-bottom: 0;
        }
        
        .badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .badge-success {
            background: #28a745;
            color: white;
        }
        
        .badge-warning {
            background: #ffc107;
            color: #333;
        }
        
        .badge-danger {
            background: #dc3545;
            color: white;
        }
        
        .actions {
            margin-top: 30px;
            text-align: center;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 PPPP Setup Verification</h1>
        <p class="subtitle">Checking your installation...</p>
        
        <?php if (count($success) > 0): ?>
        <div class="section">
            <h2>✅ Successful Checks (<?php echo count($success); ?>)</h2>
            <?php foreach ($success as $msg): ?>
                <div class="message success"><?php echo htmlspecialchars($msg); ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if (count($warnings) > 0): ?>
        <div class="section">
            <h2>⚠️ Warnings (<?php echo count($warnings); ?>)</h2>
            <?php foreach ($warnings as $msg): ?>
                <div class="message warning"><?php echo htmlspecialchars($msg); ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if (count($errors) > 0): ?>
        <div class="section">
            <h2>❌ Errors (<?php echo count($errors); ?>)</h2>
            <?php foreach ($errors as $msg): ?>
                <div class="message error"><?php echo htmlspecialchars($msg); ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <div class="summary">
            <h2 style="margin-bottom: 20px;">📊 Summary</h2>
            <div class="summary-item">
                <span>Successful Checks:</span>
                <span class="badge badge-success"><?php echo count($success); ?></span>
            </div>
            <div class="summary-item">
                <span>Warnings:</span>
                <span class="badge badge-warning"><?php echo count($warnings); ?></span>
            </div>
            <div class="summary-item">
                <span>Errors:</span>
                <span class="badge badge-danger"><?php echo count($errors); ?></span>
            </div>
        </div>
        
        <?php if (count($errors) === 0): ?>
        <div class="actions">
            <p style="margin-bottom: 20px; color: #28a745; font-weight: 600;">
                🎉 Your PPPP installation is ready!
            </p>
            <a href="index.php" class="btn">Go to Application</a>
        </div>
        <?php else: ?>
        <div class="actions">
            <p style="margin-bottom: 20px; color: #dc3545; font-weight: 600;">
                ⚠️ Please fix the errors above before proceeding
            </p>
            <a href="INSTALLATION.md" class="btn">View Installation Guide</a>
        </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; color: #666; font-size: 14px;">
            <p>PPPP v1.0.0 - Professional Project & Portfolio Platform</p>
        </div>
    </div>
</body>
</html>
