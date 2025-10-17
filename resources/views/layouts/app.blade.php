<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'K-OnDownloader')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: transform 0.2s ease-in-out;
        }
        .card-hover:hover {
            transform: translateY(-2px);
        }
        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="gradient-bg shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('downloads.create') }}" class="flex items-center text-white text-xl font-bold">
                        <i class="fas fa-download mr-2"></i>
                        K-OnDownloader
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('downloads.index') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-list mr-1"></i> Downloads
                    </a>
                    <a href="{{ route('platforms.index') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-cogs mr-1"></i> Platforms
                    </a>
                    <a href="{{ route('settings.index') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-sliders-h mr-1"></i> Settings
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-gray-400">&copy; 2024 K-OnDownloader. Download videos and audio from YouTube, TikTok, Instagram, and Facebook.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // CSRF token setup for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Global functions
        function showAlert(message, type = 'success') {
            const alertClass = type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700';
            const alertHtml = `
                <div class="mb-4 ${alertClass} border px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">${message}</span>
                    <button onclick="this.parentElement.remove()" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            $('main').prepend(alertHtml);
        }

        function showLoading(element) {
            element.html('<div class="loading-spinner mx-auto"></div>');
        }

        function hideLoading(element, originalText) {
            element.html(originalText);
        }
    </script>
    
    @stack('scripts')
</body>
</html>

