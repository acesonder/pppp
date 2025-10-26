<?php
require_once 'includes/config.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Please enter your email address';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } else {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, username FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate reset token
            $reset_token = bin2hex(random_bytes(32));
            $reset_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $db->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
            $stmt->execute([$reset_token, $reset_expiry, $user['id']]);
            
            log_activity('password_reset_request', 'Password reset requested', $user['id']);
            
            // In a real application, you would send an email here
            $success = 'Password reset instructions have been sent to your email. Check the console for the reset link (demo mode).';
            
            // Demo: Show reset link in console (in production, send via email)
            error_log("Password Reset Link: " . APP_URL . "/reset-password.php?token=" . $reset_token);
        } else {
            // Security: Don't reveal if email exists
            $success = 'If your email is registered, you will receive password reset instructions.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - CRUD Web App</title>
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
                <h1><i class="fas fa-key"></i> Forgot Password</h1>
                <p>Enter your email to reset your password</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="forgot-password.php">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-paper-plane"></i> Send Reset Link
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Remember your password? <a href="login.php">Sign in</a></p>
                <p><a href="index.html">Back to Home</a></p>
            </div>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
