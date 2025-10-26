# CRUD Web Application - Complete Documentation

## Table of Contents
1. [Introduction](#introduction)
2. [Features Overview](#features-overview)
3. [Installation Guide](#installation-guide)
4. [User Guide](#user-guide)
5. [Admin Guide](#admin-guide)
6. [API Reference](#api-reference)
7. [Troubleshooting](#troubleshooting)
8. [Screenshots](#screenshots)

## Introduction

The CRUD Web Application is a comprehensive, feature-rich platform designed to help users manage tasks, schedule events, communicate with team members, and stay organized. Built with modern web technologies, it provides a responsive and intuitive user experience across all devices.

## Features Overview

### 1. Authentication System
- **User Registration**: New users can create accounts with email verification
- **Secure Login**: Password-based authentication with session management
- **Password Recovery**: Forgot password functionality with token-based reset
- **Role-Based Access**: Three user roles (Admin, Manager, User) with different permissions

### 2. Dashboard
The dashboard is the central hub of the application, featuring:
- **Quick Statistics**: Overview of tasks, events, and messages
- **Task Widget**: View and manage recent tasks
- **Calendar Widget**: See upcoming events at a glance
- **Weather Widget**: Real-time weather information
- **Messenger Widget**: Recent messages and quick access to inbox
- **Customizable Layout**: Widgets can be arranged based on user preference

### 3. Task Management
Complete CRUD functionality for tasks:
- Create tasks with title, description, priority, and due date
- Update task status (Pending, In Progress, Completed, Cancelled)
- Set task priorities (Low, Medium, High, Urgent)
- Filter tasks by status
- Delete tasks when no longer needed
- Visual indicators for task priorities
- Quick checkbox toggle for task completion

### 4. Calendar & Events
Manage your schedule effectively:
- Create events with start and end times
- Add event descriptions and locations
- Color-code events for easy identification
- View upcoming events in a list
- Delete past or cancelled events
- Monthly calendar navigation
- Event reminders and notifications

### 5. Messenger (Facebook-Style)
Real-time communication platform:
- One-on-one messaging with other users
- Real-time message updates
- Conversation history
- Read/unread status indicators
- User online status
- Message notifications
- Quick message preview in dashboard

### 6. Weather Widget
Stay informed about weather conditions:
- Current temperature display
- Weather condition (Sunny, Cloudy, Rainy, etc.)
- Wind speed information
- Humidity percentage
- Auto-refresh capability
- Location-based weather (configurable)

### 7. Profile Management
Users can manage their profiles:
- Update full name and email
- Change password
- View account role
- See account creation date
- Profile avatar (using initials)

### 8. Admin Panel
Exclusive features for administrators:

#### System Overview
- Total user count
- Total tasks across all users
- Total messages sent
- 24-hour activity statistics

#### User Management
- View all registered users
- See user roles and status
- Monitor user activity
- Track registration dates

#### System Diagnostics
- PHP configuration details
- Database connection status
- Server information
- Disk space usage
- Required PHP extensions check
- Database table verification
- Performance metrics

#### Activity Logging
- Comprehensive audit trail
- User actions tracking
- Login/logout events
- Security events
- IP address logging
- Timestamp for all activities

## Installation Guide

### System Requirements

**Server Requirements:**
- Web Server: Apache 2.4+ or Nginx 1.18+
- PHP: 7.4 or higher
- MySQL: 5.7+ or MariaDB 10.3+
- Minimum 512MB RAM
- 100MB free disk space

**PHP Extensions:**
- PDO
- PDO MySQL
- mbstring
- JSON
- Session

### Step-by-Step Installation

1. **Download and Extract**
   ```bash
   git clone https://github.com/acesonder/pppp.git
   cd pppp
   ```

2. **Create Database**
   Access PHPMyAdmin or MySQL CLI and create a database:
   ```sql
   CREATE DATABASE crud_webapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Import Schema**
   Import the database schema:
   ```bash
   mysql -u root -p crud_webapp < database/schema.sql
   ```
   
   Or use PHPMyAdmin:
   - Select the `crud_webapp` database
   - Click "Import" tab
   - Choose `database/schema.sql`
   - Click "Go"

4. **Configure Application**
   Edit `includes/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_mysql_username');
   define('DB_PASS', 'your_mysql_password');
   define('DB_NAME', 'crud_webapp');
   ```

5. **Set File Permissions**
   ```bash
   chmod -R 755 .
   chmod -R 777 assets/images/uploads
   ```

6. **Access Application**
   Navigate to: `http://localhost/pppp/` or your configured domain

7. **First Login**
   Use default admin credentials:
   - Username: `admin`
   - Password: `admin123`
   
   **⚠️ Change the password immediately after first login!**

## User Guide

### Getting Started

1. **Registration**
   - Click "Sign Up" on the landing page
   - Fill in your details (Full Name, Username, Email, Password)
   - Click "Register"
   - Login with your credentials

2. **Dashboard Tour**
   - Upon first login, you'll be offered a guided tour
   - The tour explains each dashboard feature
   - You can skip or restart the tour anytime

### Using Tasks

1. **Create a Task**
   - Navigate to "Tasks" from the sidebar
   - Click "New Task" button
   - Enter task details
   - Set priority and due date
   - Click "Save Task"

2. **Manage Tasks**
   - Check the checkbox to mark as complete
   - Click "Edit" to modify task details
   - Click "Delete" to remove a task
   - Use filters to view specific task types

### Using Calendar

1. **Create an Event**
   - Navigate to "Calendar"
   - Click "New Event"
   - Enter event details
   - Set start and end times
   - Choose a color for the event
   - Add location if needed
   - Click "Save Event"

2. **View Events**
   - Navigate between months using arrows
   - Click "Today" to jump to current month
   - Upcoming events are listed below the calendar
   - Delete events using the delete button

### Using Messenger

1. **Send a Message**
   - Navigate to "Messages"
   - Click on a contact from the list
   - Type your message in the input box
   - Press Enter or click "Send"

2. **Read Messages**
   - Unread messages are highlighted
   - Messages auto-refresh every 3 seconds
   - View conversation history
   - Messages are marked as read automatically

### Profile Settings

1. **Update Profile**
   - Navigate to "Profile"
   - Update your name or email
   - Click "Update Profile"

2. **Change Password**
   - Enter your current password
   - Enter new password
   - Confirm new password
   - Click "Update Profile"

## Admin Guide

### Accessing Admin Panel

1. Login with an admin account
2. Click "Admin Panel" in the sidebar
3. View system overview and statistics

### User Management

- View all registered users
- Monitor user activity
- Check user roles and permissions
- Review registration dates

### System Diagnostics

1. **PHP Configuration**
   - View PHP version
   - Check memory limits
   - Review upload settings

2. **Database Health**
   - Verify connection status
   - Check database size
   - Validate table structure

3. **Server Information**
   - View server software
   - Check operating system
   - Monitor disk space

4. **Activity Logs**
   - Review user actions
   - Track login attempts
   - Monitor security events
   - Filter by date or user

## API Reference

### Task API

**Create Task**
```
POST /tasks.php
Content-Type: application/x-www-form-urlencoded

action=create
title=Task Title
description=Task Description
priority=high
due_date=2024-12-31 23:59:59
```

**Update Task**
```
POST /tasks.php

action=update
id=1
title=Updated Title
status=completed
priority=medium
```

**Delete Task**
```
POST /tasks.php

action=delete
id=1
```

### Calendar API

**Create Event**
```
POST /calendar.php

action=create
title=Event Title
description=Event Description
start_datetime=2024-12-01 10:00:00
end_datetime=2024-12-01 12:00:00
location=Conference Room
color=#3788d8
```

**Get Events**
```
POST /calendar.php

action=get_events
```

### Messenger API

**Send Message**
```
POST /messages.php

action=send_message
recipient_id=2
message=Hello!
```

**Get Messages**
```
POST /messages.php

action=get_messages
contact_id=2
```

## Troubleshooting

### Common Issues

**1. Cannot Connect to Database**
- Check database credentials in `includes/config.php`
- Verify MySQL service is running
- Confirm database exists and is accessible

**2. Login Issues**
- Clear browser cookies and cache
- Verify username and password
- Check if account is active (not suspended)

**3. PHP Errors**
- Enable error reporting in php.ini
- Check PHP error logs
- Verify all required extensions are installed

**4. Session Problems**
- Check session configuration in php.ini
- Verify session directory has write permissions
- Clear session files

**5. Upload Failures**
- Check upload_max_filesize in php.ini
- Verify uploads directory permissions
- Ensure post_max_size is adequate

### Getting Support

1. Check the diagnostics page in Admin Panel
2. Review PHP and web server error logs
3. Verify system requirements are met
4. Check file and directory permissions

## Screenshots

Screenshots documenting each feature are available in the `/docs/screenshots/` directory:

1. `landing-page.png` - Application landing page
2. `login.png` - Login screen
3. `register.png` - Registration form
4. `dashboard.png` - Main dashboard
5. `tasks.png` - Task management
6. `calendar.png` - Calendar view
7. `messages.png` - Messenger interface
8. `profile.png` - User profile
9. `admin-panel.png` - Admin dashboard
10. `diagnostics.png` - System diagnostics

---

**Documentation Version**: 1.0.0  
**Last Updated**: 2024  
**Support**: See README.md for contact information
