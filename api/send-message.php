<?php
session_start();
header('Content-Type: application/json');

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$message = trim($_POST['message'] ?? '');
$receiver_id = intval($_POST['receiver_id'] ?? 0);

if(empty($message) || !$receiver_id) {
    echo json_encode(['success' => false, 'message' => 'Message and receiver are required']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Insert message
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $success = $stmt->execute([$_SESSION['user_id'], $receiver_id, $message]);
    
    if($success) {
        // Log activity
        $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id) VALUES (?, 'send_message', 'message', ?)");
        $logStmt->execute([$_SESSION['user_id'], $conn->lastInsertId()]);
        
        echo json_encode(['success' => true, 'message' => 'Message sent']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
    }
} catch(PDOException $e) {
    error_log("Send message error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>
