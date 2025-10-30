#!/bin/bash

# Spotify Tracker - Development Server Startup Script
# This script starts the PHP development server with custom settings for large file uploads

echo "Starting Spotify Tracker development server..."
echo "Server will be available at: http://localhost:8001"
echo ""
echo "Configuration:"
echo "  - Upload max filesize: 50MB"
echo "  - POST max size: 50MB"
echo "  - Max execution time: 300 seconds"
echo "  - Memory limit: 512MB"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""

php -d upload_max_filesize=50M \
    -d post_max_size=50M \
    -d max_execution_time=300 \
    -d memory_limit=512M \
    -S localhost:8001 \
    -t public

