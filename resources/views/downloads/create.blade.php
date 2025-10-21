@extends('layouts.app')

@section('title', 'Unduh Media - NekoDrop')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Hero Section dengan Ilustrasi Kucing -->
    <div class="text-center mb-12">
        <div class="inline-block mb-6">
            <div class="relative">
                {{-- <div class="w-24 h-24 glassmorphism-card rounded-full flex items-center justify-center shadow-xl animate-bounce">
                    <i class="fas fa-cat text-5xl text-glass-accent"></i>
                </div> --}}
                <div class="absolute -top-2 -right-2 w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg animate-pulse">
                    <i class="fas fa-download text-white"></i>
                </div>
            </div>
        </div>
        {{-- <h1 class="text-4xl md:text-6xl font-extrabold text-glass-primary mb-4 bg-clip-text text-transparent bg-gradient-to-r from-orange-500 via-pink-500 to-purple-500">
            NekoDrop Downloader
        </h1>
        <p class="text-lg md:text-xl text-glass-secondary max-w-3xl mx-auto leading-relaxed">
            Unduh Media dari <span class="font-semibold text-glass-accent">YouTube, Instagram, TikTok, dan Facebook</span> dengan Mudah & Cepat 🐱✨
        </p> --}}
    </div>

    <!-- Kartu Unduhan Utama -->
    <div class="glassmorphism-card rounded-3xl p-6 md:p-10 mb-10 shadow-2xl border-2 border-white/30 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23f97316" fill-opacity="0.4"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        
        <form id="downloadForm" class="space-y-8 relative z-10">
            <!-- Input URL dengan Drag & Drop yang Ditingkatkan -->
            <div class="relative">
                <label for="url" class="block text-base font-bold text-glass-primary mb-4 flex items-center gap-3">
                    <span class="text-lg">Tempel URL Media</span>
                </label>
                
                <div id="urlDropZone" class="relative group">
                    <input type="url"
                           id="url"
                           name="url"
                           class="glassmorphism-input w-full px-6 py-5 rounded-2xl text-glass-primary text-lg transition-all  border-2"
                           placeholder="https://youtube.com/watch?v=... atau tarik & lepas URL di sini"
                           required>
                    
                    <!-- Overlay Drag & Drop -->
                    <div id="dropOverlay" class="absolute inset-0 rounded-2xl border-4 border-dashed  bg-gradient-to-br from-orange-50 to-yellow-5a0 opacity-0 transition-all duration-300 pointer-events-none flex items-center justify-center z-10">
                        <div class="text-center">
                            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-2xl">
                                <i class="fas fa-cloud-upload-alt text-4xl text-orange-500"></i>
                            </div>
                            <p class="text-xl font-bold text-glass-primary">Lepaskan URL di sini</p>
                            <p class="text-sm text-glass-secondary mt-2">Untuk menempelkan secara otomatis</p>
                        </div>
                    </div>
                    
                    <!-- Ikon Platform Helper -->
                    <div class="absolute right-5 top-1/2 -translate-y-1/2 flex items-center gap-3 pointer-events-none opacity-40 group-focus-within:opacity-80 transition-all duration-300">
                        <i class="fab fa-youtube text-red-500 text-xl"></i>
                        <i class="fab fa-tiktok text-black text-xl"></i>
                        <i class="fab fa-instagram text-pink-500 text-xl"></i>
                        <i class="fab fa-facebook text-blue-600 text-xl"></i>
                    </div>
                </div>
                
                <p class="mt-3 text-sm text-glass-secondary flex items-center gap-2 ml-1">
                    <i class="fas fa-info-circle text-glass-accent"></i>
                    <span>Tempel link atau seret & lepas URL ke sini</span>
                </p>
            </div>

            <!-- Platform Selection (Auto-detect) -->
            <div class="hidden">
                <label for="platform" class="block text-base font-bold text-glass-primary mb-4 flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-globe text-white"></i>
                    </div>
                    <span>Platform</span>
                    <span class="badge-secondary text-xs ml-2 px-3 py-1 rounded-full">Terdeteksi Otomatis</span>
                </label>
                <select id="platform"
                        name="platform"
                        class="glass-select w-full px-6 py-4 rounded-2xl text-glass-primary transition-all duration-300 border-2 border-transparent focus:border-purple-400">
                    <option value="">Mendeteksi platform...</option>
                    @foreach($platforms as $platform)
                        <option value="{{ $platform->name }}" data-icon="{{ $platform->icon }}">
                            {{ $platform->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Kartu Preview Video (Tersembunyi Awalnya) -->
            <div id="videoInfo" class="hidden opacity-0 transition-all duration-500 ease-out transform scale-95">
                <div class="glassmorphism-card rounded-2xl p-6 border-2 border-orange-200/50 shadow-xl">
                    <div class="flex flex-col md:flex-row gap-5">
                        <!-- Thumbnail -->
                        <div id="videoThumbnail" class="w-full md:w-56 h-36 glassmorphism-card rounded-xl overflow-hidden flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 flex-shrink-0 shadow-lg">
                            <div class="flex flex-col items-center gap-3">
                                <i class="fas fa-spinner fa-spin text-4xl text-glass-accent"></i>
                                <span class="text-sm font-medium text-glass-secondary">Memuat...</span>
                            </div>
                        </div>
                        
                        <!-- Detail Video -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start gap-4">
                                <div id="platformBadge" class="w-12 h-12 glassmorphism-card rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                                    <i class="fas fa-video text-glass-accent text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 id="videoTitle" class="text-xl font-bold text-glass-primary line-clamp-2 mb-3">
                                        Mendeteksi video...
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-4 text-sm text-glass-secondary">
                                        <span id="videoUploader" class="flex items-center gap-2 bg-white/50 px-3 py-1 rounded-lg">
                                            <i class="fas fa-user"></i>
                                            <span class="font-medium">Tidak Diketahui</span>
                                        </span>
                                        <span id="videoDuration" class="flex items-center gap-2 bg-white/50 px-3 py-1 rounded-lg">
                                            <i class="fas fa-clock"></i>
                                            <span class="font-medium">--:--</span>
                                        </span>
                                        <span id="videoViews" class="flex items-center gap-2 bg-white/50 px-3 py-1 rounded-lg hidden">
                                            <i class="fas fa-eye"></i>
                                            <span class="font-medium">0</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Opsi Unduhan (Tampil setelah video terdeteksi) -->
            <div id="downloadOptions" class="hidden opacity-0 transition-all duration-500 ease-out transform scale-95">
                <div class="space-y-6">
                    <!-- Grid Opsi -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Pemilihan Kualitas -->
                        <div id="qualitySection" class="hidden glassmorphism-card rounded-2xl p-6  border-2 border-white/30">
                            <label for="quality" class="block text-base font-bold text-glass-primary mb-4 flex items-center gap-3">
                                <i class="fas fa-hd-video text-glass-accent text-xl"></i>
                                <span>Kualitas Video</span>
                            </label>
                            <select id="quality"
                                    name="quality"
                                    class="glass-select w-full px-5 py-4 rounded-xl text-glass-primary transition-all duration-300 border-2 border-transparent focus:border-green-400 text-base">
                                <option value="best">🌟 Kualitas Terbaik (Direkomendasikan)</option>
                                <option value="1080p">📺 1080p Full HD</option>
                                <option value="720p">📱 720p HD</option>
                                <option value="480p">💻 480p SD</option>
                                <option value="360p">📞 360p</option>
                                <option value="240p">⚡ 240p (Tercepat)</option>
                            </select>
                        </div>

                        <!-- Pemilihan Format -->
                        <div id="formatSection" class="hidden glassmorphism-card rounded-2xl p-6 border-2 border-white/30">
                            <label for="format" class="block text-base font-bold text-glass-primary mb-4 flex items-center gap-3">
                                <i class="fas fa-file-video text-glass-accent text-xl"></i>
                                <span>Format File</span>
                            </label>
                            <select id="format"
                                    name="format"
                                    class="glass-select w-full px-5 py-4 rounded-xl text-glass-primary transition-all duration-300 border-2 border-transparent focus:border-green-400 text-base">
                                <option value="mp4">🎬 MP4 Video (Universal)</option>
                                <option value="webm">🌐 WebM Video (Lebih Kecil)</option>
                                <option value="mp3">🎵 MP3 Audio</option>
                                <option value="m4a">🎶 M4A Audio (Lebih Baik)</option>
                                <option value="wav">🎼 WAV Audio (Tanpa Kompresi)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Opsi Cepat -->
                    <div class="glassmorphism-card rounded-2xl p-6 border-2 border-white/30">
                        <div class="flex flex-wrap items-center gap-5">
                            <label class="flex items-center gap-4 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox"
                                           id="audioOnly"
                                           name="audio_only"
                                           class="peer sr-only">
                                    <div class="w-14 h-7 bg-gray-300 rounded-full peer-checked:bg-gradient-to-r peer-checked:from-orange-400 peer-checked:to-orange-600 transition-all duration-300 shadow-inner"></div>
                                    <div class="absolute left-1 top-1 w-5 h-5 bg-white rounded-full transition-all duration-300 peer-checked:translate-x-7 shadow-lg"></div>
                                </div>
                                <span class="text-base font-bold text-glass-primary group-hover:text-glass-accent transition-colors">
                                    <i class="fas fa-music mr-2 text-orange-500"></i>
                                    Mode Audio Saja
                                </span>
                            </label>
                        </div>
                        <p class="text-sm text-glass-secondary mt-3 ml-16">Ekstrak audio tanpa video</p>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6">
                <button type="submit"
                        id="downloadBtn"
                        class="hidden flex-1 glassmorphism-button text-white font-bold py-5 px-10 rounded-2xl glass-hover text-lg transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed group shadow-xl hover:shadow-2xl hover:scale-[1.02]">
                    <span class="flex items-center justify-center gap-3">
                        <i class="fas fa-download text-xl transition-transform duration-300 group-hover:scale-125"></i>
                        <span id="downloadBtnText">Mulai Unduh</span>
                    </span>
                </button>
                <button type="button"
                        id="resetBtn"
                        class="hidden flex-1 glassmorphism-button text-white font-bold py-5 px-10 rounded-2xl glass-hover text-lg transition-all duration-300 group shadow-xl hover:shadow-2xl hover:scale-[1.02]">
                    <span class="flex items-center justify-center gap-3">
                        <i class="fas fa-redo text-xl transition-transform duration-300 group-hover:scale-125"></i>
                        <span>Reset</span>
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- Kartu Statistik -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-10">
        <div class="glassmorphism-card rounded-2xl p-6 text-center glass-hover transition-all duration-300 hover:scale-105 shadow-lg border-2 border-white/30">
            <div class="text-4xl font-extrabold bg-gradient-to-r from-orange-500 to-pink-500 bg-clip-text text-black mb-2">4</div>
            <div class="text-sm font-semibold text-glass-secondary">Platform</div>
        </div>
        <div class="glassmorphism-card rounded-2xl p-6 text-center glass-hover transition-all duration-300 hover:scale-105 shadow-lg border-2 border-white/30">
            <div class="text-4xl font-extrabold bg-gradient-to-r from-blue-500 to-purple-500 bg-clip-text text-black mb-2">4K</div>
            <div class="text-sm font-semibold text-glass-secondary">Kualitas</div>
        </div>
        <div class="glassmorphism-card rounded-2xl p-6 text-center glass-hover transition-all duration-300 hover:scale-105 shadow-lg border-2 border-white/30">
            <div class="text-4xl font-extrabold bg-gradient-to-r from-green-500 to-teal-500 bg-clip-text text-black mb-2">∞</div>
            <div class="text-sm font-semibold text-glass-secondary">Unduhan</div>
        </div>
        <div class="glassmorphism-card rounded-2xl p-6 text-center glass-hover transition-all duration-300 hover:scale-105 shadow-lg border-2 border-white/30">
            <div class="text-4xl font-extrabold bg-gradient-to-r from-yellow-500 to-orange-500 bg-clip-text text-black mb-2">100%</div>
            <div class="text-sm font-semibold text-glass-secondary">Gratis</div>
        </div>
    </div>

    <!-- Seksi Unduhan Terbaru -->
    <div class="glassmorphism-card rounded-3xl p-6 md:p-10 shadow-2xl border-2 border-white/30 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0 " style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23f97316" fill-opacity="0.4"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-history text-black text-3xl"></i>
                    </div>
                    <h2 class="text-2xl md:text-3xl font-bold text-glass-primary">Unduhan Terbaru</h2>
                </div>
                <button id="refreshDownloads" class="glassmorphism-card px-5 py-3 rounded-xl text-sm font-bold text-glass-primary hover:bg-white/60 transition-all duration-300 glass-hover shadow-md hover:scale-105">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Muat Ulang
                </button>
            </div>
            
            <div id="recentDownloads" class="space-y-4">
                <!-- Loading State -->
                <div class="text-center py-16">
                    <i class="fas fa-spinner fa-spin text-5xl text-glass-accent mb-6"></i>
                    <p class="text-lg text-glass-secondary font-medium">Memuat unduhan Anda...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kontainer Toast -->
<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-3 pointer-events-none max-w-sm">
    <!-- Toasts akan dimasukkan di sini -->
</div>
@endsection

@push('styles')
<style>
/* Animasi Kustom */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

@keyframes pulse-soft {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

@keyframes glow {
    0%, 100% {
        box-shadow: 0 0 5px rgba(249, 115, 22, 0.5);
    }
    50% {
        box-shadow: 0 0 20px rgba(249, 115, 22, 0.8);
    }
}

.fade-in {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Status Drag & Drop */
#urlDropZone.drag-over #dropOverlay {
    opacity: 1;
    pointer-events: auto;
    border-color: var(--color-primary);
}

/* Gaya Toast */
.toast {
    pointer-events: auto;
    animation: slideInRight 0.3s ease-out;
}

.toast.hiding {
    animation: slideOutRight 0.3s ease-out;
}

/* Status Tombol Loading */
.btn-loading {
    position: relative;
    pointer-events: none;
}

.btn-loading::after {
    content: '';
    position: absolute;
    width: 24px;
    height: 24px;
    top: 50%;
    left: 50%;
    margin-left: -12px;
    margin-top: -12px;
    border: 3px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Status Fokus yang Ditingkatkan */
input:focus, select:focus {
    outline: none;
}

/* Scrollbar Kustom */
#recentDownloads {
    max-height: 700px;
    overflow-y: auto;
}

#recentDownloads::-webkit-scrollbar {
    width: 8px;
}

#recentDownloads::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}

#recentDownloads::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #f97316, #fb923c);
    border-radius: 10px;
}

#recentDownloads::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #ea580c, #f97316);
}

/* Animasi Bounce untuk Hero */
@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.animate-bounce {
    animation: bounce 2s infinite;
}

/* Efek Gradient Text */
.bg-clip-text {
    -webkit-background-clip: text;
    background-clip: text;
}

/* Futuristic Progress Bar */
.progress-bar {
    height: 6px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
    overflow: hidden;
    position: relative;
}

.progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: var(--progress, 0%);
    background: linear-gradient(90deg, #f97316, #fb923c);
    border-radius: 3px;
    transition: width 0.3s ease;
}

/* Glowing Effect */
.glow-effect {
    animation: glow 2s infinite;
}

/* Futuristic Card Hover */
.futuristic-card {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.futuristic-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.futuristic-card:hover::before {
    left: 100%;
}

/* Futuristic Button */
.futuristic-button {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.futuristic-button::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.5s, height 0.5s;
}

.futuristic-button:active::before {
    width: 300px;
    height: 300px;
}

/* Enhanced Glassmorphism */
.glassmorphism-enhanced {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
}

/* Futuristic Input */
.futuristic-input {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    transition: all 0.3s ease;
}

.futuristic-input:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(249, 115, 22, 0.5);
    box-shadow: 0 0 15px rgba(249, 115, 22, 0.2);
}

/* Futuristic Select */
.futuristic-select {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    transition: all 0.3s ease;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23f97316' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    padding-right: 2.5rem;
}

.futuristic-select:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(249, 115, 22, 0.5);
    box-shadow: 0 0 15px rgba(249, 115, 22, 0.2);
}

/* Futuristic Checkbox */
.futuristic-checkbox {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 26px;
}

.futuristic-checkbox input {
    opacity: 0;
    width: 0;
    height: 0;
}

.futuristic-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    transition: 0.4s;
    border-radius: 34px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.futuristic-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 4px;
    bottom: 4px;
    background: white;
    transition: 0.4s;
    border-radius: 50%;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

input:checked + .futuristic-slider {
    background: linear-gradient(to right, #f97316, #fb923c);
}

input:checked + .futuristic-slider:before {
    transform: translateX(24px);
}

/* Futuristic Badge */
.futuristic-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Futuristic Tooltip */
.futuristic-tooltip {
    position: relative;
}

.futuristic-tooltip::before {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    padding: 0.5rem 1rem;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    font-size: 0.875rem;
    border-radius: 0.5rem;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s;
    z-index: 1000;
}

.futuristic-tooltip:hover::before {
    opacity: 1;
}

/* Futuristic Loading Spinner */
.futuristic-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid rgba(255, 255, 255, 0.1);
    border-top: 4px solid #f97316;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

/* Futuristic Pulse Animation */
@keyframes futuristic-pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(249, 115, 22, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(249, 115, 22, 0);
    }
}

.futuristic-pulse {
    animation: futuristic-pulse 2s infinite;
}
</style>
@endpush

@push('scripts')
<script>
 $(document).ready(function() {
    let currentPlatform = null;
    let dragCounter = 0;

    // Fungsi Drag and Drop
    const dropZone = document.getElementById('urlDropZone');
    const urlInput = document.getElementById('url');
    const dropOverlay = document.getElementById('dropOverlay');

    // Cegah perilaku drag default
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // Handle drag enter
    dropZone.addEventListener('dragenter', function(e) {
        dragCounter++;
        dropZone.classList.add('drag-over');
    });

    // Handle drag leave
    dropZone.addEventListener('dragleave', function(e) {
        dragCounter--;
        if (dragCounter === 0) {
            dropZone.classList.remove('drag-over');
        }
    });

    // Handle drop
    dropZone.addEventListener('drop', function(e) {
        dragCounter = 0;
        dropZone.classList.remove('drag-over');
        
        const dt = e.dataTransfer;
        const url = dt.getData('text/plain');

        if (url) {
            urlInput.value = url;
            urlInput.dispatchEvent(new Event('input'));
            showToast('URL berhasil ditempelkan! 🐱', 'success');
        }
    });

    // Deteksi otomatis platform saat URL berubah
    let detectTimeout;
    $('#url').on('input', function() {
        const url = $(this).val().trim();
        
        // Hapus timeout sebelumnya
        clearTimeout(detectTimeout);
        
        if (url) {
            // Debounce detection
            detectTimeout = setTimeout(() => {
                detectPlatform(url);
            }, 500);
        } else {
            hideVideoInfo();
            hideElement('#downloadOptions');
        }
    });

    // Opsi spesifik platform
    $('#platform').on('change', function() {
        currentPlatform = $(this).val();
        updatePlatformOptions();
    });

    // Handler checkbox audio saja
    $('#audioOnly').on('change', function() {
        const isAudioOnly = $(this).is(':checked');
        
        if (isAudioOnly) {
            $('#format').html(`
                <option value="mp3">🎵 MP3 Audio (Terbaik)</option>
                <option value="m4a">🎶 M4A Audio (Apple)</option>
                <option value="wav">🎼 WAV Audio (Tanpa Kompresi)</option>
                <option value="flac">💿 FLAC Audio (Hi-Fi)</option>
            `);
            $('#quality').html(`
                <option value="320kbps">🌟 320kbps (Terbaik)</option>
                <option value="256kbps">📻 256kbps (Tinggi)</option>
                <option value="192kbps">🎧 192kbps (Bagus)</option>
                <option value="128kbps">⚡ 128kbps (Cepat)</option>
            `);
            $('label[for="quality"] span').text('Kualitas Audio');
            $('label[for="quality"] i').removeClass('fa-hd-video').addClass('fa-music');
        } else {
            $('#format').html(`
                <option value="mp4">🎬 MP4 Video (Universal)</option>
                <option value="webm">🌐 WebM Video (Lebih Kecil)</option>
                <option value="mp3">🎵 MP3 Audio</option>
                <option value="m4a">🎶 M4A Audio (Lebih Baik)</option>
                <option value="wav">🎼 WAV Audio (Tanpa Kompresi)</option>
            `);
            $('#quality').html(`
                <option value="best">🌟 Kualitas Terbaik (Direkomendasikan)</option>
                <option value="1080p">📺 1080p Full HD</option>
                <option value="720p">📱 720p HD</option>
                <option value="480p">💻 480p SD</option>
                <option value="360p">📞 360p</option>
                <option value="240p">⚡ 240p (Tercepat)</option>
            `);
            $('label[for="quality"] span').text('Kualitas Video');
            $('label[for="quality"] i').removeClass('fa-music').addClass('fa-hd-video');
        }
    });

    // Pengiriman formulir
    $('#downloadForm').on('submit', function(e) {
        e.preventDefault();

        const url = $('#url').val().trim();
        if (!url) {
            showToast('Silakan masukkan URL yang valid', 'error');
            return;
        }

        const formData = {
            url: url,
            platform: $('#platform').val(),
            quality: $('#quality').val(),
            format: $('#format').val(),
            audio_only: $('#audioOnly').is(':checked')
        };

        const submitBtn = $('#downloadBtn');
        setButtonLoading(submitBtn, true);

        $.ajax({
            url: '/api/download',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function(response) {
                if (response.success) {
                    showToast('Unduhan berhasil dimulai! 🎉', 'success');
                    // Reset formulir setelah unduhan berhasil
                    setTimeout(() => {
                        resetForm();
                    }, 1000);
                    loadRecentDownloads();
                } else {
                    showToast(response.message || 'Gagal memulai unduhan', 'error');
                }
            },
            error: function(xhr) {
                console.error('Error unduhan:', xhr);
                let errorMessage = 'Terjadi kesalahan';

                if (xhr.responseJSON) {
                    errorMessage = xhr.responseJSON.message || errorMessage;
                } else if (xhr.status === 419) {
                    errorMessage = 'Sesi berakhir. Silakan muat ulang halaman.';
                } else if (xhr.status === 422) {
                    errorMessage = 'URL atau pengaturan tidak valid. Silakan periksa dan coba lagi.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Kesalahan server. Silakan coba lagi nanti.';
                }

                showToast(errorMessage, 'error');
            },
            complete: function() {
                setButtonLoading(submitBtn, false);
            }
        });
    });

    // Tombol reset
    $('#resetBtn').on('click', function() {
        resetForm();
        showToast('Formulir direset! 🔄', 'info');
    });

    // Muat ulang unduhan
    $('#refreshDownloads').on('click', function() {
        const btn = $(this);
        btn.find('i').addClass('fa-spin');
        loadRecentDownloads();
        setTimeout(() => {
            btn.find('i').removeClass('fa-spin');
        }, 1000);
    });

    // Muat unduhan terbaru saat halaman dimuat
    loadRecentDownloads();

    // Fungsi Helper
    function detectPlatform(url) {
        showElement('#videoInfo');
        $('#videoThumbnail').html(`
            <div class="flex flex-col items-center gap-3">
                <div class="futuristic-spinner"></div>
                <span class="text-sm font-medium text-glass-secondary">Mendeteksi...</span>
            </div>
        `);
        $('#videoTitle').text('Menganalisis video...');

        $.ajax({
            url: '/api/video-info',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ url: url }),
            success: function(response) {
                if (response.success) {
                    showVideoInfo(response.info);
                    if (response.platform) {
                        $('#platform').val(response.platform);
                        currentPlatform = response.platform;
                        updatePlatformOptions();
                        updateQualityOptions(url, response.platform);
                    }
                    // Show download options, quality, format, download and reset buttons after successful verification
                    showElement('#downloadOptions');
                    $('#qualitySection').removeClass('hidden');
                    $('#formatSection').removeClass('hidden');
                    $('#downloadBtn').removeClass('hidden');
                    $('#resetBtn').removeClass('hidden');
                } else {
                    hideVideoInfo();
                    showToast('Tidak dapat mendeteksi info video', 'warning');
                    // Hide download options, quality, format, download and reset buttons if verification fails
                    hideElement('#downloadOptions');
                    $('#qualitySection').addClass('hidden');
                    $('#formatSection').addClass('hidden');
                    $('#downloadBtn').addClass('hidden');
                    $('#resetBtn').addClass('hidden');
                }
            },
            error: function(xhr) {
                console.error('Error deteksi platform:', xhr);
                hideVideoInfo();
                showToast('Gagal mendeteksi platform. Silakan periksa URL.', 'error');
                // Hide download options, quality, format, download and reset buttons if verification fails
                hideElement('#downloadOptions');
                $('#qualitySection').addClass('hidden');
                $('#formatSection').addClass('hidden');
                $('#downloadBtn').addClass('hidden');
                $('#resetBtn').addClass('hidden');
            }
        });
    }

    function updateQualityOptions(url, platform) {
        $.ajax({
            url: '/downloads/formats',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ url: url, platform: platform }),
            success: function(response) {
                if (response.success && response.quality_options) {
                    let qualityHtml = '';
                    const emojis = { '1080p': '📺', '720p': '📱', '480p': '💻', '360p': '📞', '240p': '⚡', 'best': '🌟' };
                    
                    for (const [value, label] of Object.entries(response.quality_options)) {
                        const emoji = emojis[value] || '📹';
                        qualityHtml += `<option value="${value}">${emoji} ${label}</option>`;
                    }
                    $('#quality').html(qualityHtml);
                }
            },
            error: function(xhr) {
                console.error('Error update opsi kualitas:', xhr);
            }
        });
    }

    function showVideoInfo(info) {
        let thumbnailUrl = '';

        // Untuk TikTok dan Facebook, gunakan thumbnail lokal jika tersedia
        if ((currentPlatform === 'tiktok' || currentPlatform === 'facebook') && info.thumbnail && info.thumbnail.startsWith('thumbnails/')) {
            thumbnailUrl = '/storage/' + info.thumbnail;
        } else if (info.thumbnail) {
            thumbnailUrl = '/thumbnail-proxy/' + btoa(info.thumbnail);
        }

        if (thumbnailUrl) {
            $('#videoThumbnail').html(`
                <img src="${thumbnailUrl}"
                     alt="Thumbnail video"
                     class="w-full h-full object-cover rounded-xl"
                     onerror="this.onerror=null; this.src='/images/placeholder.jpg';">
            `);
        } else {
            $('#videoThumbnail').html(`
                <i class="fas fa-video text-5xl text-glass-secondary"></i>
            `);
        }

        $('#videoTitle').text(info.title || 'Judul Tidak Diketahui');
        $('#videoDuration').find('span').text(info.duration || '--:--');
        $('#videoUploader').find('span').text(info.uploader || 'Tidak Diketahui');

        if (info.view_count) {
            $('#videoViews').removeClass('hidden').find('span').text(formatNumber(info.view_count));
        } else {
            $('#videoViews').addClass('hidden');
        }

        // Update badge platform
        const platformIcons = {
            'youtube': 'fab fa-youtube',
            'tiktok': 'fab fa-tiktok',
            'instagram': 'fab fa-instagram',
            'facebook': 'fab fa-facebook'
        };
        const icon = platformIcons[currentPlatform] || 'fas fa-video';
        $('#platformBadge').html(`<i class="${icon} text-glass-accent text-xl"></i>`);

        showElement('#videoInfo');
    }

    function hideVideoInfo() {
        hideElement('#videoInfo');
    }

    function updatePlatformOptions() {
        if (currentPlatform === 'instagram') {
            $('#qualitySection').slideUp();
        } else {
            $('#qualitySection').slideDown();
        }
    }

    function loadRecentDownloads() {
        $.ajax({
            url: '{{ route("downloads.index") }}',
            method: 'GET',
            success: function(response) {
                if (response.success && response.downloads && response.downloads.length > 0) {
                    let html = '';
                    response.downloads.slice(0, 5).forEach(function(download) {
                        const statusColors = {
                            'completed': 'bg-green-100 text-green-800',
                            'processing': 'bg-blue-100 text-blue-800',
                            'pending': 'bg-yellow-100 text-yellow-800',
                            'failed': 'bg-red-100 text-red-800'
                        };
                        const statusIcons = {
                            'completed': 'fa-check-circle',
                            'processing': 'fa-spinner fa-spin',
                            'pending': 'fa-clock',
                            'failed': 'fa-exclamation-circle'
                        };
                        const statusLabels = {
                            'completed': 'Selesai',
                            'processing': 'Memproses',
                            'pending': 'Menunggu',
                            'failed': 'Gagal'
                        };
                        
                        const statusColor = statusColors[download.status] || 'bg-gray-100 text-gray-800';
                        const statusIcon = statusIcons[download.status] || 'fa-question-circle';
                        const statusLabel = statusLabels[download.status] || download.status;
                        
                        html += `
                            <div class="glassmorphism-card rounded-2xl p-5 glass-hover transition-all duration-300 hover:scale-[1.01] hover:shadow-xl border-2 border-white/30 futuristic-card">
                                <div class="flex flex-col sm:flex-row items-start gap-5">
                                    <!-- Ikon Platform -->
                                    <div class="w-16 h-16 glassmorphism-card rounded-2xl flex items-center justify-center flex-shrink-0 bg-gradient-to-br from-white to-gray-50 shadow-lg">
                                        <i class="${download.platform_icon || 'fas fa-video'} text-3xl text-glass-accent"></i>
                                    </div>
                                    
                                    <!-- Info Unduhan -->
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-glass-primary mb-2 line-clamp-2 text-lg">${download.title || 'Judul Tidak Diketahui'}</h4>
                                        <div class="flex flex-wrap items-center gap-3 text-sm text-glass-secondary mb-3">
                                            <span class="flex items-center gap-2 bg-white/50 px-3 py-1 rounded-lg">
                                                <i class="fas fa-globe text-xs"></i>
                                                <span class="font-medium">${download.platform_display_name || download.platform}</span>
                                            </span>
                                            <span class="flex items-center gap-2 bg-white/50 px-3 py-1 rounded-lg">
                                                <i class="fas fa-calendar text-xs"></i>
                                                <span class="font-medium">${download.created_at}</span>
                                            </span>
                                            ${download.file_size ? `
                                                <span class="flex items-center gap-2 bg-white/50 px-3 py-1 rounded-lg">
                                                    <i class="fas fa-database text-xs"></i>
                                                    <span class="font-medium">${download.file_size}</span>
                                                </span>
                                            ` : ''}
                                        </div>
                                        
                                        <!-- Status dan Aksi -->
                                        <div class="flex flex-wrap items-center gap-3">
                                            <span class="px-4 py-2 text-sm font-bold rounded-xl ${statusColor} flex items-center gap-2 shadow-md futuristic-badge">
                                                <i class="fas ${statusIcon}"></i>
                                                ${statusLabel}
                                            </span>
                                            ${download.status === 'completed' ? `
                                                <a href="/downloads/${download.id}/download" 
                                                   class="px-5 py-2 glassmorphism-button-accent text-white text-sm font-bold rounded-xl glass-hover flex items-center gap-2 transition-all duration-300 shadow-lg hover:scale-105 futuristic-button">
                                                    <i class="fas fa-download"></i>
                                                    Unduh
                                                </a>
                                            ` : ''}
                                            ${download.status === 'processing' ? `
                                                <span class="px-4 py-2 text-sm text-glass-secondary flex items-center gap-3">
                                                    <div class="progress-bar" style="--progress: ${download.progress || 50}%"></div>
                                                    <span class="font-bold">${download.progress || 50}%</span>
                                                </span>
                                            ` : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $('#recentDownloads').html(html);
                } else {
                    $('#recentDownloads').html(`
                        <div class="text-center py-20">
                            <div class="w-28 h-28 mx-auto mb-8 glassmorphism-card rounded-full flex items-center justify-center shadow-xl futuristic-pulse">
                                <i class="fas fa-download text-6xl text-glass-secondary"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-glass-primary mb-3">Belum Ada Unduhan</h3>
                            <p class="text-glass-secondary max-w-md mx-auto text-lg leading-relaxed">
                                Media yang Anda unduh akan muncul di sini. Mulai dengan menempelkan URL di atas! 🐱✨
                            </p>
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                console.error('Error muat unduhan terbaru:', xhr);
                $('#recentDownloads').html(`
                    <div class="text-center py-20">
                        <div class="w-28 h-28 mx-auto mb-8 glassmorphism-card rounded-full flex items-center justify-center bg-red-50 shadow-xl">
                            <i class="fas fa-exclamation-triangle text-6xl text-red-500"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-glass-primary mb-3">Gagal Memuat Unduhan</h3>
                        <p class="text-glass-secondary mb-6 text-lg">Silakan coba muat ulang halaman</p>
                        <button onclick="location.reload()" class="glassmorphism-button text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:scale-105 transition-all duration-300 futuristic-button">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Muat Ulang Halaman
                        </button>
                    </div>
                `);
            }
        });
    }

    function resetForm() {
        $('#downloadForm')[0].reset();
        hideVideoInfo();
        hideElement('#downloadOptions');
        $('#qualitySection').addClass('hidden');
        $('#formatSection').addClass('hidden');
        $('#url').val('').focus();
        dragCounter = 0;
        // Hide download button on reset
        $('#downloadBtn').addClass('hidden');
        $('#resetBtn').addClass('hidden');
    }

    function setButtonLoading(button, isLoading) {
        const textEl = button.find('#downloadBtnText');
        
        if (isLoading) {
            button.prop('disabled', true);
            button.addClass('btn-loading');
            textEl.html('<span class="opacity-0">Memproses...</span>');
        } else {
            button.prop('disabled', false);
            button.removeClass('btn-loading');
            textEl.text('Mulai Unduh');
        }
    }

    function showElement(selector) {
        $(selector).removeClass('hidden').addClass('opacity-100 scale-100').removeClass('opacity-0 scale-95');
    }

    function hideElement(selector) {
        $(selector).addClass('opacity-0 scale-95').removeClass('opacity-100 scale-100');
        setTimeout(() => {
            $(selector).addClass('hidden');
        }, 300);
    }

    function formatNumber(num) {
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'Jt';
        } else if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'Rb';
        }
        return num.toString();
    }

    function showToast(message, type = 'info') {
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        
        const colors = {
            success: 'from-green-400 to-green-600',
            error: 'from-red-400 to-red-600',
            warning: 'from-yellow-400 to-yellow-600',
            info: 'from-blue-400 to-blue-600'
        };

        const toast = $(`
            <div class="toast glassmorphism-card rounded-2xl shadow-2xl p-5 max-w-sm border-2 border-white/40 futuristic-card">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br ${colors[type]} rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg futuristic-pulse">
                        <i class="fas ${icons[type]} text-white text-xl"></i>
                    </div>
                    <p class="flex-1 text-glass-primary font-semibold text-base">${message}</p>
                    <button class="text-glass-secondary hover:text-glass-primary transition-colors ml-2">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
        `);

        toast.find('button').on('click', function() {
            removeToast(toast);
        });

        $('#toastContainer').append(toast);

        setTimeout(() => {
            removeToast(toast);
        }, 5000);
    }

    function removeToast(toast) {
        toast.addClass('hiding');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }

    // Muat ulang unduhan otomatis setiap 30 detik jika ada item yang sedang diproses
    setInterval(function() {
        if ($('#recentDownloads').find('.fa-spinner').length > 0) {
            loadRecentDownloads();
        }
    }, 30000);
});
</script>
@endpush