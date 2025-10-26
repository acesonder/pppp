<?php
require_once 'includes/config.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, username, password, role, status FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== 'active') {
                $error = 'Your account has been ' . $user['status'];
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                // Update last login
                $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $stmt->execute([$user['id']]);
                
                log_activity('login', 'User logged in successfully');
                
                redirect('dashboard.php');
            }
        } else {
            $error = 'Invalid username or password';
            log_activity('login_failed', 'Failed login attempt for: ' . $username, null);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CRUD Web App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
        }
        
        .auth-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 400px;
            width: 100%;
            padding: 2.5rem;
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-header h1 {
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .auth-header p {
            color: #666;
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
        }
        
        .auth-footer a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1><i class="fas fa-cube"></i> CRUD Web App</h1>
                <p>Sign in to your account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="login.php" id="loginForm">
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div class="auth-footer">
                <p><a href="forgot-password.php">Forgot your password?</a></p>
                <p>Don't have an account? <a href="register.php">Sign up</a></p>
                <p><a href="index.html">Back to Home</a></p>
            </div>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
