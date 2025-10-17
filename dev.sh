#!/bin/bash

# K-OnDownloader Development Server with Vite

echo "🎵 Starting K-OnDownloader with Vite Development Server"
echo "========================================================"

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
echo "🎨 Starting Vite development server..."
echo "📱 Application will be available at: http://localhost:8000"
echo ""
echo "Press Ctrl+C to stop both servers"
echo ""

# Start both servers concurrently
php artisan serve --host=0.0.0.0 --port=8000 &
LARAVEL_PID=$!

npm run dev &
VITE_PID=$!

# Function to cleanup on exit
cleanup() {
    echo ""
    echo "🛑 Stopping servers..."
    kill $LARAVEL_PID 2>/dev/null
    kill $VITE_PID 2>/dev/null
    exit 0
}

# Set trap to cleanup on script exit
trap cleanup SIGINT SIGTERM

# Wait for both processes
wait $LARAVEL_PID $VITE_PID

