// Messages page JavaScript

let currentContactId = null;
let currentContactName = null;
let messageInterval = null;

function loadChat(contactId, contactName) {
    currentContactId = contactId;
    currentContactName = contactName;
    
    // Update active contact
    document.querySelectorAll('.contact-item').forEach(item => {
        item.classList.remove('active');
    });
    document.querySelector(`[data-contact-id="${contactId}"]`).classList.add('active');
    
    // Load messages
    loadMessages();
    
    // Start auto-refresh
    if (messageInterval) {
        clearInterval(messageInterval);
    }
    messageInterval = setInterval(loadMessages, 3000);
}

function loadMessages() {
    if (!currentContactId) return;
    
    const formData = new FormData();
    formData.append('action', 'get_messages');
    formData.append('contact_id', currentContactId);
    
    fetch('messages.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayMessages(data.messages);
        }
    })
    .catch(error => {
        console.error('Error loading messages:', error);
    });
}

function displayMessages(messages) {
    const chatPanel = document.getElementById('chatPanel');
    const currentUserId = <?php echo $_SESSION['user_id']; ?>;
    
    let html = `
        <div class="chat-header">
            <div class="contact-avatar">
                ${currentContactName.charAt(0).toUpperCase()}
            </div>
            <div>
                <div class="contact-name">${currentContactName}</div>
                <small style="color: #666;">Active now</small>
            </div>
        </div>
        <div class="chat-messages" id="chatMessages">
    `;
    
    messages.forEach(msg => {
        const isSent = msg.sender_id == currentUserId;
        const messageClass = isSent ? 'sent' : 'received';
        const time = new Date(msg.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        
        html += `
            <div class="message-bubble ${messageClass}">
                <div>${msg.message}</div>
                <div class="message-time">${time}</div>
            </div>
        `;
    });
    
    html += `
        </div>
        <div class="chat-input">
            <input type="text" class="form-control" id="messageInput" placeholder="Type a message..." onkeypress="handleKeyPress(event)">
            <button class="btn btn-primary" onclick="sendMessage()">
                <i class="fas fa-paper-plane"></i> Send
            </button>
        </div>
    `;
    
    chatPanel.innerHTML = html;
    
    // Scroll to bottom
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
    
    // Focus input
    document.getElementById('messageInput').focus();
}

function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (!message || !currentContactId) return;
    
    const formData = new FormData();
    formData.append('action', 'send_message');
    formData.append('recipient_id', currentContactId);
    formData.append('message', message);
    
    fetch('messages.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            loadMessages();
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('Failed to send message', 'error');
    });
}

function handleKeyPress(event) {
    if (event.key === 'Enter') {
        sendMessage();
    }
}

// Clean up interval on page unload
window.addEventListener('beforeunload', function() {
    if (messageInterval) {
        clearInterval(messageInterval);
    }
});
