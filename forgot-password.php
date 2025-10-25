<?php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';

if(isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';
$step = 1;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['email'])) {
        // Step 1: Send reset link
        $email = trim($_POST['email']);
        
        if(empty($email)) {
            $error = 'Please enter your email';
        } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address';
        } else {
            try {
                $db = new Database();
                $conn = $db->getConnection();
                
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if($user) {
                    // Generate reset token
                    $token = bin2hex(random_bytes(32));
                    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    
                    // Delete old tokens
                    $deleteStmt = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
                    $deleteStmt->execute([$user['id']]);
                    
                    // Insert new token
                    $insertStmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
                    $insertStmt->execute([$user['id'], $token, $expires]);
                    
                    // In production, send email with reset link
                    $reset_link = APP_URL . "/reset-password.php?token=" . $token;
                    
                    // For demo purposes, show the link
                    $success = "Password reset instructions have been sent to your email. <br><small>For demo: <a href='$reset_link'>$reset_link</a></small>";
                } else {
                    $success = "If an account exists with that email, you will receive password reset instructions.";
                }
            } catch(PDOException $e) {
                $error = 'An error occurred. Please try again.';
                error_log("Forgot password error: " . $e->getMessage());
            }
        }
    }
}

$page_title = 'Forgot Password';
include 'includes/header.php';
?>

<style>
.auth-container {
    min-height: calc(100vh - 70px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.auth-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    width: 100%;
    max-width: 450px;
    padding: 40px;
}

.auth-header {
    text-align: center;
    margin-bottom: 30px;
}

.auth-header h1 {
    font-size: 28px;
    color: var(--dark-color);
    margin-bottom: 10px;
}

.auth-header p {
    color: var(--gray-600);
}

.auth-form .form-group {
    position: relative;
}

.auth-form .form-control {
    padding-left: 45px;
}

.auth-form .input-icon {
    position: absolute;
    left: 15px;
    top: 42px;
    color: var(--gray-500);
}

.auth-footer {
    text-align: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--gray-200);
}

.auth-footer a {
    color: var(--primary-color);
    font-weight: 600;
}
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Forgot Password?</h1>
            <p>Enter your email to reset your password</p>
        </div>

        <?php if($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="auth-form">
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <i class="fas fa-envelope input-icon"></i>
                <input type="email" 
                       class="form-control" 
                       id="email" 
                       name="email" 
                       placeholder="Enter your email"
                       required>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 20px;">
                Send Reset Link
            </button>
        </form>

        <div class="auth-footer">
            <p>Remember your password? <a href="login.php">Login</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
