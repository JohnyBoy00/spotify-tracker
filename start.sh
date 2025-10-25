#!/bin/bash

# Spotify Tracker - Development Server Startup Script

echo "🎵 Starting Spotify Tracker Development Environment..."
echo ""

# Check if .env file exists
if [ ! -f .env ]; then
    echo "❌ .env file not found!"
    echo "Creating .env from .env.example..."
    cp .env.example .env
    php artisan key:generate
fi

# Check if database is configured
if grep -q "DB_CONNECTION=sqlite" .env; then
    echo "📊 Using SQLite database..."
    # Create database file if it doesn't exist
    if [ ! -f database/database.sqlite ]; then
        touch database/database.sqlite
        echo "✅ Created database/database.sqlite"
    fi
elif grep -q "DB_CONNECTION=mysql" .env; then
    echo "📊 Using MySQL database..."
    echo "⚠️  Make sure MySQL is running and database is created!"
fi

# Check if migrations have been run
echo ""
echo "🔄 Running database migrations..."
php artisan migrate --graceful

# Check if node modules are installed
if [ ! -d "node_modules" ]; then
    echo ""
    echo "📦 Installing npm dependencies..."
    npm install
fi

echo ""
echo "✅ Setup complete!"
echo ""
echo "🚀 Starting Laravel development server on http://localhost:8000"
echo "🎨 Starting Vite dev server for hot module replacement..."
echo ""
echo "Press Ctrl+C to stop the servers"
echo ""

# Start both servers in parallel
php artisan serve & npm run dev

