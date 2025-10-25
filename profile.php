<?php
require_once 'includes/config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user = get_current_user();
$db = Database::getInstance()->getConnection();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize_input($_POST['full_name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($full_name) || empty($email)) {
        $error = 'Name and email are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } else {
        // Update profile
        $stmt = $db->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
        if ($stmt->execute([$full_name, $email, $_SESSION['user_id']])) {
            $success = 'Profile updated successfully';
            
            // Update password if provided
            if (!empty($current_password) && !empty($new_password)) {
                $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $stored_password = $stmt->fetch()['password'];
                
                if (password_verify($current_password, $stored_password)) {
                    if ($new_password === $confirm_password) {
                        if (strlen($new_password) >= PASSWORD_MIN_LENGTH) {
                            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                            $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                            $stmt->execute([$hashed, $_SESSION['user_id']]);
                            $success .= ' and password changed';
                            log_activity('password_change', 'User changed password');
                        } else {
                            $error = 'New password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
                        }
                    } else {
                        $error = 'New passwords do not match';
                    }
                } else {
                    $error = 'Current password is incorrect';
                }
            }
            
            log_activity('profile_update', 'Updated profile information');
        } else {
            $error = 'Failed to update profile';
        }
    }
    
    // Reload user data
    $user = get_current_user();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - CRUD Web App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <?php include 'includes/topbar.php'; ?>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1 class="page-title">My Profile</h1>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-user"></i> Profile Information
                    </div>
                    <div class="card-body">
                        <form method="POST" action="profile.php">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" 
                                       value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                <small>Username cannot be changed</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="full_name">Full Name *</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="role">Role</label>
                                <input type="text" class="form-control" id="role" 
                                       value="<?php echo ucfirst($user['role']); ?>" disabled>
                            </div>
                            
                            <hr>
                            
                            <h3>Change Password</h3>
                            <p><small>Leave blank to keep current password</small></p>
                            
                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password">
                            </div>
                            
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>
