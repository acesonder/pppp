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

// Get user's widget configuration
$widgetStmt = $conn->prepare("SELECT * FROM dashboard_widgets WHERE user_id = ? ORDER BY widget_position");
$widgetStmt->execute([$_SESSION['user_id']]);
$widgets = $widgetStmt->fetchAll();

// Handle settings updates
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['update_widgets'])) {
        // Update widget visibility
        $widget_settings = $_POST['widgets'] ?? [];
        
        foreach($widgets as $widget) {
            $is_visible = isset($widget_settings[$widget['widget_type']]) ? 1 : 0;
            $updateStmt = $conn->prepare("UPDATE dashboard_widgets SET is_visible = ? WHERE id = ?");
            $updateStmt->execute([$is_visible, $widget['id']]);
        }
        
        $success = 'Widget settings updated!';
    }
}

$page_title = 'Settings';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
.settings-container {
    max-width: 1000px;
    margin: 30px auto;
    padding: 0 20px;
}

.settings-header {
    margin-bottom: 30px;
}

.settings-header h1 {
    font-size: 32px;
    color: var(--dark-color);
}

.settings-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    padding: 30px;
    margin-bottom: 20px;
}

.settings-card h3 {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--gray-200);
}

.widget-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: var(--gray-100);
    border-radius: var(--border-radius);
    margin-bottom: 10px;
}

.widget-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.widget-icon {
    width: 40px;
    height: 40px;
    background: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--gray-400);
    transition: .4s;
    border-radius: 24px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .toggle-slider {
    background-color: var(--primary-color);
}

input:checked + .toggle-slider:before {
    transform: translateX(26px);
}

.preference-item {
    padding: 15px 0;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.preference-item:last-child {
    border-bottom: none;
}
</style>

<div class="settings-container">
    <div class="settings-header">
        <h1><i class="fas fa-cog"></i> Settings</h1>
        <p>Customize your PPPP experience</p>
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

    <!-- Dashboard Widgets -->
    <div class="settings-card">
        <h3>Dashboard Widgets</h3>
        <p class="text-muted" style="margin-bottom: 20px;">
            Choose which widgets to display on your dashboard
        </p>
        
        <form method="POST">
            <?php
            $widget_icons = [
                'tasks' => 'fa-tasks',
                'calendar' => 'fa-calendar',
                'weather' => 'fa-cloud-sun',
                'inbox' => 'fa-inbox'
            ];
            
            foreach($widgets as $widget):
                $icon = $widget_icons[$widget['widget_type']] ?? 'fa-puzzle-piece';
            ?>
                <div class="widget-item">
                    <div class="widget-info">
                        <div class="widget-icon">
                            <i class="fas <?php echo $icon; ?>"></i>
                        </div>
                        <div>
                            <strong><?php echo ucfirst($widget['widget_type']); ?></strong>
                            <br>
                            <small class="text-muted">
                                <?php
                                $descriptions = [
                                    'tasks' => 'View and manage your tasks',
                                    'calendar' => 'See upcoming events',
                                    'weather' => 'Check current weather',
                                    'inbox' => 'Quick access to messages'
                                ];
                                echo $descriptions[$widget['widget_type']] ?? 'Widget';
                                ?>
                            </small>
                        </div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" 
                               name="widgets[<?php echo $widget['widget_type']; ?>]" 
                               <?php echo $widget['is_visible'] ? 'checked' : ''; ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            <?php endforeach; ?>
            
            <button type="submit" name="update_widgets" class="btn btn-primary" style="margin-top: 20px;">
                <i class="fas fa-save"></i> Save Widget Settings
            </button>
        </form>
    </div>

    <!-- Appearance Settings -->
    <div class="settings-card">
        <h3>Appearance</h3>
        
        <div class="preference-item">
            <div>
                <strong>Dark Mode</strong>
                <br>
                <small class="text-muted">Switch between light and dark theme</small>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" id="darkModeToggle">
                <span class="toggle-slider"></span>
            </label>
        </div>
    </div>

    <!-- Notification Settings -->
    <div class="settings-card">
        <h3>Notifications</h3>
        
        <div class="preference-item">
            <div>
                <strong>Email Notifications</strong>
                <br>
                <small class="text-muted">Receive email updates for important events</small>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" checked>
                <span class="toggle-slider"></span>
            </label>
        </div>
        
        <div class="preference-item">
            <div>
                <strong>Task Reminders</strong>
                <br>
                <small class="text-muted">Get notified about upcoming task deadlines</small>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" checked>
                <span class="toggle-slider"></span>
            </label>
        </div>
        
        <div class="preference-item">
            <div>
                <strong>Message Notifications</strong>
                <br>
                <small class="text-muted">Alert when you receive new messages</small>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" checked>
                <span class="toggle-slider"></span>
            </label>
        </div>
    </div>

    <!-- Privacy Settings -->
    <div class="settings-card">
        <h3>Privacy & Security</h3>
        
        <div class="preference-item">
            <div>
                <strong>Profile Visibility</strong>
                <br>
                <small class="text-muted">Make your profile visible to other users</small>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" checked>
                <span class="toggle-slider"></span>
            </label>
        </div>
        
        <div class="preference-item">
            <div>
                <strong>Activity Status</strong>
                <br>
                <small class="text-muted">Show when you're online</small>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" checked>
                <span class="toggle-slider"></span>
            </label>
        </div>
    </div>

    <!-- Account Actions -->
    <div class="settings-card">
        <h3>Account</h3>
        
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="profile.php" class="btn btn-outline">
                <i class="fas fa-user"></i> Edit Profile
            </a>
            <button class="btn btn-outline" onclick="showAlert('Export feature coming soon!', 'info')">
                <i class="fas fa-download"></i> Export Data
            </button>
            <button class="btn btn-danger" onclick="confirmAction('Are you sure you want to delete your account? This cannot be undone.', function() { showAlert('Account deletion coming soon', 'info'); })">
                <i class="fas fa-trash"></i> Delete Account
            </button>
        </div>
    </div>
</div>

<script>
// Dark mode toggle
document.getElementById('darkModeToggle').addEventListener('change', function() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', this.checked);
});

// Load dark mode preference
if(localStorage.getItem('darkMode') === 'true') {
    document.getElementById('darkModeToggle').checked = true;
    document.body.classList.add('dark-mode');
}
</script>

<?php include 'includes/footer.php'; ?>
