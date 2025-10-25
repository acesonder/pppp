// Main JavaScript File
(function() {
    'use strict';

    // Mobile Menu Toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileSidebar = document.getElementById('mobileSidebar');
    const mobileSidebarOverlay = document.getElementById('mobileSidebarOverlay');
    const closeSidebar = document.getElementById('closeSidebar');

    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            if (mobileSidebar) {
                mobileSidebar.classList.add('active');
                mobileSidebarOverlay.classList.add('active');
            }
        });
    }

    if (closeSidebar) {
        closeSidebar.addEventListener('click', closeMobileSidebar);
    }

    if (mobileSidebarOverlay) {
        mobileSidebarOverlay.addEventListener('click', closeMobileSidebar);
    }

    function closeMobileSidebar() {
        if (mobileSidebar) {
            mobileSidebar.classList.remove('active');
            mobileSidebarOverlay.classList.remove('active');
        }
    }

    // Dark Mode Toggle
    const darkModeToggle = document.getElementById('toggleDarkMode');
    const darkModeText = document.getElementById('darkModeText');

    if (darkModeToggle) {
        // Check for saved dark mode preference
        const isDarkMode = localStorage.getItem('darkMode') === 'true';
        if (isDarkMode) {
            document.body.classList.add('dark-mode');
            if (darkModeText) darkModeText.textContent = 'Light Mode';
        }

        darkModeToggle.addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDark);
            if (darkModeText) {
                darkModeText.textContent = isDark ? 'Light Mode' : 'Dark Mode';
            }
        });
    }

    // Global Search
    const globalSearch = document.getElementById('globalSearch');
    if (globalSearch) {
        let searchTimeout;
        globalSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 300);
            }
        });
    }

    function performSearch(query) {
        // Implement search functionality
        console.log('Searching for:', query);
    }

    // Notification and Message Count Updates
    function updateNotifications() {
        fetch('api/notifications.php')
            .then(response => response.json())
            .then(data => {
                const notificationCount = document.getElementById('notificationCount');
                const notificationList = document.getElementById('notificationList');
                
                if (notificationCount && data.count > 0) {
                    notificationCount.textContent = data.count;
                    notificationCount.style.display = 'block';
                } else if (notificationCount) {
                    notificationCount.style.display = 'none';
                }

                if (notificationList && data.notifications) {
                    notificationList.innerHTML = data.notifications.map(n => `
                        <div class="notification-item">
                            <i class="fas fa-${n.icon}"></i>
                            <div>
                                <p>${n.message}</p>
                                <small>${n.time}</small>
                            </div>
                        </div>
                    `).join('');
                }
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }

    function updateMessages() {
        fetch('api/messages.php')
            .then(response => response.json())
            .then(data => {
                const messageCount = document.getElementById('messageCount');
                const messageList = document.getElementById('messageList');
                
                if (messageCount && data.count > 0) {
                    messageCount.textContent = data.count;
                    messageCount.style.display = 'block';
                } else if (messageCount) {
                    messageCount.style.display = 'none';
                }

                if (messageList && data.messages) {
                    messageList.innerHTML = data.messages.map(m => `
                        <div class="message-item">
                            <img src="${m.avatar}" alt="${m.sender}">
                            <div>
                                <h5>${m.sender}</h5>
                                <p>${m.message}</p>
                                <small>${m.time}</small>
                            </div>
                        </div>
                    `).join('');
                }
            })
            .catch(error => console.error('Error fetching messages:', error));
    }

    // Update notifications and messages periodically
    if (document.getElementById('notificationCount')) {
        updateNotifications();
        setInterval(updateNotifications, 30000); // Every 30 seconds
    }

    if (document.getElementById('messageCount')) {
        updateMessages();
        setInterval(updateMessages, 30000); // Every 30 seconds
    }

    // Form Validation
    window.validateForm = function(formId) {
        const form = document.getElementById(formId);
        if (!form) return false;

        const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('error');
                isValid = false;
            } else {
                input.classList.remove('error');
            }
        });

        return isValid;
    };

    // AJAX Form Submit
    window.submitFormAjax = function(formId, url, callback) {
        const form = document.getElementById(formId);
        if (!form) return;

        const formData = new FormData(form);

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (callback) callback(data);
        })
        .catch(error => {
            console.error('Error:', error);
            if (callback) callback({ success: false, message: 'An error occurred' });
        });
    };

    // Show Alert
    window.showAlert = function(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            ${message}
        `;

        const container = document.querySelector('.container') || document.body;
        container.insertBefore(alertDiv, container.firstChild);

        setTimeout(() => {
            alertDiv.style.opacity = '0';
            setTimeout(() => alertDiv.remove(), 300);
        }, 5000);
    };

    // Confirm Dialog
    window.confirmAction = function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    };

    // Auto-hide alerts
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    // Smooth Scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href.length > 1) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Loading State
    window.setLoading = function(element, loading) {
        if (loading) {
            element.disabled = true;
            element.dataset.originalText = element.textContent;
            element.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        } else {
            element.disabled = false;
            element.textContent = element.dataset.originalText || 'Submit';
        }
    };

})();
