<?php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$error = '';
$success = '';

// Get user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['update_profile'])) {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        
        if(empty($full_name) || empty($email)) {
            $error = 'Name and email are required';
        } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address';
        } else {
            // Check if email is already used by another user
            $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $checkStmt->execute([$email, $_SESSION['user_id']]);
            
            if($checkStmt->fetch()) {
                $error = 'Email is already in use';
            } else {
                $updateStmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
                if($updateStmt->execute([$full_name, $email, $_SESSION['user_id']])) {
                    $_SESSION['full_name'] = $full_name;
                    $success = 'Profile updated successfully!';
                    // Refresh user data
                    $stmt->execute([$_SESSION['user_id']]);
                    $user = $stmt->fetch();
                } else {
                    $error = 'Failed to update profile';
                }
            }
        }
    } elseif(isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if(empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = 'All password fields are required';
        } elseif($new_password !== $confirm_password) {
            $error = 'New passwords do not match';
        } elseif(strlen($new_password) < PASSWORD_MIN_LENGTH) {
            $error = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
        } elseif(!password_verify($current_password, $user['password'])) {
            $error = 'Current password is incorrect';
        } else {
            $hashed = password_hash($new_password, HASH_ALGORITHM);
            $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            if($updateStmt->execute([$hashed, $_SESSION['user_id']])) {
                $success = 'Password changed successfully!';
            } else {
                $error = 'Failed to change password';
            }
        }
    }
}

$page_title = 'Profile';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
.profile-container {
    max-width: 1000px;
    margin: 30px auto;
    padding: 0 20px;
}

.profile-header {
    background: white;
    padding: 30px;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 30px;
}

.profile-avatar-section {
    text-align: center;
}

.profile-avatar-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--primary-color);
    margin-bottom: 15px;
}

.profile-info {
    flex: 1;
}

.profile-info h1 {
    font-size: 28px;
    margin-bottom: 10px;
}

.profile-info p {
    color: var(--gray-600);
    margin-bottom: 5px;
}

.profile-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    border-bottom: 2px solid var(--gray-200);
}

.profile-tab-btn {
    padding: 12px 24px;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    font-weight: 500;
    transition: var(--transition);
}

.profile-tab-btn.active {
    border-bottom-color: var(--primary-color);
    color: var(--primary-color);
}

.profile-tab-content {
    display: none;
}

.profile-tab-content.active {
    display: block;
}

.profile-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    padding: 30px;
}

.profile-card h3 {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--gray-200);
}

@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar-section">
            <img src="<?php echo APP_URL; ?>/uploads/avatars/<?php echo $user['avatar']; ?>" 
                 alt="Profile" 
                 class="profile-avatar-large"
                 onerror="this.src='<?php echo APP_URL; ?>/assets/img/default-avatar.png'">
            <button class="btn btn-sm btn-outline">Change Photo</button>
        </div>
        <div class="profile-info">
            <h1><?php echo htmlspecialchars($user['full_name']); ?></h1>
            <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><i class="fas fa-user-tag"></i> <?php echo ucfirst($user['role']); ?></p>
            <p><i class="fas fa-calendar"></i> Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></p>
            <?php if($user['last_login']): ?>
                <p><i class="fas fa-clock"></i> Last login: <?php echo date('M d, Y h:i A', strtotime($user['last_login'])); ?></p>
            <?php endif; ?>
        </div>
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

    <div class="profile-tabs">
        <button class="profile-tab-btn active" onclick="switchProfileTab('general')">General</button>
        <button class="profile-tab-btn" onclick="switchProfileTab('security')">Security</button>
        <button class="profile-tab-btn" onclick="switchProfileTab('activity')">Activity</button>
    </div>

    <!-- General Tab -->
    <div class="profile-tab-content active" id="general-tab">
        <div class="profile-card">
            <h3>Profile Information</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" 
                           value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" 
                           value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                    <small class="text-muted">Username cannot be changed</small>
                </div>

                <button type="submit" name="update_profile" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </div>
    </div>

    <!-- Security Tab -->
    <div class="profile-tab-content" id="security-tab">
        <div class="profile-card">
            <h3>Change Password</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" name="change_password" class="btn btn-primary">
                    <i class="fas fa-key"></i> Change Password
                </button>
            </form>
        </div>
    </div>

    <!-- Activity Tab -->
    <div class="profile-tab-content" id="activity-tab">
        <div class="profile-card">
            <h3>Recent Activity</h3>
            <?php
            $activityStmt = $conn->prepare("SELECT * FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 20");
            $activityStmt->execute([$_SESSION['user_id']]);
            $activities = $activityStmt->fetchAll();
            ?>
            
            <?php if(count($activities) > 0): ?>
                <?php foreach($activities as $activity): ?>
                    <div class="activity-item" style="padding: 12px; border-bottom: 1px solid var(--gray-200);">
                        <div style="display: flex; justify-content: space-between;">
                            <div>
                                <strong><?php echo ucfirst(str_replace('_', ' ', $activity['action'])); ?></strong>
                                <br>
                                <small style="color: var(--gray-500);">
                                    IP: <?php echo htmlspecialchars($activity['ip_address']); ?>
                                </small>
                            </div>
                            <div style="color: var(--gray-600); font-size: 14px;">
                                <?php echo date('M d, Y h:i A', strtotime($activity['created_at'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No recent activity</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function switchProfileTab(tabName) {
    document.querySelectorAll('.profile-tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    document.querySelectorAll('.profile-tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    document.getElementById(tabName + '-tab').classList.add('active');
    event.target.classList.add('active');
}
</script>

<?php include 'includes/footer.php'; ?>
