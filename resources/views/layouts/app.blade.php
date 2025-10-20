<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NekoDrop')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&family=M+PLUS+Rounded+1c:wght@400;500;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    @vite(['resources/css/app.css'])

    @stack('styles')
</head>
<body class="text-glass-primary">
    <!-- Navigation -->
    <nav class="glassmorphism-nav sticky top-0 z-50">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('downloads.create') }}" class="flex items-center text-glass-primary text-3xl font-bold text-shadow glass-hover">
                        <i class="fas fa-cat mr-2 text-glass-accent"></i>
                        <span style="font-family: 'M PLUS Rounded 1c', sans-serif;">NekoDrop</span>
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="{{ route('downloads.index') }}" class="text-glass-primary hover:text-glass-accent px-3 py-2 rounded-lg text-sm font-bold glass-hover transition-all duration-300">
                        <i class="fas fa-list mr-1"></i> DOWNLOAD
                    </a>
                    <a href="{{ route('platforms.index') }}" class="text-glass-primary hover:text-glass-accent px-3 py-2 rounded-lg text-sm font-bold glass-hover transition-all duration-300">
                        <i class="fas fa-cogs mr-1"></i> PLATFORM
                    </a>
                    <a href="{{ route('settings.index') }}" class="text-glass-primary hover:text-glass-accent px-3 py-2 rounded-lg text-sm font-bold glass-hover transition-all duration-300">
                        <i class="fas fa-sliders-h mr-1"></i> PENGATURAN
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 glassmorphism-alert text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="absolute top-0 bottom-0 right-0 px-4 py-3 glass-hover">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 glassmorphism-alert text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="absolute top-0 bottom-0 right-0 px-4 py-3 glass-hover">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="glassmorphism-card py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-glass-secondary">Made by NekoDrop Team</p>
                <p class="text-glass-secondary text-sm mt-2">© 2025 NekoDrop. All rights reserved. 🐾</p>
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
            const alertClass = type === 'success' ? 'text-green-700' : 'text-red-700';
            const alertHtml = `
                <div class="mb-4 glassmorphism-alert ${alertClass} px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">${message}</span>
                    <button onclick="this.parentElement.remove()" class="absolute top-0 bottom-0 right-0 px-4 py-3 glass-hover">
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
