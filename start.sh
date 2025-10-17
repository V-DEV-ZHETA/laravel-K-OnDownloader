#!/bin/bash

# K-OnDownloader Development Server

echo "🎵 Starting K-OnDownloader Development Server"
echo "============================================="

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Please run this script from the Laravel project root directory"
    exit 1
fi

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "❌ Error: .env file not found. Please run ./setup.sh first"
    exit 1
fi

echo "🚀 Starting Laravel development server..."
echo "📱 Application will be available at: http://localhost:8000"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""

# Start the Laravel development server
php artisan serve --host=0.0.0.0 --port=8000

