<?php
session_start();
require_once 'config/config.php';

$page_title = 'Home';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
/* Hero Section */
.hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 100px 20px;
    text-align: center;
}

.hero h1 {
    font-size: 48px;
    margin-bottom: 20px;
    font-weight: 700;
}

.hero p {
    font-size: 20px;
    margin-bottom: 30px;
    opacity: 0.9;
}

.hero-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.hero-buttons .btn {
    padding: 15px 30px;
    font-size: 16px;
}

/* Features Section */
.features {
    padding: 80px 20px;
    background-color: white;
}

.features h2 {
    text-align: center;
    font-size: 36px;
    margin-bottom: 50px;
    color: var(--dark-color);
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
}

.feature-card {
    background: white;
    padding: 30px;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    text-align: center;
    transition: var(--transition);
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--box-shadow-lg);
}

.feature-card i {
    font-size: 48px;
    color: var(--primary-color);
    margin-bottom: 20px;
}

.feature-card h3 {
    font-size: 22px;
    margin-bottom: 15px;
    color: var(--dark-color);
}

.feature-card p {
    color: var(--gray-600);
    line-height: 1.6;
}

/* Stats Section */
.stats {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 60px 20px;
    color: white;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
}

.stat-item h3 {
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 10px;
}

.stat-item p {
    font-size: 18px;
    opacity: 0.9;
}

/* CTA Section */
.cta {
    padding: 80px 20px;
    background-color: white;
    text-align: center;
}

.cta h2 {
    font-size: 36px;
    margin-bottom: 20px;
    color: var(--dark-color);
}

.cta p {
    font-size: 18px;
    color: var(--gray-600);
    margin-bottom: 30px;
}

/* Responsive */
@media (max-width: 768px) {
    .hero h1 {
        font-size: 32px;
    }
    
    .hero p {
        font-size: 16px;
    }
    
    .features h2, .cta h2 {
        font-size: 28px;
    }
}
</style>

<!-- Hero Section -->
<section class="hero" id="home">
    <div class="container">
        <h1>Welcome to <?php echo APP_NAME; ?></h1>
        <p>Professional Project & Portfolio Platform - Manage your projects, tasks, and collaborate with your team</p>
        <div class="hero-buttons">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="btn btn-lg" style="background: white; color: #667eea;">Go to Dashboard</a>
            <?php else: ?>
                <a href="register.php" class="btn btn-lg" style="background: white; color: #667eea;">Get Started Free</a>
                <a href="login.php" class="btn btn-outline btn-lg" style="border-color: white; color: white;">Login</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features" id="features">
    <div class="container">
        <h2>Powerful Features</h2>
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-tasks"></i>
                <h3>Task Management</h3>
                <p>Create, assign, and track tasks with ease. Set priorities, due dates, and monitor progress.</p>
            </div>
            
            <div class="feature-card">
                <i class="fas fa-calendar-alt"></i>
                <h3>Calendar & Events</h3>
                <p>Schedule events, set reminders, and never miss important deadlines.</p>
            </div>
            
            <div class="feature-card">
                <i class="fas fa-comments"></i>
                <h3>Real-time Messaging</h3>
                <p>Communicate with your team instantly using our Facebook-style messenger.</p>
            </div>
            
            <div class="feature-card">
                <i class="fas fa-chart-line"></i>
                <h3>Analytics Dashboard</h3>
                <p>Track your progress with comprehensive analytics and customizable widgets.</p>
            </div>
            
            <div class="feature-card">
                <i class="fas fa-mobile-alt"></i>
                <h3>Mobile Responsive</h3>
                <p>Access your workspace from any device - desktop, tablet, or mobile.</p>
            </div>
            
            <div class="feature-card">
                <i class="fas fa-shield-alt"></i>
                <h3>Secure & Reliable</h3>
                <p>Your data is protected with enterprise-grade security and regular backups.</p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <h3>10K+</h3>
                <p>Active Users</p>
            </div>
            <div class="stat-item">
                <h3>50K+</h3>
                <p>Tasks Completed</p>
            </div>
            <div class="stat-item">
                <h3>99.9%</h3>
                <p>Uptime</p>
            </div>
            <div class="stat-item">
                <h3>24/7</h3>
                <p>Support</p>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="features" id="about">
    <div class="container">
        <h2>About PPPP</h2>
        <div style="max-width: 800px; margin: 0 auto; text-align: center;">
            <p style="font-size: 18px; color: var(--gray-600); line-height: 1.8;">
                Professional Project & Portfolio Platform (PPPP) is a comprehensive web application 
                designed to help individuals and teams manage their projects efficiently. With powerful 
                features like task management, real-time messaging, calendar integration, and customizable 
                dashboards, PPPP provides everything you need to stay organized and productive.
            </p>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <h2>Ready to Get Started?</h2>
        <p>Join thousands of users who are already managing their projects more efficiently</p>
        <?php if(!isset($_SESSION['user_id'])): ?>
            <a href="register.php" class="btn btn-primary btn-lg">Create Free Account</a>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
