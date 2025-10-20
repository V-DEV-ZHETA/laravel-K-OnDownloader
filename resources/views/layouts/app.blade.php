<!DOCTYPE html>
<html lang="id" class="light">
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
    <link rel="stylesheet" href="{{ asset('css/enhanced-styles.css') }}">
    @vite(['resources/css/app.css'])

    @stack('styles')
</head>
<body class="text-glass-primary">
    <!-- Navigation -->
    <nav class="glassmorphism-nav sticky top-0 z-50">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('downloads.create') }}" class="flex items-center text-glass-primary text-3xl font-bold text-shadow glass-hover">
                        <i class="fas fa-cat mr-2 text-glass-accent"></i>
                        <span style="font-family: 'M PLUS Rounded 1c', sans-serif;">NekoDrop</span>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex flex-wrap items-center gap-2 sm:gap-4">
                    <a href="{{ route('downloads.index') }}" class="text-glass-primary hover:text-glass-accent px-3 py-2 rounded-lg text-sm font-bold glass-hover transition-all duration-300">
                        <i class="fas fa-list mr-1"></i> DOWNLOAD
                    </a>
                    <a href="{{ route('platforms.index') }}" class="text-glass-primary hover:text-glass-accent px-3 py-2 rounded-lg text-sm font-bold glass-hover transition-all duration-300">
                        <i class="fas fa-cogs mr-1"></i> PLATFORM
                    </a>
                    <a href="{{ route('settings.index') }}" class="text-glass-primary hover:text-glass-accent px-3 py-2 rounded-lg text-sm font-bold glass-hover transition-all duration-300">
                        <i class="fas fa-sliders-h mr-1"></i> PENGATURAN
                    </a>

                    <!-- Dark Mode Toggle -->
                    <button id="theme-toggle" class="text-glass-primary hover:text-glass-accent p-2 rounded-lg glass-hover transition-all duration-300 tooltip">
                        <i class="fas fa-moon text-lg"></i>
                        <span class="tooltip-text">Toggle Dark Mode</span>
                    </button>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex flex-wrap items-center gap-2">
                    <button id="mobile-menu-button" class="text-glass-primary hover:text-glass-accent p-2 rounded-lg glass-hover transition-all duration-300">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div id="mobile-menu" class="mobile-menu md:hidden fixed top-16 left-0 w-full h-screen glassmorphism-nav z-40">
                <div class="px-4 py-6 space-y-4">
                    <a href="{{ route('downloads.index') }}" class="block text-glass-primary hover:text-glass-accent px-3 py-2 rounded-lg text-sm font-bold glass-hover transition-all duration-300">
                        <i class="fas fa-list mr-2"></i> DOWNLOAD
                    </a>
                    <a href="{{ route('platforms.index') }}" class="block text-glass-primary hover:text-glass-accent px-3 py-2 rounded-lg text-sm font-bold glass-hover transition-all duration-300">
                        <i class="fas fa-cogs mr-2"></i> PLATFORM
                    </a>
                    <a href="{{ route('settings.index') }}" class="block text-glass-primary hover:text-glass-accent px-3 py-2 rounded-lg text-sm font-bold glass-hover transition-all duration-300">
                        <i class="fas fa-sliders-h mr-2"></i> PENGATURAN
                    </a>

                    <!-- Mobile Dark Mode Toggle -->
                    <button id="mobile-theme-toggle" class="flex items-center text-glass-primary hover:text-glass-accent px-3 py-2 rounded-lg text-sm font-bold glass-hover transition-all duration-300 w-full text-left">
                        <i class="fas fa-moon mr-2"></i> Dark Mode
                    </button>
                </div>
            </div>

            <!-- Mobile Menu Overlay -->
            <div id="mobile-menu-overlay" class="mobile-menu-overlay md:hidden fixed inset-0 bg-black bg-opacity-50 z-30"></div>
        </div>
    </nav>

    <!-- Toast Container -->
    <div id="toast-container" class="toast-container"></div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 glassmorphism-alert glassmorphism-alert-success text-green-700 px-4 py-3 rounded-lg relative fade-in" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="absolute top-0 bottom-0 right-0 px-4 py-3 glass-hover">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 glassmorphism-alert glassmorphism-alert-error text-red-700 px-4 py-3 rounded-lg relative fade-in" role="alert">
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

        // Theme Management
        function initTheme() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.className = savedTheme;
            updateThemeIcon(savedTheme);
        }

        function toggleTheme() {
            const currentTheme = document.documentElement.className;
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            document.documentElement.className = newTheme;
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
        }

        function updateThemeIcon(theme) {
            const icons = document.querySelectorAll('#theme-toggle i, #mobile-theme-toggle i');
            icons.forEach(icon => {
                icon.className = theme === 'dark' ? 'fas fa-sun text-lg' : 'fas fa-moon text-lg';
            });

            // Update mobile text
            const mobileText = document.querySelector('#mobile-theme-toggle');
            if (mobileText) {
                mobileText.innerHTML = theme === 'dark' ?
                    '<i class="fas fa-sun mr-2"></i> Light Mode' :
                    '<i class="fas fa-moon mr-2"></i> Dark Mode';
            }
        }

        // Mobile Menu Management
        function initMobileMenu() {
            const menuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const overlay = document.getElementById('mobile-menu-overlay');

            if (menuButton && mobileMenu && overlay) {
                menuButton.addEventListener('click', () => {
                    mobileMenu.classList.toggle('open');
                    overlay.classList.toggle('open');
                });

                overlay.addEventListener('click', () => {
                    mobileMenu.classList.remove('open');
                    overlay.classList.remove('open');
                });

                // Close menu when clicking on links
                mobileMenu.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        mobileMenu.classList.remove('open');
                        overlay.classList.remove('open');
                    });
                });
            }
        }

        // Toast Notifications
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toast-container');
            if (!toastContainer) return;

            const toast = document.createElement('div');
            toast.className = `toast ${type} fade-in`;
            toast.innerHTML = `
                <div class="flex flex-col items-start gap-2">
                    <div class="flex-shrink-0">
                        <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} text-lg mr-3"></i>
                    </div>
                    <div class="flex-1 mt-4 sm:mt-0">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-3 flex-shrink-0">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            `;

            toastContainer.appendChild(toast);

            // Show toast
            setTimeout(() => toast.classList.add('show'), 100);

            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        // Global functions
        function showAlert(message, type = 'success') {
            showToast(message, type);
        }

        function showLoading(element) {
            const originalText = element.html();
            element.data('original-text', originalText);
            element.html('<div class="loading-spinner mx-auto"></div>');
        }

        function hideLoading(element) {
            const originalText = element.data('original-text');
            element.html(originalText);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initTheme();
            initMobileMenu();

            // Theme toggle listeners
            document.getElementById('theme-toggle')?.addEventListener('click', toggleTheme);
            document.getElementById('mobile-theme-toggle')?.addEventListener('click', toggleTheme);
        });
    </script>

    @stack('scripts')
</body>
</html>
