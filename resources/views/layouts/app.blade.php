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
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #FCA5A5 100%);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #FFB6C1 0%, #FCA5A5 100%);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 182, 193, 0.3);
        }

        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #FFB6C1;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .btn-kawaii {
            background: linear-gradient(135deg, #FFB6C1 0%, #E6D0F7 100%);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-kawaii:hover {
            background: linear-gradient(135deg, #E6D0F7 0%, #FFF3B0 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 182, 193, 0.4);
        }

        .glassmorphism {
            background: rgba(255, 249, 250, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 182, 193, 0.3);
        }

        .glassmorphism-card {
            background: rgba(255, 255, 255, 0.7);
            /* backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px); */
            border: 1px solid rgba(255, 182, 193, 0.2);
            box-shadow: 0 8px 32px rgba(255, 182, 193, 0.1);
        }

        .glassmorphism-nav {
            background: rgba(255, 255, 255, 0.9);

            border-bottom: 1px solid rgba(0, 0, 0, 0.2);
        }

        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .bounce {
            aniftion: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="glassmorphism-nav shadow-lg sticky top-0 z-50">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('downloads.create') }}" class="flex items-center text-black text-3xl font-bold text-shadow">
                        <i class="fas fa-cat mr-2 "></i>
                        <span style="font-family: 'M PLUS Rounded 1c', sans-serif;">NekoDrop</span>
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="{{ route('downloads.index') }}" class="text-black hover:text-gray-200 px-3 py-2 rounded-md text-sm font-bold transition duration-200">
                        <i class="fas fa-list mr-1"></i> DOWNLOAD
                    </a>
                    <a href="{{ route('platforms.index') }}" class="text-black hover:text-gray-200 px-3 py-2 rounded-md text-sm font-bold transition duration-200">
                        <i class="fas fa-cogs mr-1"></i> PLATFORM
                    </a>
                    <a href="{{ route('settings.index') }}" class="text-black hover:text-gray-200 px-3 py-2 rounded-md text-sm font-bold transition duration-200">
                        <i class="fas fa-sliders-h mr-1"></i> PENGATURAN
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative glassmorphism-card" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative glassmorphism-card" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="absolute top-0 bottom-0 right-0 px-4 py-3">
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
                <p class="text-gray-600">Made by NekoDrop Team</p>
                <p class="text-gray-500 text-sm mt-2">© 2025 NekoDrop. All rights reserved. 🐾</p>
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
                <div class="mb-4 ${alertClass} border px-4 py-3 rounded-lg relative glassmorphism-card" role="alert">
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
