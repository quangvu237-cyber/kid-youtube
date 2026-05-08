#!/bin/bash

echo "🚀 Starting Node.js dev container..."

if [ ! -d "/var/www/node_modules" ]; then
    echo "📦 Installing npm dependencies..."
    cd /var/www
    npm install
else
    echo "✅ npm dependencies already exist"
fi

exec "$@"