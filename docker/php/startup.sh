#!/bin/bash

echo "🚀 Starting PHP container..."

# Check if vendor directory exists
if [ ! -d "/var/www/vendor" ]; then
    echo "📦 Installing Composer dependencies..."
    cd /var/www
    composer install --optimize-autoloader
    composer run-script post-autoload-dump
    echo "✅ Composer dependencies installed successfully!"
else
    echo "✅ Composer dependencies already exist, skipping installation"
fi

# Create storage link if it doesn't exist
if [ ! -L "/var/www/public/storage" ]; then
    echo "🔗 Creating storage symbolic link..."
    cd /var/www
    php artisan storage:link
    echo "✅ Storage link created successfully!"
else
    echo "✅ Storage link already exists"
fi

# Start php-fpm or whatever command was passed
exec "$@"
