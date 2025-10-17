# K-OnDownloader

A powerful media downloader built with Laravel that supports downloading videos and audio from YouTube, TikTok, Instagram, and Facebook with customizable quality settings and platform-specific options.

## Features

- 🎥 **Multi-Platform Support**: YouTube, TikTok, Instagram, Facebook
- 🎵 **Audio & Video**: Download in various formats (MP4, WebM, MP3, WAV, M4A)
- ⚙️ **Customizable Settings**: Platform-specific settings for each service
- 🎨 **Modern UI**: Beautiful, responsive interface built with Tailwind CSS
- 📱 **Mobile Friendly**: Works perfectly on all devices
- 🚀 **Fast Downloads**: Optimized for speed and reliability
- 🔧 **Easy Configuration**: Simple settings management

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- yt-dlp (YouTube downloader)
- FFmpeg (for audio conversion)

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd laravel-K-OnDownloader
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Setup

```bash
# Run migrations
php artisan migrate

# Seed the database with platform data
php artisan db:seed --class=PlatformSeeder
```

### 5. Install External Tools

#### Install yt-dlp

**Ubuntu/Debian:**
```bash
sudo apt update
sudo apt install yt-dlp
```

**macOS:**
```bash
brew install yt-dlp
```

**Windows:**
```bash
pip install yt-dlp
```

#### Install FFmpeg

**Ubuntu/Debian:**
```bash
sudo apt update
sudo apt install ffmpeg
```

**macOS:**
```bash
brew install ffmpeg
```

**Windows:**
Download from [FFmpeg website](https://ffmpeg.org/download.html)

### 6. Build Assets

```bash
npm run build
```

### 7. Start the Application

```bash
# Start Laravel development server
php artisan serve

# In another terminal, start Vite for asset compilation
npm run dev
```

Visit `http://localhost:8000` to access the application.

## Usage

### Basic Download

1. Go to the home page
2. Paste your video URL (YouTube, TikTok, Instagram, or Facebook)
3. Select quality and format preferences
4. Click "Start Download"
5. Wait for the download to complete
6. Download your file

### Platform Settings

Each platform has specific settings:

#### YouTube
- Quality selection (Best, 720p, 480p, etc.)
- Subtitle download
- Metadata embedding
- Audio-only mode

#### TikTok
- Quality selection
- Watermark removal
- Audio-only mode

#### Instagram
- Quality selection
- Stories inclusion
- Audio-only mode

#### Facebook
- Quality selection
- Subtitle download
- Audio-only mode

### Settings Management

1. Go to Settings page
2. Select a platform to configure
3. Adjust settings according to your preferences
4. Save changes
5. Settings will be applied to future downloads

## API Endpoints

### Downloads
- `GET /downloads` - List all downloads
- `POST /downloads` - Start new download
- `GET /downloads/{id}` - Show download details
- `DELETE /downloads/{id}` - Delete download

### Platform Management
- `GET /platforms` - List all platforms
- `GET /platforms/{platform}/settings` - Get platform settings
- `PATCH /platforms/{platform}/toggle` - Toggle platform status

### Settings
- `GET /settings` - List all settings
- `PUT /settings/{platform}` - Update platform settings
- `POST /settings/{platform}/reset` - Reset to default settings

## Configuration

### Environment Variables

Add these to your `.env` file:

```env
# Download settings
DOWNLOAD_PATH=storage/app/downloads
MAX_DOWNLOAD_SIZE=1073741824  # 1GB in bytes
DOWNLOAD_TIMEOUT=300  # 5 minutes

# Platform settings
YOUTUBE_DEFAULT_QUALITY=best
TIKTOK_DEFAULT_QUALITY=best
INSTAGRAM_DEFAULT_QUALITY=best
FACEBOOK_DEFAULT_QUALITY=best
```

### Storage Configuration

Make sure the storage directory is writable:

```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## Troubleshooting

### Common Issues

1. **yt-dlp not found**
   - Make sure yt-dlp is installed and accessible from command line
   - Check if it's in your PATH

2. **FFmpeg not found**
   - Install FFmpeg as mentioned in installation steps
   - Verify installation with `ffmpeg -version`

3. **Permission errors**
   - Check file permissions on storage directory
   - Ensure web server has write access

4. **Download failures**
   - Check if the URL is valid and accessible
   - Verify platform is supported
   - Check yt-dlp logs for specific errors

### Debug Mode

Enable debug mode in `.env`:

```env
APP_DEBUG=true
LOG_LEVEL=debug
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and questions:
- Create an issue on GitHub
- Check the troubleshooting section
- Review the documentation

## Disclaimer

This tool is for educational purposes only. Please respect the terms of service of the platforms you're downloading from and only download content you have permission to download.