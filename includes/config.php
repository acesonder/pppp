<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'crud_webapp');

// Application Configuration
define('APP_NAME', 'CRUD Web App');
define('APP_URL', 'http://localhost');
define('UPLOAD_PATH', __DIR__ . '/../assets/images/uploads/');

// Security Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour
define('PASSWORD_MIN_LENGTH', 6);
define('MAX_LOGIN_ATTEMPTS', 5);

// Timezone
date_default_timezone_set('UTC');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Connection
class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                )
            );
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
}

// Helper Functions
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function get_current_user() {
    if (!is_logged_in()) {
        return null;
    }
    
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT id, username, email, full_name, role, avatar FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function log_activity($action, $description = '', $user_id = null) {
    if ($user_id === null && isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }
    
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("INSERT INTO activity_log (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $user_id,
        $action,
        $description,
        $_SERVER['REMOTE_ADDR'] ?? '',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
}

function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}
?>
