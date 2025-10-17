#!/bin/bash

# K-OnDownloader Complete Development Environment

echo "🎵 K-OnDownloader Complete Development Environment"
echo "=================================================="

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

echo "🚀 Starting complete development environment..."
echo "📱 Application will be available at: http://localhost:8000"
echo "🎨 Vite dev server will be available at: http://localhost:5173"
echo ""
echo "Press Ctrl+C to stop all servers"
echo ""

# Start Laravel development server
php artisan serve --host=0.0.0.0 --port=8000 &
LARAVEL_PID=$!

# Start Vite development server
npm run dev &
VITE_PID=$!

# Start queue worker
php artisan queue:work --tries=1 &
QUEUE_PID=$!

# Function to cleanup on exit
cleanup() {
    echo ""
    echo "🛑 Stopping all servers..."
    kill $LARAVEL_PID 2>/dev/null
    kill $VITE_PID 2>/dev/null
    kill $QUEUE_PID 2>/dev/null
    exit 0
}

# Set trap to cleanup on script exit
trap cleanup SIGINT SIGTERM

# Wait for all processes
wait $LARAVEL_PID $VITE_PID $QUEUE_PID