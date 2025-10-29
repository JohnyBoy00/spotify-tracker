#!/bin/bash

# Spotify Tracker - Stop All Servers Script

echo "Stopping Spotify Tracker servers..."

# Stop PHP development servers
pkill -f "php.*-S localhost:8001" 2>/dev/null
pkill -f "php.*artisan serve" 2>/dev/null

# Stop queue workers
pkill -f "php.*artisan queue:work" 2>/dev/null

# Stop scheduler
pkill -f "php.*artisan schedule:work" 2>/dev/null

echo "All servers stopped."
echo ""
echo "To start again, run: ./serve.sh"

