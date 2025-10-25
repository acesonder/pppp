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

$selected_user = $_GET['user'] ?? null;

// Get all users for conversation list
$usersStmt = $conn->prepare("
    SELECT u.*, 
           (SELECT COUNT(*) FROM messages WHERE sender_id = u.id AND receiver_id = ? AND is_read = 0) as unread_count,
           (SELECT message FROM messages WHERE (sender_id = u.id AND receiver_id = ?) OR (sender_id = ? AND receiver_id = u.id) ORDER BY created_at DESC LIMIT 1) as last_message,
           (SELECT created_at FROM messages WHERE (sender_id = u.id AND receiver_id = ?) OR (sender_id = ? AND receiver_id = u.id) ORDER BY created_at DESC LIMIT 1) as last_message_time
    FROM users u
    WHERE u.id != ?
    ORDER BY last_message_time DESC
");
$usersStmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
$users = $usersStmt->fetchAll();

// Get messages with selected user
$messages = [];
if($selected_user) {
    $messagesStmt = $conn->prepare("
        SELECT m.*, 
               s.full_name as sender_name, 
               s.avatar as sender_avatar
        FROM messages m
        JOIN users s ON m.sender_id = s.id
        WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.created_at ASC
    ");
    $messagesStmt->execute([$_SESSION['user_id'], $selected_user, $selected_user, $_SESSION['user_id']]);
    $messages = $messagesStmt->fetchAll();
    
    // Mark messages as read
    $readStmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE receiver_id = ? AND sender_id = ?");
    $readStmt->execute([$_SESSION['user_id'], $selected_user]);
    
    // Get selected user info
    $userStmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $userStmt->execute([$selected_user]);
    $selected_user_data = $userStmt->fetch();
}

$page_title = 'Messenger';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
.messenger-container {
    max-width: 1400px;
    margin: 30px auto;
    padding: 0 20px;
    height: calc(100vh - 150px);
}

.messenger-wrapper {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 0;
    height: 100%;
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
}

.conversations-panel {
    border-right: 1px solid var(--gray-200);
    display: flex;
    flex-direction: column;
}

.conversations-header {
    padding: 20px;
    border-bottom: 1px solid var(--gray-200);
}

.conversations-header h2 {
    font-size: 24px;
    margin-bottom: 15px;
}

.search-box {
    position: relative;
}

.search-box input {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid var(--gray-300);
    border-radius: 20px;
    font-size: 14px;
}

.search-box i {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-500);
}

.conversations-list {
    flex: 1;
    overflow-y: auto;
}

.conversation-item {
    padding: 15px 20px;
    border-bottom: 1px solid var(--gray-200);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    gap: 12px;
    align-items: center;
}

.conversation-item:hover, .conversation-item.active {
    background: var(--gray-100);
}

.conversation-avatar {
    position: relative;
}

.conversation-avatar img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.online-indicator {
    width: 12px;
    height: 12px;
    background: #27ae60;
    border: 2px solid white;
    border-radius: 50%;
    position: absolute;
    bottom: 2px;
    right: 2px;
}

.conversation-info {
    flex: 1;
    min-width: 0;
}

.conversation-name {
    font-weight: 600;
    margin-bottom: 4px;
}

.conversation-preview {
    color: var(--gray-600);
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.conversation-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 5px;
}

.conversation-time {
    font-size: 12px;
    color: var(--gray-500);
}

.unread-badge {
    background: var(--primary-color);
    color: white;
    border-radius: 10px;
    padding: 2px 8px;
    font-size: 12px;
    font-weight: 600;
}

.chat-panel {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.chat-header {
    padding: 20px;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-user-info {
    display: flex;
    gap: 12px;
    align-items: center;
}

.chat-user-info img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.chat-user-name h3 {
    font-size: 18px;
    margin-bottom: 2px;
}

.chat-user-status {
    font-size: 12px;
    color: var(--gray-600);
}

.chat-actions {
    display: flex;
    gap: 10px;
}

.chat-actions button {
    background: none;
    border: none;
    font-size: 18px;
    color: var(--gray-600);
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: var(--transition);
}

.chat-actions button:hover {
    background: var(--gray-100);
    color: var(--primary-color);
}

.messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: var(--gray-100);
}

.message-group {
    margin-bottom: 20px;
}

.message {
    display: flex;
    gap: 10px;
    margin-bottom: 8px;
}

.message.sent {
    flex-direction: row-reverse;
}

.message-avatar img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
}

.message-content {
    max-width: 60%;
}

.message-bubble {
    background: white;
    padding: 10px 15px;
    border-radius: 18px;
    word-wrap: break-word;
}

.message.sent .message-bubble {
    background: var(--primary-color);
    color: white;
}

.message-time {
    font-size: 11px;
    color: var(--gray-500);
    margin-top: 4px;
    padding: 0 10px;
}

.message-input-container {
    padding: 20px;
    border-top: 1px solid var(--gray-200);
    background: white;
}

.message-input-wrapper {
    display: flex;
    gap: 10px;
    align-items: center;
}

.message-input {
    flex: 1;
    padding: 12px 15px;
    border: 1px solid var(--gray-300);
    border-radius: 20px;
    font-size: 14px;
    resize: none;
    max-height: 100px;
}

.message-input:focus {
    outline: none;
    border-color: var(--primary-color);
}

.input-actions {
    display: flex;
    gap: 8px;
}

.input-actions button {
    width: 40px;
    height: 40px;
    border: none;
    background: none;
    color: var(--gray-600);
    font-size: 18px;
    cursor: pointer;
    border-radius: 50%;
    transition: var(--transition);
}

.input-actions button:hover {
    background: var(--gray-100);
}

.send-btn {
    background: var(--primary-color) !important;
    color: white !important;
}

.send-btn:hover {
    background: #3a7bc8 !important;
}

.empty-chat {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: var(--gray-500);
}

.empty-chat i {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.5;
}

@media (max-width: 768px) {
    .messenger-wrapper {
        grid-template-columns: 1fr;
    }
    
    .conversations-panel {
        display: none;
    }
    
    .messenger-wrapper.show-conversations .conversations-panel {
        display: flex;
    }
    
    .messenger-wrapper.show-conversations .chat-panel {
        display: none;
    }
}
</style>

<div class="messenger-container">
    <div class="messenger-wrapper <?php echo $selected_user ? '' : 'show-conversations'; ?>">
        <!-- Conversations Panel -->
        <div class="conversations-panel">
            <div class="conversations-header">
                <h2>Messages</h2>
                <div class="search-box">
                    <input type="text" id="searchUsers" placeholder="Search conversations...">
                    <i class="fas fa-search"></i>
                </div>
            </div>
            
            <div class="conversations-list">
                <?php foreach($users as $user): ?>
                    <div class="conversation-item <?php echo ($selected_user == $user['id']) ? 'active' : ''; ?>" 
                         onclick="selectUser(<?php echo $user['id']; ?>)">
                        <div class="conversation-avatar">
                            <img src="<?php echo APP_URL; ?>/uploads/avatars/<?php echo $user['avatar']; ?>" 
                                 alt="Avatar" 
                                 onerror="this.src='<?php echo APP_URL; ?>/assets/img/default-avatar.png'">
                            <?php if($user['status'] === 'active'): ?>
                                <div class="online-indicator"></div>
                            <?php endif; ?>
                        </div>
                        <div class="conversation-info">
                            <div class="conversation-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                            <div class="conversation-preview">
                                <?php echo $user['last_message'] ? htmlspecialchars(substr($user['last_message'], 0, 30)) . '...' : 'No messages yet'; ?>
                            </div>
                        </div>
                        <div class="conversation-meta">
                            <?php if($user['last_message_time']): ?>
                                <div class="conversation-time">
                                    <?php echo date('h:i A', strtotime($user['last_message_time'])); ?>
                                </div>
                            <?php endif; ?>
                            <?php if($user['unread_count'] > 0): ?>
                                <div class="unread-badge"><?php echo $user['unread_count']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Chat Panel -->
        <div class="chat-panel">
            <?php if($selected_user && isset($selected_user_data)): ?>
                <div class="chat-header">
                    <div class="chat-user-info">
                        <img src="<?php echo APP_URL; ?>/uploads/avatars/<?php echo $selected_user_data['avatar']; ?>" 
                             alt="Avatar" 
                             onerror="this.src='<?php echo APP_URL; ?>/assets/img/default-avatar.png'">
                        <div class="chat-user-name">
                            <h3><?php echo htmlspecialchars($selected_user_data['full_name']); ?></h3>
                            <div class="chat-user-status">
                                <?php echo $selected_user_data['status'] === 'active' ? 'Active now' : 'Offline'; ?>
                            </div>
                        </div>
                    </div>
                    <div class="chat-actions">
                        <button title="Call"><i class="fas fa-phone"></i></button>
                        <button title="Video call"><i class="fas fa-video"></i></button>
                        <button title="Info"><i class="fas fa-info-circle"></i></button>
                    </div>
                </div>

                <div class="messages-container" id="messagesContainer">
                    <?php if(count($messages) > 0): ?>
                        <?php foreach($messages as $msg): ?>
                            <div class="message <?php echo $msg['sender_id'] == $_SESSION['user_id'] ? 'sent' : 'received'; ?>">
                                <div class="message-avatar">
                                    <img src="<?php echo APP_URL; ?>/uploads/avatars/<?php echo $msg['sender_avatar']; ?>" 
                                         alt="Avatar" 
                                         onerror="this.src='<?php echo APP_URL; ?>/assets/img/default-avatar.png'">
                                </div>
                                <div class="message-content">
                                    <div class="message-bubble">
                                        <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                    </div>
                                    <div class="message-time">
                                        <?php echo date('h:i A', strtotime($msg['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-chat">
                            <i class="fas fa-comments"></i>
                            <p>No messages yet. Start the conversation!</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="message-input-container">
                    <div class="message-input-wrapper">
                        <div class="input-actions">
                            <button title="Attach file"><i class="fas fa-paperclip"></i></button>
                            <button title="Emoji"><i class="fas fa-smile"></i></button>
                        </div>
                        <textarea class="message-input" 
                                  id="messageInput" 
                                  placeholder="Type a message..."
                                  rows="1"></textarea>
                        <button class="send-btn" onclick="sendMessage()" title="Send">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div class="empty-chat">
                    <i class="fas fa-comments"></i>
                    <p>Select a conversation to start messaging</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function selectUser(userId) {
    window.location.href = '?user=' + userId;
}

function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if(!message) return;
    
    const formData = new FormData();
    formData.append('message', message);
    formData.append('receiver_id', <?php echo $selected_user ?? 'null'; ?>);
    
    fetch('api/send-message.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            input.value = '';
            window.location.reload();
        } else {
            showAlert(data.message || 'Failed to send message', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred', 'error');
    });
}

// Allow sending with Enter key
document.getElementById('messageInput')?.addEventListener('keypress', function(e) {
    if(e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

// Auto-scroll to bottom
const container = document.getElementById('messagesContainer');
if(container) {
    container.scrollTop = container.scrollHeight;
}

// Search users
document.getElementById('searchUsers')?.addEventListener('input', function() {
    const query = this.value.toLowerCase();
    const items = document.querySelectorAll('.conversation-item');
    
    items.forEach(item => {
        const name = item.querySelector('.conversation-name').textContent.toLowerCase();
        item.style.display = name.includes(query) ? 'flex' : 'none';
    });
});
</script>

<?php include 'includes/footer.php'; ?>
