<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NekoDrop - Download Media Favorit Anda')</title>
    <meta name="description" content="Download video dan audio dari YouTube, TikTok, Instagram, dan Facebook dengan mudah dan cepat!">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&family=M+PLUS+Rounded+1c:wght@400;500;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/enhanced-styles.css') }}">
    @vite(['resources/css/app.css'])

    @stack('styles')

    <style>
        /* Additional inline styles for immediate loading */
        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
        }
        
        .logo-text {
            font-family: 'M PLUS Rounded 1c', sans-serif;
        }

        /* Mobile menu animations */
        #mobile-menu {
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #mobile-menu.open {
            transform: translateX(0);
        }

        #mobile-menu-overlay {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        #mobile-menu-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        /* Nav active state */
        .nav-link.active {
            background: rgba(255, 140, 66, 0.1);
            color: var(--color-primary);
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Loading spinner */
        .loading-spinner {
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Footer wave effect */
        .footer-wave {
            position: relative;
            overflow: hidden;
        }

        .footer-wave::before {
            content: '';
            position: absolute;
            top: -50px;
            left: 0;
            width: 100%;
            height: 100px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath d='M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z' fill='%23fff9f0' opacity='0.3'/%3E%3C/svg%3E") no-repeat;
            background-size: cover;
        }

        /* Enhanced tooltip */
        .tooltip-enhanced {
            position: relative;
        }

        .tooltip-enhanced::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(-8px);
            padding: 8px 12px;
            background: rgba(45, 55, 72, 0.95);
            color: white;
            font-size: 12px;
            font-weight: 600;
            border-radius: 8px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .tooltip-enhanced:hover::after {
            opacity: 1;
            transform: translateX(-50%) translateY(-4px);
        }

        /* Logo animation */
        .logo-container {
            transition: transform 0.3s ease;
        }
        
        .logo-container:hover {
            transform: scale(1.05);
        }

        /* Footer link animation */
        .footer-link {
            position: relative;
            transition: color 0.3s ease;
        }
        
        .footer-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(to right, #fb923c, #fbbf24);
            transition: width 0.3s ease;
        }
        
        .footer-link:hover::after {
            width: 100%;
        }
    </style>
</head>
<body class="text-glass-primary min-h-screen flex flex-col">
    <!-- Skip to content for accessibility -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-glass-accent text-white px-4 py-2 rounded-lg z-50">
        Skip to content
    </a>

    <!-- Navigation -->
    <nav class="glassmorphism-nav sticky top-0 z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('downloads.create') }}" class="flex items-center text-glass-primary text-2xl md:text-3xl font-bold ">
                        <div class="logo-container w-12 h-12 md:w-20 md:h-20 rounded-xl overflow-hidden">
                            <img src="https://ik.imagekit.io/hdxn6kcob/nekodrop.png?updatedAt=1761014269538" alt="NekoDrop Logo" class="w-full h-full object-cover">
                        </div>
                        <div class="flex flex-col">
                            <span class="logo-text text-orange-500">NEKODROP</span>
                            <span class="text-sm text-glass-secondary font-bold">MEDIA DOWNLOADER</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('downloads.create') }}" 
                       class="nav-link text-glass-primary hover:text-glass-accent px-4 py-2 rounded-xl text-sm font-bold glass-hover transition-all duration-300 flex items-center gap-2 {{ request()->routeIs('downloads.create') ? 'active' : '' }}">
                        <i class="fas fa-download"></i>
                        <span>UNDUH</span>
                    </a>
                    <a href="{{ route('downloads.index') }}" 
                       class="nav-link text-glass-primary hover:text-glass-accent px-4 py-2 rounded-xl text-sm font-bold glass-hover transition-all duration-300 flex items-center gap-2 {{ request()->routeIs('downloads.index') || request()->routeIs('downloads.show') ? 'active' : '' }}">
                        <i class="fas fa-history"></i>
                        <span>RIWAYAT</span>
                    </a>
                    <a href="{{ route('platforms.index') }}" 
                       class="nav-link text-glass-primary hover:text-glass-accent px-4 py-2 rounded-xl text-sm font-bold glass-hover transition-all duration-300 flex items-center gap-2 {{ request()->routeIs('platforms.*') ? 'active' : '' }}">
                        <i class="fas fa-globe"></i>
                        <span>PLATFORM</span>
                    </a>
                    <a href="{{ route('settings.index') }}" 
                       class="nav-link text-glass-primary hover:text-glass-accent px-4 py-2 rounded-xl text-sm font-bold glass-hover transition-all duration-300 flex items-center gap-2 {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i>
                        <span>PENGATURAN</span>
                    </a>

                    <!-- Divider -->
                    <div class="w-px h-8 bg-white/30 mx-3"></div>

                    <!-- Dark Mode Toggle -->
                    <button id="theme-toggle" 
                            class="tooltip-enhanced text-glass-primary hover:text-glass-accent p-2 rounded-xl glass-hover transition-all duration-300 hover:scale-110"
                            data-tooltip="Toggle Dark Mode"
                            aria-label="Toggle dark mode">
                        <i class="fas fa-moon text-lg"></i>
                    </button>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center gap-2">
                    <button id="theme-toggle-mobile" 
                            class="text-glass-primary hover:text-glass-accent p-2 rounded-xl glass-hover transition-all duration-300"
                            aria-label="Toggle dark mode">
                        <i class="fas fa-moon text-lg"></i>
                    </button>
                    <button id="mobile-menu-button" 
                            class="text-glass-primary hover:text-glass-accent p-2 rounded-xl glass-hover transition-all duration-300"
                            aria-label="Toggle menu"
                            aria-expanded="false">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Navigation Menu -->
    <div id="mobile-menu" class="md:hidden fixed top-16 left-0 w-64 h-full glassmorphism-nav shadow-2xl z-40 overflow-y-auto">
        <div class="p-6 space-y-3">
            <!-- User greeting (optional) -->
            <div class="glassmorphism-card rounded-xl p-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-glass-primary">Halo! 👋</p>
                        <p class="text-xs text-glass-secondary">Selamat datang</p>
                    </div>
                </div>
            </div>

            <a href="{{ route('downloads.create') }}" 
               class="nav-link block text-glass-primary hover:bg-white/50 px-4 py-3 rounded-xl text-sm font-bold glass-hover transition-all duration-300 {{ request()->routeIs('downloads.create') ? 'active' : '' }}">
                <i class="fas fa-download mr-3 w-5"></i>
                <span>UNDUH</span>
            </a>
            <a href="{{ route('downloads.index') }}" 
               class="nav-link block text-glass-primary hover:bg-white/50 px-4 py-3 rounded-xl text-sm font-bold glass-hover transition-all duration-300 {{ request()->routeIs('downloads.index') ? 'active' : '' }}">
                <i class="fas fa-history mr-3 w-5"></i>
                <span>RIWAYAT</span>
            </a>
            <a href="{{ route('platforms.index') }}" 
               class="nav-link block text-glass-primary hover:bg-white/50 px-4 py-3 rounded-xl text-sm font-bold glass-hover transition-all duration-300 {{ request()->routeIs('platforms.*') ? 'active' : '' }}">
                <i class="fas fa-globe mr-3 w-5"></i>
                <span>PLATFORM</span>
            </a>
            <a href="{{ route('settings.index') }}" 
               class="nav-link block text-glass-primary hover:bg-white/50 px-4 py-3 rounded-xl text-sm font-bold glass-hover transition-all duration-300 {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="fas fa-cog mr-3 w-5"></i>
                <span>PENGATURAN</span>
            </a>

            <!-- Divider -->
            <div class="border-t border-white/30 my-4"></div>

            <!-- Mobile Dark Mode Toggle -->
            <button id="mobile-theme-toggle" 
                    class="flex items-center w-full text-glass-primary hover:bg-white/50 px-4 py-3 rounded-xl text-sm font-bold glass-hover transition-all duration-300">
                <i class="fas fa-moon mr-3 w-5"></i>
                <span>Dark Mode</span>
            </button>

            <!-- Social Links (optional) -->
            <div class="pt-4">
                <p class="text-xs text-glass-secondary font-semibold mb-3 px-4">IKUTI KAMI</p>
                <div class="flex gap-2 px-4">
                    <a href="#" class="w-10 h-10 glassmorphism-card rounded-lg flex items-center justify-center glass-hover">
                        <i class="fab fa-github text-glass-primary"></i>
                    </a>
                    <a href="#" class="w-10 h-10 glassmorphism-card rounded-lg flex items-center justify-center glass-hover">
                        <i class="fab fa-twitter text-glass-primary"></i>
                    </a>
                    <a href="#" class="w-10 h-10 glassmorphism-card rounded-lg flex items-center justify-center glass-hover">
                        <i class="fab fa-discord text-glass-primary"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu-overlay" class="md:hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-30"></div>

    <!-- Main Content -->
    <main id="main-content" class="flex-1">
        <div class="py-6">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                    <div class="glassmorphism-alert-success rounded-xl p-4 flex items-start gap-3 fade-in shadow-lg" role="alert">
                        <i class="fas fa-check-circle text-green-600 text-xl flex-shrink-0 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="font-semibold text-green-800">Berhasil!</p>
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                    <div class="glassmorphism-alert-error rounded-xl p-4 flex items-start gap-3 fade-in shadow-lg" role="alert">
                        <i class="fas fa-exclamation-circle text-red-600 text-xl flex-shrink-0 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="font-semibold text-red-800">Error!</p>
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if(session('warning'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                    <div class="glassmorphism-alert-warning rounded-xl p-4 flex items-start gap-3 fade-in shadow-lg" role="alert">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl flex-shrink-0 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="font-semibold text-yellow-800">Peringatan!</p>
                            <p class="text-sm text-yellow-700">{{ session('warning') }}</p>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-yellow-600 hover:text-yellow-800 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="glassmorphism-card py-12 mt-20 footer-wave">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Brand -->
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-xl overflow-hidden shadow-lg">
                            <img src="https://ik.imagekit.io/hdxn6kcob/nekodrop.png?updatedAt=1761014269538" alt="NekoDrop Logo" class="w-full h-full object-cover">
                        </div>
                        <span class="logo-text text-2xl font-bold text-glass-primary">NekoDrop</span>
                    </div>
                    <p class="text-glass-secondary mb-4 max-w-md">
                        Download media favorit Anda dari berbagai platform dengan mudah, cepat, dan gratis! 🐱✨
                    </p>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 glassmorphism-card rounded-lg flex items-center justify-center glass-hover transition-all duration-300 hover:scale-110">
                            <i class="fab fa-github text-glass-primary"></i>
                        </a>
                        <a href="#" class="w-10 h-10 glassmorphism-card rounded-lg flex items-center justify-center glass-hover transition-all duration-300 hover:scale-110">
                            <i class="fab fa-twitter text-glass-primary"></i>
                        </a>
                        <a href="#" class="w-10 h-10 glassmorphism-card rounded-lg flex items-center justify-center glass-hover transition-all duration-300 hover:scale-110">
                            <i class="fab fa-discord text-glass-primary"></i>
                        </a>
                        <a href="#" class="w-10 h-10 glassmorphism-card rounded-lg flex items-center justify-center glass-hover transition-all duration-300 hover:scale-110">
                            <i class="fab fa-telegram text-glass-primary"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="font-bold text-glass-primary mb-4 text-lg">Tautan Cepat</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('downloads.create') }}" class="footer-link text-glass-secondary hover:text-glass-accent transition-colors flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-orange-400"></i>Unduh Media
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('downloads.index') }}" class="footer-link text-glass-secondary hover:text-glass-accent transition-colors flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-orange-400"></i>Riwayat Download
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('platforms.index') }}" class="footer-link text-glass-secondary hover:text-glass-accent transition-colors flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-orange-400"></i>Platform
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h3 class="font-bold text-glass-primary mb-4 text-lg">Dukungan</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="#" class="footer-link text-glass-secondary hover:text-glass-accent transition-colors flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-orange-400"></i>Pusat Bantuan
                            </a>
                        </li>
                        <li>
                            <a href="#" class="footer-link text-glass-secondary hover:text-glass-accent transition-colors flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-orange-400"></i>FAQ
                            </a>
                        </li>
                        <li>
                            <a href="#" class="footer-link text-glass-secondary hover:text-glass-accent transition-colors flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-orange-400"></i>Hubungi Kami
                            </a>
                        </li>
                        <li>
                            <a href="#" class="footer-link text-glass-secondary hover:text-glass-accent transition-colors flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-orange-400"></i>Lapor Bug
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-white/30 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-glass-secondary text-sm text-center md:text-left">
                        © {{ date('Y') }} <span class="font-bold">NekoDrop</span>. Made with <i class="fas fa-heart text-red-500"></i> by NekoDrop Team. All rights reserved. 🐾
                    </p>
                    <div class="flex gap-4 text-sm">
                        <a href="#" class="footer-link text-glass-secondary hover:text-glass-accent transition-colors">Privasi</a>
                        <span class="text-glass-secondary">•</span>
                        <a href="#" class="footer-link text-glass-secondary hover:text-glass-accent transition-colors">Syarat & Ketentuan</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scroll-to-top" 
            class="fixed bottom-6 right-6 w-12 h-12 glassmorphism-button text-white rounded-full shadow-2xl opacity-0 pointer-events-none transition-all duration-300 hover:scale-110 z-40"
            aria-label="Scroll to top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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
            
            // Show toast
            showToast(newTheme === 'dark' ? 'Mode gelap diaktifkan 🌙' : 'Mode terang diaktifkan ☀️', 'info');
        }

        function updateThemeIcon(theme) {
            const icons = document.querySelectorAll('#theme-toggle i, #theme-toggle-mobile i');
            icons.forEach(icon => {
                icon.className = theme === 'dark' ? 'fas fa-sun text-lg' : 'fas fa-moon text-lg';
            });

            // Update mobile text
            const mobileText = document.querySelector('#mobile-theme-toggle span');
            if (mobileText) {
                mobileText.textContent = theme === 'dark' ? 'Light Mode' : 'Dark Mode';
            }
            
            const mobileIcon = document.querySelector('#mobile-theme-toggle i');
            if (mobileIcon) {
                mobileIcon.className = theme === 'dark' ? 'fas fa-sun mr-3 w-5' : 'fas fa-moon mr-3 w-5';
            }
        }

        // Mobile Menu Management
        function initMobileMenu() {
            const menuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const overlay = document.getElementById('mobile-menu-overlay');

            if (menuButton && mobileMenu && overlay) {
                menuButton.addEventListener('click', () => {
                    const isOpen = mobileMenu.classList.contains('open');
                    mobileMenu.classList.toggle('open');
                    overlay.classList.toggle('open');
                    menuButton.setAttribute('aria-expanded', !isOpen);
                    
                    // Change icon
                    const icon = menuButton.querySelector('i');
                    icon.className = isOpen ? 'fas fa-bars text-xl' : 'fas fa-times text-xl';
                });

                overlay.addEventListener('click', () => {
                    mobileMenu.classList.remove('open');
                    overlay.classList.remove('open');
                    menuButton.setAttribute('aria-expanded', 'false');
                    menuButton.querySelector('i').className = 'fas fa-bars text-xl';
                });

                // Close menu when clicking on links
                mobileMenu.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        mobileMenu.classList.remove('open');
                        overlay.classList.remove('open');
                        menuButton.setAttribute('aria-expanded', 'false');
                        menuButton.querySelector('i').className = 'fas fa-bars text-xl';
                    });
                });
            }
        }

        // Toast Notifications
        function showToast(message, type = 'success') {
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };
            
            const colors = {
                success: 'from-green-400 to-green-500',
                error: 'from-red-400 to-red-500',
                warning: 'from-yellow-400 to-yellow-500',
                info: 'from-blue-400 to-blue-500'
            };

            const toast = $(`
                <div class="toast glassmorphism-card rounded-xl shadow-2xl p-4 max-w-sm border-2 border-white/30 mb-3 opacity-0 translate-x-full transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br ${colors[type]} rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas ${icons[type]} text-white"></i>
                        </div>
                        <p class="flex-1 text-glass-primary font-medium">${message}</p>
                        <button class="text-glass-secondary hover:text-glass-primary transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `);

            toast.find('button').on('click', function() {
                removeToast(toast);
            });

            if ($('#toast-container').length === 0) {
                $('body').append('<div id="toast-container" class="fixed top-20 right-4 z-50 space-y-3 pointer-events-none"></div>');
            }

            $('#toast-container').append(toast);
            
            setTimeout(() => {
                toast.removeClass('opacity-0 translate-x-full').addClass('pointer-events-auto');
            }, 100);

            setTimeout(() => removeToast(toast), 5000);
        }

        function removeToast(toast) {
            toast.addClass('opacity-0 translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }

        // Global functions
        function showAlert(message, type = 'success') {
            showToast(message, type);
        }

        function showLoading(element) {
            const $element = $(element);
            const originalText = $element.html();
            $element.data('original-text', originalText);
            $element.prop('disabled', true).html('<div class="loading-spinner mx-auto"></div>');
        }

        function hideLoading(element, originalText = null) {
            const $element = $(element);
            const text = originalText || $element.data('original-text');
            $element.prop('disabled', false).html(text);
        }

        // Scroll to Top Button
        function initScrollToTop() {
            const button = document.getElementById('scroll-to-top');
            
            if (button) {
                window.addEventListener('scroll', () => {
                    if (window.scrollY > 300) {
                        button.classList.remove('opacity-0', 'pointer-events-none');
                        button.classList.add('opacity-100');
                    } else {
                        button.classList.add('opacity-0', 'pointer-events-none');
                        button.classList.remove('opacity-100');
                    }
                });

                button.addEventListener('click', () => {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }
        }

        // Auto-dismiss flash messages
        function initFlashMessages() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        }

        // Keyboard shortcuts
        function initKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                // ESC to close mobile menu
                if (e.key === 'Escape') {
                    const mobileMenu = document.getElementById('mobile-menu');
                    const overlay = document.getElementById('mobile-menu-overlay');
                    if (mobileMenu && mobileMenu.classList.contains('open')) {
                        mobileMenu.classList.remove('open');
                        overlay.classList.remove('open');
                    }
                }

                // Ctrl/Cmd + K to focus search (if exists)
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    const searchInput = document.querySelector('input[type="search"], input[placeholder*="Cari"]');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }
            });
        }

        // Page loading animation
        function initPageLoad() {
            document.body.style.opacity = '0';
            setTimeout(() => {
                document.body.style.transition = 'opacity 0.3s ease';
                document.body.style.opacity = '1';
            }, 100);
        }

        // Performance monitoring (optional)
        function initPerformanceMonitoring() {
            if ('performance' in window) {
                window.addEventListener('load', () => {
                    const perfData = window.performance.timing;
                    const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
                    
                    if (pageLoadTime > 3000) {
                        console.warn('Page load time:', pageLoadTime + 'ms');
                    }
                });
            }
        }

        // Service Worker registration (for PWA - optional)
        function initServiceWorker() {
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    // Uncomment when service worker is ready
                    // navigator.serviceWorker.register('/sw.js')
                    //     .then(reg => console.log('Service Worker registered'))
                    //     .catch(err => console.log('Service Worker registration failed'));
                });
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initTheme();
            initMobileMenu();
            initScrollToTop();
            initFlashMessages();
            initKeyboardShortcuts();
            initPageLoad();
            // initPerformanceMonitoring();
            // initServiceWorker();

            // Theme toggle listeners
            document.getElementById('theme-toggle')?.addEventListener('click', toggleTheme);
            document.getElementById('theme-toggle-mobile')?.addEventListener('click', toggleTheme);
            document.getElementById('mobile-theme-toggle')?.addEventListener('click', toggleTheme);

            // Add active class to current nav item
            const currentPath = window.location.pathname;
            document.querySelectorAll('.nav-link').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });

            // Animate elements on scroll (optional)
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                observer.observe(el);
            });
        });

        // Handle online/offline status
        window.addEventListener('online', () => {
            showToast('Koneksi internet tersambung kembali! 🌐', 'success');
        });

        window.addEventListener('offline', () => {
            showToast('Koneksi internet terputus! 📡', 'warning');
        });

        // Prevent form resubmission on refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        // Debug mode (only in development)
        const DEBUG = {{ config('app.debug') ? 'true' : 'false' }};
        if (DEBUG) {
            console.log('NekoDrop Debug Mode Enabled 🐱');
            console.log('Current Route:', '{{ Route::currentRouteName() }}');
        }
    </script>

    @stack('scripts')
</body>
</html>