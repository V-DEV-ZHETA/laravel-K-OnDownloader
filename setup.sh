#!/bin/bash

# K-OnDownloader Setup and Run Script

echo "🎵 K-OnDownloader Setup Script"
echo "================================"

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Please run this script from the Laravel project root directory"
    exit 1
fi

echo "📦 Installing dependencies..."

# Install PHP dependencies
echo "Installing PHP dependencies..."
composer install --no-interaction

# Install Node.js dependencies
echo "Installing Node.js dependencies..."
npm install

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
    php artisan key:generate
fi

# Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Seed the database
echo "🌱 Seeding database..."
php artisan db:seed --class=PlatformSeeder --force

# Create storage directories
echo "📁 Creating storage directories..."
mkdir -p storage/app/downloads
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# Build assets
echo "🎨 Building frontend assets..."
npm run build

echo ""
echo "✅ Setup completed successfully!"
echo ""
echo "🚀 To start the application:"
echo "   php artisan serve"
echo ""
echo "📱 Then visit: http://localhost:8000"
echo ""
echo "⚠️  Make sure you have installed:"
echo "   - yt-dlp (for downloading videos)"
echo "   - ffmpeg (for audio conversion)"
echo ""
echo "📖 Check README.md for detailed installation instructions"

