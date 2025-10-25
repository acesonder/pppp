<?php
session_start();
header('Content-Type: application/json');

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0, 'notifications' => []]);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Get recent activity logs as notifications
    $stmt = $conn->prepare("
        SELECT action, created_at 
        FROM activity_logs 
        WHERE user_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $logs = $stmt->fetchAll();
    
    $notifications = [];
    foreach($logs as $log) {
        $icon = 'bell';
        $message = ucfirst(str_replace('_', ' ', $log['action']));
        
        if(strpos($log['action'], 'task') !== false) $icon = 'tasks';
        elseif(strpos($log['action'], 'message') !== false) $icon = 'envelope';
        elseif(strpos($log['action'], 'calendar') !== false) $icon = 'calendar';
        
        $notifications[] = [
            'icon' => $icon,
            'message' => $message,
            'time' => date('h:i A', strtotime($log['created_at']))
        ];
    }
    
    echo json_encode([
        'count' => count($notifications),
        'notifications' => $notifications
    ]);
    
} catch(PDOException $e) {
    error_log("Notifications error: " . $e->getMessage());
    echo json_encode(['count' => 0, 'notifications' => []]);
}
?>
