# CRUD Web Application

A full-featured CRUD-based web application with authentication, role-based dashboards, real-time messaging, task management, calendar, weather widget, and comprehensive admin tools.

## Features

### Core Features
- **User Authentication** - Login, registration, and password recovery
- **Role-Based Access Control** - Admin, Manager, and User roles with custom dashboards
- **CRUD Operations** - Complete Create, Read, Update, Delete functionality for all entities
- **Responsive Design** - Works seamlessly on desktop, tablet, and mobile devices

### Dashboard Features
- **Task Management** - Create, edit, and organize tasks with priorities and due dates
- **Calendar Integration** - Schedule and track events with color-coded categories
- **Facebook-Style Messenger** - Real-time messaging between users
- **Weather Widget** - Live weather updates for your location
- **Customizable Widgets** - Drag-and-drop dashboard customization
- **Activity Inbox** - Track all your notifications and updates

### Admin Features
- **User Management** - Full control over user accounts and permissions
- **System Diagnostics** - Monitor server health, database status, and performance
- **Activity Logging** - Comprehensive audit trail of all system actions
- **Backup Tools** - Database backup and restore functionality
- **System Settings** - Configure application parameters

## Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **AJAX**: Vanilla JavaScript Fetch API
- **Icons**: Font Awesome 6.4
- **Architecture**: MVC-inspired structure

## Installation

### Prerequisites

1. **Web Server**: Apache 2.4+ or Nginx 1.18+
2. **PHP**: Version 7.4 or higher
3. **MySQL**: Version 5.7 or higher (or MariaDB 10.3+)
4. **PHPMyAdmin**: For database management (optional but recommended)

### Required PHP Extensions

- PDO
- PDO MySQL
- mbstring
- JSON
- Session

### Installation Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/acesonder/pppp.git
   cd pppp
   ```

2. **Configure Database**
   
   Create a new MySQL database:
   ```sql
   CREATE DATABASE crud_webapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Import Database Schema**
   
   Import the schema file using PHPMyAdmin or MySQL CLI:
   ```bash
   mysql -u root -p crud_webapp < database/schema.sql
   ```

4. **Configure Application**
   
   Edit `includes/config.php` and update database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'crud_webapp');
   ```

5. **Set Permissions**
   
   Ensure the web server has write permissions:
   ```bash
   chmod -R 755 /path/to/pppp
   chown -R www-data:www-data /path/to/pppp
   ```

6. **Access the Application**
   
   Open your browser and navigate to:
   - Landing Page: `http://localhost/` or your configured domain
   - Login: `http://localhost/login.php`
   - Default Admin Credentials:
     - Username: `admin`
     - Password: `admin123`

## Default Credentials

After installation, you can login with:
- **Username**: admin
- **Password**: admin123
- **Role**: Administrator

**⚠️ IMPORTANT**: Change the default password immediately after first login!

## Project Structure

```
pppp/
├── admin/                  # Admin panel pages
│   ├── index.php          # Admin dashboard
│   └── diagnostics.php    # System diagnostics
├── assets/
│   ├── css/               # Stylesheets
│   ├── js/                # JavaScript files
│   └── images/            # Image assets
├── database/
│   └── schema.sql         # Database schema
├── includes/
│   ├── config.php         # Configuration & database
│   ├── sidebar.php        # Sidebar component
│   └── topbar.php         # Top navigation
├── index.html             # Landing page
├── login.php              # Login page
├── register.php           # Registration page
├── dashboard.php          # Main dashboard
├── tasks.php              # Task management
├── messages.php           # Messenger
└── README.md              # This file
```

## Security Features

- Password hashing using bcrypt
- SQL injection prevention via PDO prepared statements
- XSS protection via input sanitization
- Session management with timeout
- Activity logging for audit trails
- Role-based access control

## Testing

The application has been thoroughly tested across:
- **Browsers**: Chrome, Firefox, Safari, Edge
- **Devices**: Desktop, Tablet, Mobile
- **PHP Versions**: 7.4, 8.0, 8.1

## License

This project is open source and available under the MIT License.

---

**Version**: 1.0.0  
**Last Updated**: 2024
