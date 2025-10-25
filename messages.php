<?php
require_once 'includes/config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user = get_current_user();
$db = Database::getInstance()->getConnection();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    
    switch ($action) {
        case 'send_message':
            $recipient_id = intval($_POST['recipient_id'] ?? 0);
            $message = sanitize_input($_POST['message'] ?? '');
            
            if (empty($message) || $recipient_id === 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid message']);
                exit;
            }
            
            $stmt = $db->prepare("INSERT INTO messages (sender_id, recipient_id, message) VALUES (?, ?, ?)");
            if ($stmt->execute([$_SESSION['user_id'], $recipient_id, $message])) {
                log_activity('message_sent', 'Sent message to user ID: ' . $recipient_id);
                echo json_encode(['success' => true, 'message' => 'Message sent']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send message']);
            }
            exit;
            
        case 'get_messages':
            $contact_id = intval($_POST['contact_id'] ?? 0);
            
            $stmt = $db->prepare("
                SELECT m.*, 
                       s.username as sender_username, s.full_name as sender_name,
                       r.username as recipient_username, r.full_name as recipient_name
                FROM messages m
                JOIN users s ON m.sender_id = s.id
                JOIN users r ON m.recipient_id = r.id
                WHERE (m.sender_id = ? AND m.recipient_id = ?)
                   OR (m.sender_id = ? AND m.recipient_id = ?)
                ORDER BY m.created_at ASC
            ");
            $stmt->execute([$_SESSION['user_id'], $contact_id, $contact_id, $_SESSION['user_id']]);
            $messages = $stmt->fetchAll();
            
            // Mark messages as read
            $stmt = $db->prepare("UPDATE messages SET is_read = 1 WHERE recipient_id = ? AND sender_id = ?");
            $stmt->execute([$_SESSION['user_id'], $contact_id]);
            
            echo json_encode(['success' => true, 'messages' => $messages]);
            exit;
            
        case 'mark_read':
            $message_id = intval($_POST['message_id'] ?? 0);
            
            $stmt = $db->prepare("UPDATE messages SET is_read = 1 WHERE id = ? AND recipient_id = ?");
            $stmt->execute([$message_id, $_SESSION['user_id']]);
            echo json_encode(['success' => true]);
            exit;
    }
}

// Get all users for contacts
$stmt = $db->prepare("SELECT id, username, full_name, avatar FROM users WHERE id != ? ORDER BY full_name");
$stmt->execute([$_SESSION['user_id']]);
$contacts = $stmt->fetchAll();

// Get last message with each contact
$contact_messages = [];
foreach ($contacts as $contact) {
    $stmt = $db->prepare("
        SELECT m.*, u.full_name 
        FROM messages m
        JOIN users u ON (CASE WHEN m.sender_id = ? THEN m.recipient_id ELSE m.sender_id END) = u.id
        WHERE (m.sender_id = ? AND m.recipient_id = ?) OR (m.sender_id = ? AND m.recipient_id = ?)
        ORDER BY m.created_at DESC
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $contact['id'], $contact['id'], $_SESSION['user_id']]);
    $last_message = $stmt->fetch();
    
    if ($last_message) {
        $contact_messages[$contact['id']] = $last_message;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - CRUD Web App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .messenger-container {
            display: grid;
            grid-template-columns: 350px 1fr;
            height: calc(100vh - 200px);
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        
        .contacts-panel {
            border-right: 1px solid var(--border-color);
            overflow-y: auto;
        }
        
        .contact-item {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .contact-item:hover {
            background-color: var(--light-color);
        }
        
        .contact-item.active {
            background-color: rgba(74, 144, 226, 0.1);
        }
        
        .contact-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            flex-shrink: 0;
        }
        
        .contact-info {
            flex: 1;
            overflow: hidden;
        }
        
        .contact-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .contact-preview {
            font-size: 0.875rem;
            color: #666;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .chat-panel {
            display: flex;
            flex-direction: column;
        }
        
        .chat-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .chat-messages {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
            background-color: #f8f9fa;
        }
        
        .message-bubble {
            max-width: 70%;
            margin-bottom: 1rem;
            padding: 0.75rem 1rem;
            border-radius: 18px;
            word-wrap: break-word;
        }
        
        .message-bubble.sent {
            background-color: var(--primary-color);
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 4px;
        }
        
        .message-bubble.received {
            background-color: white;
            border-bottom-left-radius: 4px;
        }
        
        .message-time {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 0.25rem;
        }
        
        .chat-input {
            padding: 1.5rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            gap: 1rem;
        }
        
        .chat-input input {
            flex: 1;
        }
        
        .empty-chat {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #999;
        }
        
        .empty-chat i {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <?php include 'includes/topbar.php'; ?>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1 class="page-title">Messages</h1>
                </div>

                <div class="messenger-container">
                    <div class="contacts-panel">
                        <?php foreach ($contacts as $contact): ?>
                        <div class="contact-item" data-contact-id="<?php echo $contact['id']; ?>" onclick="loadChat(<?php echo $contact['id']; ?>, '<?php echo htmlspecialchars($contact['full_name']); ?>')">
                            <div class="contact-avatar">
                                <?php echo strtoupper(substr($contact['full_name'], 0, 1)); ?>
                            </div>
                            <div class="contact-info">
                                <div class="contact-name"><?php echo htmlspecialchars($contact['full_name']); ?></div>
                                <?php if (isset($contact_messages[$contact['id']])): ?>
                                <div class="contact-preview">
                                    <?php echo htmlspecialchars(substr($contact_messages[$contact['id']]['message'], 0, 40)); ?>...
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="chat-panel" id="chatPanel">
                        <div class="empty-chat">
                            <i class="fas fa-comments"></i>
                            <p>Select a contact to start messaging</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/messages.js"></script>
</body>
</html>
