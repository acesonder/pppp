# PPPP - Professional Project & Portfolio Platform

A comprehensive CRUD-based web application with authentication, role-based dashboards, Facebook-style messenger, and advanced admin portal.

## Features

### 🔐 Authentication System
- User registration with email validation
- Secure login with password hashing (bcrypt)
- Forgot password functionality with token-based reset
- Session management with "Remember Me" option
- Role-based access control (Admin, Manager, User)

### 📊 Dashboard
- Customizable widget-based dashboard
- Real-time statistics and analytics
- Quick access to tasks, calendar, messages
- Welcome tour for new users
- Responsive design for all devices

### ✅ Task Management (CRUD)
- Create, read, update, delete tasks
- Priority levels (Low, Medium, High, Urgent)
- Status tracking (Pending, In Progress, Completed, Cancelled)
- Due date management
- Filter and search functionality

### 📅 Calendar & Events (CRUD)
- Interactive calendar view
- Create and manage events
- Date and time scheduling
- Location tracking
- Monthly navigation

### 💬 Facebook-Style Messenger
- Real-time messaging interface
- User-to-user conversations
- Unread message indicators
- Message history
- Online status indicators
- Search conversations

### 🛠️ Admin Portal
- System dashboard with statistics
- User management
- Activity logs and monitoring
- System diagnostics
- Health checks
- Database tools
- Settings management

### 🎨 Design Features
- Modern, clean UI with gradient themes
- Fully responsive (mobile, tablet, desktop)
- Dark mode support
- Smooth animations and transitions
- Font Awesome icons
- Custom color scheme

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher (or MariaDB 10.2+)
- Apache/Nginx web server
- phpMyAdmin (optional, for database management)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/acesonder/pppp.git
   cd pppp
   ```

2. **Database Setup**
   - Open phpMyAdmin
   - Create a new database named `pppp_db`
   - Import the schema: `database/schema.sql`
   - Or run the SQL file manually in MySQL

3. **Configure Application**
   - Edit `config/config.php`
   - Update database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_db_user');
     define('DB_PASS', 'your_db_password');
     define('DB_NAME', 'pppp_db');
     ```
   - Update APP_URL to match your domain/localhost

4. **Set Permissions**
   ```bash
   chmod -R 755 uploads/
   chmod -R 755 assets/
   ```

5. **Access the Application**
   - Navigate to: `http://localhost/pppp` (or your configured URL)
   - Default admin credentials:
     - Username: `admin`
     - Password: `admin123`
   - **Important**: Change the default admin password immediately!

## Usage Guide

### For Regular Users

1. **Registration**: Click "Get Started" or "Register" and fill in your details
2. **Dashboard**: View tasks, events, and messages in one place
3. **Tasks**: Create and manage tasks with priorities and due dates
4. **Calendar**: Schedule events and set reminders
5. **Messenger**: Chat with other users in real-time

### For Administrators

1. **Admin Portal**: Access via `/admin` to view system statistics
2. **User Management**: Monitor and manage all users
3. **Diagnostics**: Check system health and view logs
4. **Settings**: Configure application settings

## Project Structure

```
pppp/
├── admin/              # Admin panel
├── api/                # API endpoints
├── assets/             # CSS, JS, images
├── config/             # Configuration files
├── database/           # SQL schema
├── includes/           # Reusable components
├── uploads/            # User uploads
└── [pages].php         # Application pages
```

## Technologies

- **Backend**: PHP, MySQL, PDO
- **Frontend**: HTML5, CSS3, JavaScript, jQuery
- **Libraries**: Font Awesome, Google Fonts

## Security

- Password hashing with bcrypt
- SQL injection prevention
- XSS protection
- Session security
- Input validation

## License

MIT License - See LICENSE file for details

## Support

- Email: info@pppp.com
- GitHub Issues: https://github.com/acesonder/pppp/issues

---

**Version 1.0.0** - Full-featured CRUD web application with authentication, messaging, and admin tools
