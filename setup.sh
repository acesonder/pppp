#!/bin/bash

# CRUD Web Application - Quick Setup Script
# This script helps automate the installation process

echo "================================"
echo "CRUD Web App - Quick Setup"
echo "================================"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if MySQL is installed
if ! command -v mysql &> /dev/null; then
    echo -e "${RED}Error: MySQL is not installed${NC}"
    echo "Please install MySQL first and try again."
    exit 1
fi

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo -e "${RED}Error: PHP is not installed${NC}"
    echo "Please install PHP 7.4+ first and try again."
    exit 1
fi

echo -e "${GREEN}✓ MySQL found${NC}"
echo -e "${GREEN}✓ PHP found (version: $(php -v | head -n 1))${NC}"
echo ""

# Get database credentials
echo "Please enter your MySQL credentials:"
read -p "MySQL Host [localhost]: " DB_HOST
DB_HOST=${DB_HOST:-localhost}

read -p "MySQL Username [root]: " DB_USER
DB_USER=${DB_USER:-root}

read -sp "MySQL Password: " DB_PASS
echo ""

read -p "Database Name [crud_webapp]: " DB_NAME
DB_NAME=${DB_NAME:-crud_webapp}

echo ""
echo "Database Configuration:"
echo "  Host: $DB_HOST"
echo "  User: $DB_USER"
echo "  Database: $DB_NAME"
echo ""

read -p "Is this correct? (y/n): " CONFIRM
if [ "$CONFIRM" != "y" ]; then
    echo "Setup cancelled."
    exit 0
fi

echo ""
echo "Creating database..."

# Create database
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Database created successfully${NC}"
else
    echo -e "${RED}✗ Failed to create database${NC}"
    echo "Please check your MySQL credentials and try again."
    exit 1
fi

# Import schema
echo "Importing database schema..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < database/schema.sql 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Schema imported successfully${NC}"
else
    echo -e "${RED}✗ Failed to import schema${NC}"
    exit 1
fi

# Update config file
echo "Updating configuration file..."

CONFIG_FILE="includes/config.php"

# Backup original config
if [ -f "$CONFIG_FILE" ]; then
    cp "$CONFIG_FILE" "$CONFIG_FILE.bak"
    echo -e "${YELLOW}Backup created: $CONFIG_FILE.bak${NC}"
fi

# Update database credentials in config.php
sed -i "s/define('DB_HOST', '.*');/define('DB_HOST', '$DB_HOST');/" "$CONFIG_FILE"
sed -i "s/define('DB_USER', '.*');/define('DB_USER', '$DB_USER');/" "$CONFIG_FILE"
sed -i "s/define('DB_PASS', '.*');/define('DB_PASS', '$DB_PASS');/" "$CONFIG_FILE"
sed -i "s/define('DB_NAME', '.*');/define('DB_NAME', '$DB_NAME');/" "$CONFIG_FILE"

echo -e "${GREEN}✓ Configuration updated${NC}"

# Create upload directory
echo "Creating upload directory..."
mkdir -p assets/images/uploads
chmod 777 assets/images/uploads
echo -e "${GREEN}✓ Upload directory created${NC}"

echo ""
echo "================================"
echo -e "${GREEN}Setup completed successfully!${NC}"
echo "================================"
echo ""
echo "Default Admin Credentials:"
echo "  Username: admin"
echo "  Password: admin123"
echo ""
echo -e "${YELLOW}⚠️  IMPORTANT: Change the admin password after first login!${NC}"
echo ""
echo "Next Steps:"
echo "1. Configure your web server to point to this directory"
echo "2. Navigate to http://localhost/pppp/ (or your configured URL)"
echo "3. Login with the default admin credentials"
echo "4. Change the admin password immediately"
echo ""
echo "For detailed setup instructions, see README.md"
echo ""
