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

# Start php-fpm or whatever command was passed
exec "$@"
