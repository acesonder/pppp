@echo off
REM CRUD Web Application - Quick Setup Script for Windows
REM This script helps automate the installation process

echo ================================
echo CRUD Web App - Quick Setup
echo ================================
echo.

REM Check if MySQL is accessible
where mysql >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo Error: MySQL is not in PATH
    echo Please install MySQL or add it to PATH and try again.
    pause
    exit /b 1
)

REM Check if PHP is accessible
where php >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo Error: PHP is not in PATH
    echo Please install PHP 7.4+ or add it to PATH and try again.
    pause
    exit /b 1
)

echo [OK] MySQL found
echo [OK] PHP found
echo.

REM Get database credentials
set /p DB_HOST="MySQL Host [localhost]: "
if "%DB_HOST%"=="" set DB_HOST=localhost

set /p DB_USER="MySQL Username [root]: "
if "%DB_USER%"=="" set DB_USER=root

set /p DB_PASS="MySQL Password: "

set /p DB_NAME="Database Name [crud_webapp]: "
if "%DB_NAME%"=="" set DB_NAME=crud_webapp

echo.
echo Database Configuration:
echo   Host: %DB_HOST%
echo   User: %DB_USER%
echo   Database: %DB_NAME%
echo.

set /p CONFIRM="Is this correct? (y/n): "
if /i not "%CONFIRM%"=="y" (
    echo Setup cancelled.
    pause
    exit /b 0
)

echo.
echo Creating database...

REM Create database
mysql -h %DB_HOST% -u %DB_USER% -p%DB_PASS% -e "CREATE DATABASE IF NOT EXISTS %DB_NAME% CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>nul

if %ERRORLEVEL% EQU 0 (
    echo [OK] Database created successfully
) else (
    echo [ERROR] Failed to create database
    echo Please check your MySQL credentials and try again.
    pause
    exit /b 1
)

REM Import schema
echo Importing database schema...
mysql -h %DB_HOST% -u %DB_USER% -p%DB_PASS% %DB_NAME% < database\schema.sql 2>nul

if %ERRORLEVEL% EQU 0 (
    echo [OK] Schema imported successfully
) else (
    echo [ERROR] Failed to import schema
    pause
    exit /b 1
)

REM Create backup of config file
echo Updating configuration file...
if exist includes\config.php (
    copy includes\config.php includes\config.php.bak >nul
    echo [INFO] Backup created: includes\config.php.bak
)

REM Note: Manual update needed for Windows
echo.
echo [INFO] Please manually update includes\config.php with your database credentials:
echo   DB_HOST: %DB_HOST%
echo   DB_USER: %DB_USER%
echo   DB_PASS: %DB_PASS%
echo   DB_NAME: %DB_NAME%
echo.

REM Create upload directory
echo Creating upload directory...
if not exist assets\images\uploads mkdir assets\images\uploads
echo [OK] Upload directory created

echo.
echo ================================
echo Setup completed successfully!
echo ================================
echo.
echo Default Admin Credentials:
echo   Username: admin
echo   Password: admin123
echo.
echo WARNING: Change the admin password after first login!
echo.
echo Next Steps:
echo 1. Update includes\config.php with your database credentials
echo 2. Configure your web server (Apache/Nginx) to point to this directory
echo 3. Navigate to http://localhost/pppp/ (or your configured URL)
echo 4. Login with the default admin credentials
echo 5. Change the admin password immediately
echo.
echo For detailed setup instructions, see README.md
echo.
pause
