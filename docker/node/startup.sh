#!/bin/bash

echo "🚀 Starting Node.js container..."

# Check if node_modules directory exists
if [ ! -d "/var/www/node_modules" ]; then
    echo "📦 Installing npm dependencies..."
    cd /var/www
    npm install
    npm run build
    echo "✅ npm dependencies installed and built successfully!"
else
    echo "✅ npm dependencies already exist, skipping installation"
    # Check if we need to rebuild (optional)
    if [ ! -d "/var/www/public/build" ]; then
        echo "🔨 Building frontend assets..."
        cd /var/www
        npm run build
        echo "✅ Frontend assets built successfully!"
    fi
fi

# Start the command that was passed
exec "$@"
