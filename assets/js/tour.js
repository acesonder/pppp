// Welcome Tour - Feature walkthrough

class WelcomeTour {
    constructor() {
        this.steps = [
            {
                element: '.page-title',
                title: 'Welcome to CRUD Web App!',
                content: 'This is your personalized dashboard. Let\'s take a quick tour of the features.',
                position: 'bottom'
            },
            {
                element: '.stats-grid',
                title: 'Quick Stats',
                content: 'View your task count, pending items, upcoming events, and unread messages at a glance.',
                position: 'bottom'
            },
            {
                element: '.widget-grid',
                title: 'Dashboard Widgets',
                content: 'Your dashboard contains widgets for tasks, calendar, weather, and messages. Each widget is fully interactive.',
                position: 'top'
            },
            {
                element: '.sidebar-menu',
                title: 'Navigation Menu',
                content: 'Access all features from the sidebar: Tasks, Calendar, Messages, Profile, and Admin Panel (for admins).',
                position: 'right'
            },
            {
                element: '.search-box',
                title: 'Quick Search',
                content: 'Use the search box to quickly find tasks, messages, and events.',
                position: 'bottom'
            },
            {
                element: '.notification-icon',
                title: 'Notifications',
                content: 'Stay updated with notifications for upcoming events and important updates.',
                position: 'bottom'
            },
            {
                element: '.message-icon',
                title: 'Messages',
                content: 'Access your inbox to communicate with other users in real-time.',
                position: 'bottom'
            },
            {
                element: '.user-menu',
                title: 'User Menu',
                content: 'Access your profile, settings, and logout from the user menu.',
                position: 'bottom'
            }
        ];
        
        this.currentStep = 0;
        this.overlay = null;
        this.tooltip = null;
    }
    
    start() {
        this.createOverlay();
        this.showStep(0);
    }
    
    createOverlay() {
        this.overlay = document.createElement('div');
        this.overlay.className = 'tour-overlay';
        this.overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9998;
        `;
        document.body.appendChild(this.overlay);
        
        this.tooltip = document.createElement('div');
        this.tooltip.className = 'tour-tooltip';
        this.tooltip.style.cssText = `
            position: fixed;
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            z-index: 9999;
            max-width: 400px;
        `;
        document.body.appendChild(this.tooltip);
    }
    
    showStep(index) {
        if (index >= this.steps.length) {
            this.end();
            return;
        }
        
        const step = this.steps[index];
        const element = document.querySelector(step.element);
        
        if (!element) {
            this.showStep(index + 1);
            return;
        }
        
        // Highlight element
        element.style.position = 'relative';
        element.style.zIndex = '9999';
        
        // Position tooltip
        const rect = element.getBoundingClientRect();
        let top, left;
        
        switch (step.position) {
            case 'top':
                top = rect.top - 200;
                left = rect.left + rect.width / 2 - 200;
                break;
            case 'bottom':
                top = rect.bottom + 20;
                left = rect.left + rect.width / 2 - 200;
                break;
            case 'right':
                top = rect.top + rect.height / 2 - 100;
                left = rect.right + 20;
                break;
            default:
                top = rect.top;
                left = rect.left;
        }
        
        this.tooltip.style.top = top + 'px';
        this.tooltip.style.left = left + 'px';
        
        this.tooltip.innerHTML = `
            <h3 style="margin-bottom: 0.5rem;">${step.title}</h3>
            <p style="margin-bottom: 1rem;">${step.content}</p>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #666; font-size: 0.9rem;">
                    Step ${index + 1} of ${this.steps.length}
                </span>
                <div>
                    ${index > 0 ? '<button class="btn btn-small btn-outline" onclick="tour.previous()">Previous</button>' : ''}
                    <button class="btn btn-small btn-primary" onclick="tour.next()">
                        ${index < this.steps.length - 1 ? 'Next' : 'Finish'}
                    </button>
                    <button class="btn btn-small btn-outline" onclick="tour.end()">Skip</button>
                </div>
            </div>
        `;
        
        this.currentStep = index;
    }
    
    next() {
        this.showStep(this.currentStep + 1);
    }
    
    previous() {
        this.showStep(this.currentStep - 1);
    }
    
    end() {
        if (this.overlay) {
            this.overlay.remove();
        }
        if (this.tooltip) {
            this.tooltip.remove();
        }
        
        // Reset z-index for all elements
        document.querySelectorAll('[style*="z-index"]').forEach(el => {
            el.style.zIndex = '';
        });
        
        // Mark tour as completed
        localStorage.setItem('tourCompleted', 'true');
    }
}

// Initialize tour if not completed
const tour = new WelcomeTour();

document.addEventListener('DOMContentLoaded', function() {
    // Check if tour should be shown
    const tourCompleted = localStorage.getItem('tourCompleted');
    const currentPage = window.location.pathname;
    
    if (!tourCompleted && currentPage.includes('dashboard.php')) {
        // Show tour after a short delay
        setTimeout(() => {
            if (confirm('Would you like a quick tour of the dashboard features?')) {
                tour.start();
            } else {
                localStorage.setItem('tourCompleted', 'true');
            }
        }, 1000);
    }
});

// Add button to restart tour
function restartTour() {
    localStorage.removeItem('tourCompleted');
    tour.start();
}
