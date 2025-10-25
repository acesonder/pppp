# PPPP Installation Guide

## Quick Start Guide

### Step 1: Prerequisites Check

Before installing PPPP, ensure you have:
- ✅ PHP 7.4 or higher
- ✅ MySQL 5.7 or higher
- ✅ Apache or Nginx web server
- ✅ phpMyAdmin (recommended)

### Step 2: Download & Extract

1. Download PPPP or clone from GitHub:
   ```bash
   git clone https://github.com/acesonder/pppp.git
   ```

2. Move to your web server directory:
   ```bash
   # For Apache on Ubuntu/Debian
   sudo mv pppp /var/www/html/

   # For XAMPP
   mv pppp C:/xampp/htdocs/

   # For MAMP
   mv pppp /Applications/MAMP/htdocs/
   ```

### Step 3: Database Setup

**Option A: Using phpMyAdmin**
1. Open phpMyAdmin in your browser
2. Create a new database named `pppp_db`
3. Click on the database
4. Go to "Import" tab
5. Choose file: `database/schema.sql`
6. Click "Go"

**Option B: Using MySQL Command Line**
```bash
mysql -u root -p
CREATE DATABASE pppp_db;
USE pppp_db;
SOURCE /path/to/pppp/database/schema.sql;
EXIT;
```

### Step 4: Configuration

1. Open `config/config.php` in a text editor

2. Update database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');           // Your MySQL username
   define('DB_PASS', '');               // Your MySQL password
   define('DB_NAME', 'pppp_db');
   ```

3. Update application URL:
   ```php
   define('APP_URL', 'http://localhost/pppp');
   // Or for live server: 'https://yourdomain.com'
   ```

### Step 5: Set File Permissions

**On Linux/Mac:**
```bash
cd /path/to/pppp
chmod -R 755 uploads/
chmod -R 755 assets/
```

**On Windows:**
- Right-click `uploads` folder → Properties → Security
- Ensure "Users" group has write permissions

### Step 6: First Access

1. Open your web browser
2. Navigate to: `http://localhost/pppp`
3. You should see the landing page

### Step 7: Admin Login

1. Click "Login" button
2. Use default admin credentials:
   - **Username**: `admin`
   - **Email**: `admin@pppp.com`
   - **Password**: `admin123`

3. **⚠️ IMPORTANT**: Change admin password immediately!
   - Go to Profile → Settings
   - Update password

### Step 8: Create Your Account (As User)

1. Click "Get Started" or "Register"
2. Fill in registration form
3. You'll be redirected to dashboard

## Testing the Application

### Test 1: Create a Task
1. Go to Dashboard
2. Click "Tasks" or "+ New Task"
3. Fill in task details
4. Save and verify it appears

### Test 2: Create an Event
1. Go to Calendar
2. Click "+ New Event"
3. Set date, time, and details
4. Save and check calendar

### Test 3: Send a Message
1. Go to Messenger
2. Select admin user (if you're not admin)
3. Type and send a message
4. Check if it appears in conversation

### Test 4: Access Admin Panel (As Admin)
1. Login as admin
2. Go to `/admin`
3. Check statistics and diagnostics

## Common Issues & Solutions

### Issue: "Database connection failed"
**Solution:**
- Check database credentials in `config/config.php`
- Ensure MySQL is running
- Verify database `pppp_db` exists

### Issue: "Cannot write to uploads directory"
**Solution:**
```bash
chmod -R 777 uploads/  # On Linux/Mac
```

### Issue: Blank page or white screen
**Solution:**
1. Enable error reporting in `config/config.php`:
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```
2. Check PHP error logs

### Issue: Session not working
**Solution:**
- Clear browser cookies
- Check PHP session directory is writable
- Restart web server

### Issue: CSS/JS not loading
**Solution:**
- Check file paths in `config/config.php`
- Verify `APP_URL` is correct
- Clear browser cache

## Production Deployment

When deploying to production:

1. **Disable error display:**
   ```php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```

2. **Use HTTPS:**
   - Get SSL certificate
   - Update `APP_URL` to use `https://`

3. **Change default passwords:**
   - Update admin password
   - Use strong passwords

4. **Set secure permissions:**
   ```bash
   chmod 644 config/config.php
   chmod 755 uploads/
   ```

5. **Enable security features:**
   - Set `session.cookie_secure` to 1
   - Configure CSRF protection
   - Set up backup system

## Server Requirements

### Recommended Specifications
- **RAM**: 512MB minimum, 1GB+ recommended
- **Storage**: 100MB minimum for application
- **PHP Extensions**: 
  - PDO
  - MySQL/MySQLi
  - Session
  - JSON
  - MBString

### Checking PHP Extensions
```bash
php -m
```

Look for:
- pdo_mysql
- session
- json
- mbstring

## Next Steps

1. ✅ Customize dashboard widgets
2. ✅ Invite team members
3. ✅ Create your first project/tasks
4. ✅ Set up calendar events
5. ✅ Explore admin features

## Getting Help

- 📧 Email: info@pppp.com
- 🐛 Report bugs: GitHub Issues
- 📖 Documentation: Coming soon
- 💬 Community: Join our Discord (link coming soon)

## Backup & Maintenance

### Database Backup
```bash
mysqldump -u root -p pppp_db > backup.sql
```

### Restore from Backup
```bash
mysql -u root -p pppp_db < backup.sql
```

---

**Congratulations! 🎉**

Your PPPP installation is complete. Start managing your projects, tasks, and team communications efficiently!
