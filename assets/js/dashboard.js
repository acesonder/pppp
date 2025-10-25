// Dashboard specific JavaScript

// User menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const userMenu = document.querySelector('.user-menu');
    const userDropdown = document.querySelector('.user-dropdown');
    
    if (userMenu) {
        userMenu.addEventListener('click', function() {
            if (userDropdown) {
                userDropdown.classList.toggle('show');
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userMenu.contains(e.target) && userDropdown) {
                userDropdown.classList.remove('show');
            }
        });
    }
    
    // Sidebar toggle for mobile
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const sidebar = document.getElementById('sidebar');
    
    if (mobileMenuToggle && sidebar) {
        mobileMenuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }
    
    // Initialize weather widget
    loadWeather();
    
    // Task checkbox handlers
    const taskCheckboxes = document.querySelectorAll('.task-checkbox');
    taskCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const taskItem = this.closest('.task-item');
            if (this.checked) {
                taskItem.style.opacity = '0.6';
            } else {
                taskItem.style.opacity = '1';
            }
        });
    });
});

// Load weather data
function loadWeather() {
    const weatherWidget = document.getElementById('weatherWidget');
    if (!weatherWidget) return;
    
    // Simulated weather data (in production, use real weather API)
    const weatherData = {
        temp: Math.floor(Math.random() * 30) + 60,
        condition: ['Sunny', 'Cloudy', 'Rainy', 'Partly Cloudy'][Math.floor(Math.random() * 4)],
        wind: Math.floor(Math.random() * 20) + 5,
        humidity: Math.floor(Math.random() * 40) + 30
    };
    
    const iconMap = {
        'Sunny': 'fa-sun',
        'Cloudy': 'fa-cloud',
        'Rainy': 'fa-cloud-rain',
        'Partly Cloudy': 'fa-cloud-sun'
    };
    
    weatherWidget.innerHTML = `
        <div class="weather-icon">
            <i class="fas ${iconMap[weatherData.condition]}"></i>
        </div>
        <div class="weather-temp">${weatherData.temp}°F</div>
        <div class="weather-condition">${weatherData.condition}</div>
        <div class="weather-details">
            <div class="weather-detail">
                <i class="fas fa-wind"></i>
                <span>${weatherData.wind} mph</span>
            </div>
            <div class="weather-detail">
                <i class="fas fa-tint"></i>
                <span>${weatherData.humidity}%</span>
            </div>
        </div>
    `;
}

// Modal functionality
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('show');
    }
});

// Search functionality
const searchBox = document.querySelector('.search-box input');
if (searchBox) {
    searchBox.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        // Implement search logic here
        console.log('Searching for:', searchTerm);
    });
}

// Notification click handler
const notificationIcon = document.querySelector('.notification-icon');
if (notificationIcon) {
    notificationIcon.addEventListener('click', function() {
        // Show notifications dropdown
        console.log('Show notifications');
    });
}

// Message icon click handler
const messageIcon = document.querySelector('.message-icon');
if (messageIcon) {
    messageIcon.addEventListener('click', function() {
        window.location.href = 'messages.php';
    });
}
