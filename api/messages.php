<?php
session_start();
header('Content-Type: application/json');

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0, 'messages' => []]);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Get unread messages count
    $countStmt = $conn->prepare("SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0");
    $countStmt->execute([$_SESSION['user_id']]);
    $count = $countStmt->fetch()['count'];
    
    // Get recent unread messages
    $stmt = $conn->prepare("
        SELECT m.*, u.full_name as sender, u.avatar 
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE m.receiver_id = ? AND m.is_read = 0
        ORDER BY m.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $messages = $stmt->fetchAll();
    
    $result = [];
    foreach($messages as $msg) {
        $result[] = [
            'sender' => $msg['sender'],
            'message' => substr($msg['message'], 0, 50) . '...',
            'avatar' => APP_URL . '/uploads/avatars/' . $msg['avatar'],
            'time' => date('h:i A', strtotime($msg['created_at']))
        ];
    }
    
    echo json_encode([
        'count' => $count,
        'messages' => $result
    ]);
    
} catch(PDOException $e) {
    error_log("Messages error: " . $e->getMessage());
    echo json_encode(['count' => 0, 'messages' => []]);
}
?>
