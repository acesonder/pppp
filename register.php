<?php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';

// Redirect if already logged in
if(isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if(empty($username) || empty($email) || empty($full_name) || empty($password)) {
        $error = 'Please fill in all fields';
    } elseif($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif(strlen($password) < PASSWORD_MIN_LENGTH) {
        $error = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } else {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            // Check if username exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if($stmt->fetch()) {
                $error = 'Username already exists';
            } else {
                // Check if email exists
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if($stmt->fetch()) {
                    $error = 'Email already exists';
                } else {
                    // Create user
                    $hashed_password = password_hash($password, HASH_ALGORITHM);
                    $stmt = $conn->prepare("INSERT INTO users (username, email, full_name, password, role) VALUES (?, ?, ?, ?, 'user')");
                    
                    if($stmt->execute([$username, $email, $full_name, $hashed_password])) {
                        $user_id = $conn->lastInsertId();
                        
                        // Create default dashboard widgets
                        $defaultWidgets = ['tasks', 'calendar', 'weather', 'inbox'];
                        $widgetStmt = $conn->prepare("INSERT INTO dashboard_widgets (user_id, widget_type, widget_position) VALUES (?, ?, ?)");
                        foreach($defaultWidgets as $index => $widget) {
                            $widgetStmt->execute([$user_id, $widget, $index]);
                        }
                        
                        // Log activity
                        $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, ip_address, user_agent) VALUES (?, 'register', ?, ?)");
                        $logStmt->execute([$user_id, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
                        
                        $success = 'Registration successful! You can now login.';
                        
                        // Auto login
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['username'] = $username;
                        $_SESSION['role'] = 'user';
                        $_SESSION['full_name'] = $full_name;
                        
                        header('Location: dashboard.php?welcome=1');
                        exit();
                    } else {
                        $error = 'Registration failed. Please try again.';
                    }
                }
            }
        } catch(PDOException $e) {
            $error = 'An error occurred. Please try again.';
            error_log("Registration error: " . $e->getMessage());
        }
    }
}

$page_title = 'Register';
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
            <h1>Create Account</h1>
            <p>Join us today!</p>
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
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="auth-form" id="registerForm">
            <div class="form-group">
                <label for="full_name" class="form-label">Full Name</label>
                <i class="fas fa-user input-icon"></i>
                <input type="text" 
                       class="form-control" 
                       id="full_name" 
                       name="full_name" 
                       placeholder="Enter your full name"
                       value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <i class="fas fa-user-circle input-icon"></i>
                <input type="text" 
                       class="form-control" 
                       id="username" 
                       name="username" 
                       placeholder="Choose a username"
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <i class="fas fa-envelope input-icon"></i>
                <input type="email" 
                       class="form-control" 
                       id="email" 
                       name="email" 
                       placeholder="Enter your email"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <i class="fas fa-lock input-icon"></i>
                <input type="password" 
                       class="form-control" 
                       id="password" 
                       name="password" 
                       placeholder="Choose a password"
                       required>
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <i class="fas fa-lock input-icon"></i>
                <input type="password" 
                       class="form-control" 
                       id="confirm_password" 
                       name="confirm_password" 
                       placeholder="Confirm your password"
                       required>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 20px;">
                Create Account
            </button>
        </form>

        <div class="auth-footer">
            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
