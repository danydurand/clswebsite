#!/bin/bash

# Laravel Permissions Setup Script
# This script fixes common permission issues in Laravel projects
# Usage: sudo ./set_permissions.sh

# Get the directory where the script is located
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "Setting Laravel permissions for: $SCRIPT_DIR"

# Create directories if they don't exist
echo "Ensuring required directories exist..."
mkdir -p "$SCRIPT_DIR/storage/logs"
mkdir -p "$SCRIPT_DIR/storage/framework/cache"
mkdir -p "$SCRIPT_DIR/storage/framework/sessions"
mkdir -p "$SCRIPT_DIR/storage/framework/views"
mkdir -p "$SCRIPT_DIR/bootstrap/cache"

# Set ownership to www-data (web server user)
echo "Setting ownership to www-data..."
chown -R www-data:www-data "$SCRIPT_DIR/storage" "$SCRIPT_DIR/bootstrap"

# Set permissions: 775 for directories, 664 for files
echo "Setting directory permissions to 775..."
find "$SCRIPT_DIR/storage" -type d -exec chmod 775 {} \;
find "$SCRIPT_DIR/bootstrap" -type d -exec chmod 775 {} \;
find "$SCRIPT_DIR/bootstrap/cache" -type d -exec chmod 777 {} \;

echo "Setting file permissions to 666..."
find "$SCRIPT_DIR/storage" -type f -exec chmod 666 {} \;
find "$SCRIPT_DIR/bootstrap" -type f -exec chmod 666 {} \;

# Add current user to www-data group if not already a member
if [ -n "$SUDO_USER" ]; then
    echo "Adding user $SUDO_USER to www-data group..."
    usermod -a -G www-data "$SUDO_USER"
fi

echo "✓ Permissions set successfully!"
echo ""
echo "Storage and bootstrap/cache directories are now writable by the web server."
if [ -n "$SUDO_USER" ]; then
    echo "Note: User $SUDO_USER has been added to www-data group. You may need to log out and back in for changes to take effect."
fi
