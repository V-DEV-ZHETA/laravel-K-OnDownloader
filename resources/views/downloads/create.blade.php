@extends('layouts.app')

@section('title', 'Download Media - NekoDrop')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Hero Section with Cat Illustration -->
    <div class="text-center mb-8">
        <div class="inline-block mb-4">
            {{-- <div class="relative">
                <div class="w-20 h-20 glassmorphism-card rounded-full flex items-center justify-center">
                    <i class="fas fa-cat text-4xl text-glass-accent"></i>
                </div>
                <div class="absolute -top-2 -right-2 w-8 h-8 bg-gradient-to-br from-yellow-400 to-orange-400 rounded-full flex items-center justify-center animate-bounce">
                    <i class="fas fa-download text-white text-xs"></i>
                </div>
            </div>
        </div> --}}
        <h1 class="text-4xl md:text-5xl font-bold text-glass-primary mb-3">
            NekoDrop Downloader
        </h1>
        <p class="text-lg text-glass-secondary max-w-3xl mx-auto">
            Download Media Dari Platform Youtube, Instagram, Tiktok, Dan Facebook Dengan Mudah🐱
        </p>
    </div>

    <!-- Main Download Card -->
    <div class="glassmorphism-card rounded-2xl p-6 md:p-8 mb-8 shadow-xl">
        <form id="downloadForm" class="space-y-6">
            <!-- URL Input with Enhanced Drag & Drop -->
            <div class="relative">
                <label for="url" class="block text-sm font-semibold text-glass-primary mb-3 flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-orange-400 to-orange-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-link text-white text-sm"></i>
                    </div>
                    <span>Paste Media URL</span>
                </label>
                
                <div id="urlDropZone" class="relative group">
                    <input type="url"
                           id="url"
                           name="url"
                           class="glassmorphism-input w-full px-5 py-4 rounded-xl text-glass-primary placeholder-glass-secondary text-base transition-all duration-300 focus:scale-[1.01]"
                           placeholder="https://youtube.com/watch?v=..."
                           required>
                    
                    <!-- Drag & Drop Overlay -->
                    <div id="dropOverlay" class="absolute inset-0 rounded-xl border-3 border-dashed border-transparent bg-gradient-to-br from-orange-50 to-yellow-50 opacity-0 transition-all duration-300 pointer-events-none flex items-center justify-center">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                                <i class="fas fa-cloud-upload-alt text-3xl text-orange-500"></i>
                            </div>
                            <p class="text-lg font-semibold text-glass-primary">Drop URL here</p>
                            <p class="text-sm text-glass-secondary">Release to paste</p>
                        </div>
                    </div>
                    
                    <!-- Platform Icons Helper -->
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-2 pointer-events-none opacity-40 group-focus-within:opacity-70 transition-opacity">
                        <i class="fab fa-youtube text-red-500"></i>
                        <i class="fab fa-tiktok text-black"></i>
                        <i class="fab fa-instagram text-pink-500"></i>
                        <i class="fab fa-facebook text-blue-600"></i>
                    </div>
                </div>
                
                <p class="mt-2 text-sm text-glass-secondary flex items-center gap-2">
                    <i class="fas fa-info-circle text-glass-accent"></i>
                    <span>Paste a link or drag & drop it here</span>
                </p>
            </div>

            <!-- Platform Selection (Auto-detect) -->
            <div class="hidden">
                <label for="platform" class="block text-sm font-semibold text-glass-primary mb-3 flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-purple-400 to-purple-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-globe text-white text-sm"></i>
                    </div>
                    <span>Platform</span>
                    <span class="badge-secondary text-xs ml-2">Auto-detected</span>
                </label>
                <select id="platform"
                        name="platform"
                        class="glass-select w-full px-5 py-4 rounded-xl text-glass-primary transition-all duration-300">
                    <option value="">Detecting platform...</option>
                    @foreach($platforms as $platform)
                        <option value="{{ $platform->name }}" data-icon="{{ $platform->icon }}">
                            {{ $platform->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Video Preview Card (Initially Hidden) -->
            <div id="videoInfo" class="hidden opacity-0 transition-all duration-500 ease-out transform scale-95">
                <div class="glassmorphism-card rounded-xl p-5 border-2 border-orange-200/50">
                    <div class="flex flex-col md:flex-row gap-4">
                        <!-- Thumbnail -->
                        <div id="videoThumbnail" class="w-full md:w-48 h-32 glassmorphism-card rounded-lg overflow-hidden flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 flex-shrink-0">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-spinner fa-spin text-3xl text-glass-accent"></i>
                                <span class="text-sm text-glass-secondary">Loading...</span>
                            </div>
                        </div>
                        
                        <!-- Video Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start gap-3">
                                <div id="platformBadge" class="w-10 h-10 glassmorphism-card rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-video text-glass-accent"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 id="videoTitle" class="text-lg font-bold text-glass-primary line-clamp-2 mb-2">
                                        Detecting video...
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-3 text-sm text-glass-secondary">
                                        <span id="videoUploader" class="flex items-center gap-1">
                                            <i class="fas fa-user text-xs"></i>
                                            <span>Unknown</span>
                                        </span>
                                        <span id="videoDuration" class="flex items-center gap-1">
                                            <i class="fas fa-clock text-xs"></i>
                                            <span>--:--</span>
                                        </span>
                                        <span id="videoViews" class="flex items-center gap-1 hidden">
                                            <i class="fas fa-eye text-xs"></i>
                                            <span>0</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Download Options (Show after video detected) -->
            <div id="downloadOptions" class="hidden opacity-0 transition-all duration-500 ease-out transform scale-95">
                <div class="space-y-5">
                    <!-- Section Title -->
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-green-400 to-green-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-sliders-h text-white text-sm"></i>
                        </div>
                        <h3 class="text-lg font-bold text-glass-primary">Download Settings</h3>
                    </div>

                    <!-- Options Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Quality Selection -->
                        <div id="qualitySection" class="glassmorphism-card rounded-xl p-5 transition-all duration-300 hover:shadow-lg">
                            <label for="quality" class="block text-sm font-semibold text-glass-primary mb-3 flex items-center gap-2">
                                <i class="fas fa-hd-video text-glass-accent"></i>
                                <span>Video Quality</span>
                            </label>
                            <select id="quality"
                                    name="quality"
                                    class="glass-select w-full px-4 py-3 rounded-lg text-glass-primary transition-all duration-300">
                                <option value="best">🌟 Best Quality (Recommended)</option>
                                <option value="1080p">📺 1080p Full HD</option>
                                <option value="720p">📱 720p HD</option>
                                <option value="480p">💻 480p SD</option>
                                <option value="360p">📞 360p</option>
                                <option value="240p">⚡ 240p (Fastest)</option>
                            </select>
                        </div>

                        <!-- Format Selection -->
                        <div class="glassmorphism-card rounded-xl p-5 transition-all duration-300 hover:shadow-lg">
                            <label for="format" class="block text-sm font-semibold text-glass-primary mb-3 flex items-center gap-2">
                                <i class="fas fa-file-video text-glass-accent"></i>
                                <span>File Format</span>
                            </label>
                            <select id="format"
                                    name="format"
                                    class="glass-select w-full px-4 py-3 rounded-lg text-glass-primary transition-all duration-300">
                                <option value="mp4">🎬 MP4 Video (Universal)</option>
                                <option value="webm">🌐 WebM Video (Smaller)</option>
                                <option value="mp3">🎵 MP3 Audio</option>
                                <option value="m4a">🎶 M4A Audio (Better)</option>
                                <option value="wav">🎼 WAV Audio (Lossless)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Quick Options -->
                    <div class="glassmorphism-card rounded-xl p-5">
                        <div class="flex flex-wrap items-center gap-4">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox"
                                           id="audioOnly"
                                           name="audio_only"
                                           class="peer sr-only">
                                    <div class="w-11 h-6 bg-gray-300 rounded-full peer-checked:bg-gradient-to-r peer-checked:from-orange-400 peer-checked:to-orange-500 transition-all duration-300"></div>
                                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-all duration-300 peer-checked:translate-x-5 shadow-md"></div>
                                </div>
                                <span class="text-sm font-medium text-glass-primary group-hover:text-glass-accent transition-colors">
                                    <i class="fas fa-music mr-1"></i>
                                    Audio Only Mode
                                </span>
                            </label>
                        </div>
                        <p class="text-xs text-glass-secondary mt-2 ml-14">Extract audio without video</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button type="submit"
                        id="downloadBtn"
                        class="flex-1 glassmorphism-button text-white font-bold py-4 px-8 rounded-xl glass-hover text-lg transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed group">
                    <span class="flex items-center justify-center gap-3">
                        <i class="fas fa-download transition-transform duration-300 group-hover:scale-110"></i>
                        <span id="downloadBtnText">Start Download</span>
                    </span>
                </button>
                <button type="button"
                        id="resetBtn"
                        class="sm:w-auto px-6 py-4 glassmorphism-card rounded-xl font-semibold text-glass-primary hover:bg-white/50 transition-all duration-300 glass-hover">
                    <i class="fas fa-redo mr-2"></i>
                    Reset
                </button>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="glassmorphism-card rounded-xl p-4 text-center glass-hover">
            <div class="text-3xl font-bold text-glass-accent mb-1">4</div>
            <div class="text-sm text-glass-secondary">Platforms</div>
        </div>
        <div class="glassmorphism-card rounded-xl p-4 text-center glass-hover">
            <div class="text-3xl font-bold text-glass-accent mb-1">HD</div>
            <div class="text-sm text-glass-secondary">Quality</div>
        </div>
        <div class="glassmorphism-card rounded-xl p-4 text-center glass-hover">
            <div class="text-3xl font-bold text-glass-accent mb-1">∞</div>
            <div class="text-sm text-glass-secondary">Downloads</div>
        </div>
        <div class="glassmorphism-card rounded-xl p-4 text-center glass-hover">
            <div class="text-3xl font-bold text-glass-accent mb-1">Free</div>
            <div class="text-sm text-glass-secondary">Forever</div>
        </div>
    </div>

    <!-- Recent Downloads Section -->
    <div class="glassmorphism-card rounded-2xl p-6 md:p-8 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-history text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-glass-primary">Recent Downloads</h2>
            </div>
            <button id="refreshDownloads" class="glassmorphism-card px-4 py-2 rounded-lg text-sm font-medium text-glass-primary hover:bg-white/50 transition-all duration-300 glass-hover">
                <i class="fas fa-sync-alt mr-2"></i>
                Refresh
            </button>
        </div>
        
        <div id="recentDownloads" class="space-y-3">
            <!-- Loading State -->
            <div class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-glass-accent mb-4"></i>
                <p class="text-glass-secondary">Loading your downloads...</p>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-3 pointer-events-none">
    <!-- Toasts will be inserted here -->
</div>
@endsection

@push('styles')
<style>
/* Custom Animations */
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

/* Drag & Drop States */
#urlDropZone.drag-over #dropOverlay {
    opacity: 1;
    pointer-events: auto;
    border-color: var(--color-primary);
}

/* Toast Styles */
.toast {
    pointer-events: auto;
    animation: slideInRight 0.3s ease-out;
}

.toast.hiding {
    animation: slideOutRight 0.3s ease-out;
}

/* Loading Button State */
.btn-loading {
    position: relative;
    pointer-events: none;
}

.btn-loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    top: 50%;
    left: 50%;
    margin-left: -10px;
    margin-top: -10px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Improved Focus States */
input:focus, select:focus {
    outline: none;
}

/* Custom Scrollbar */
#recentDownloads {
    max-height: 600px;
    overflow-y: auto;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let currentPlatform = null;
    let dragCounter = 0;

    // Drag and Drop functionality
    const dropZone = document.getElementById('urlDropZone');
    const urlInput = document.getElementById('url');
    const dropOverlay = document.getElementById('dropOverlay');

    // Prevent default drag behaviors
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
            showToast('URL pasted successfully! 🐱', 'success');
        }
    });

    // Auto-detect platform when URL changes
    let detectTimeout;
    $('#url').on('input', function() {
        const url = $(this).val().trim();
        
        // Clear previous timeout
        clearTimeout(detectTimeout);
        
        if (url) {
            // Show download options immediately
            showElement('#downloadOptions');
            
            // Debounce detection
            detectTimeout = setTimeout(() => {
                detectPlatform(url);
            }, 500);
        } else {
            hideVideoInfo();
            hideElement('#downloadOptions');
        }
    });

    // Platform-specific options
    $('#platform').on('change', function() {
        currentPlatform = $(this).val();
        updatePlatformOptions();
    });

    // Audio only checkbox handler
    $('#audioOnly').on('change', function() {
        const isAudioOnly = $(this).is(':checked');
        
        if (isAudioOnly) {
            $('#format').html(`
                <option value="mp3">🎵 MP3 Audio (Best)</option>
                <option value="m4a">🎶 M4A Audio (Apple)</option>
                <option value="wav">🎼 WAV Audio (Lossless)</option>
                <option value="flac">💿 FLAC Audio (Hi-Fi)</option>
            `);
            $('#quality').html(`
                <option value="320kbps">🌟 320kbps (Best)</option>
                <option value="256kbps">📻 256kbps (High)</option>
                <option value="192kbps">🎧 192kbps (Good)</option>
                <option value="128kbps">⚡ 128kbps (Fast)</option>
            `);
            $('label[for="quality"] span').text('Audio Quality');
            $('label[for="quality"] i').removeClass('fa-hd-video').addClass('fa-music');
        } else {
            $('#format').html(`
                <option value="mp4">🎬 MP4 Video (Universal)</option>
                <option value="webm">🌐 WebM Video (Smaller)</option>
                <option value="mp3">🎵 MP3 Audio</option>
                <option value="m4a">🎶 M4A Audio (Better)</option>
                <option value="wav">🎼 WAV Audio (Lossless)</option>
            `);
            $('#quality').html(`
                <option value="best">🌟 Best Quality (Recommended)</option>
                <option value="1080p">📺 1080p Full HD</option>
                <option value="720p">📱 720p HD</option>
                <option value="480p">💻 480p SD</option>
                <option value="360p">📞 360p</option>
                <option value="240p">⚡ 240p (Fastest)</option>
            `);
            $('label[for="quality"] span').text('Video Quality');
            $('label[for="quality"] i').removeClass('fa-music').addClass('fa-hd-video');
        }
    });

    // Form submission
    $('#downloadForm').on('submit', function(e) {
        e.preventDefault();

        const url = $('#url').val().trim();
        if (!url) {
            showToast('Please enter a valid URL', 'error');
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
                    showToast('Download started successfully! 🎉', 'success');
                    // Reset form after successful download
                    setTimeout(() => {
                        resetForm();
                    }, 1000);
                    loadRecentDownloads();
                } else {
                    showToast(response.message || 'Failed to start download', 'error');
                }
            },
            error: function(xhr) {
                console.error('Download error:', xhr);
                let errorMessage = 'An error occurred';

                if (xhr.responseJSON) {
                    errorMessage = xhr.responseJSON.message || errorMessage;
                } else if (xhr.status === 419) {
                    errorMessage = 'Session expired. Please refresh the page.';
                } else if (xhr.status === 422) {
                    errorMessage = 'Invalid URL or settings. Please check and try again.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error. Please try again later.';
                }

                showToast(errorMessage, 'error');
            },
            complete: function() {
                setButtonLoading(submitBtn, false);
            }
        });
    });

    // Reset button
    $('#resetBtn').on('click', function() {
        resetForm();
        showToast('Form reset! 🔄', 'info');
    });

    // Refresh downloads
    $('#refreshDownloads').on('click', function() {
        const btn = $(this);
        btn.find('i').addClass('fa-spin');
        loadRecentDownloads();
        setTimeout(() => {
            btn.find('i').removeClass('fa-spin');
        }, 1000);
    });

    // Load recent downloads on page load
    loadRecentDownloads();

    // Helper Functions
    function detectPlatform(url) {
        showElement('#videoInfo');
        $('#videoThumbnail').html(`
            <div class="flex flex-col items-center gap-2">
                <i class="fas fa-spinner fa-spin text-3xl text-glass-accent"></i>
                <span class="text-sm text-glass-secondary">Detecting...</span>
            </div>
        `);
        $('#videoTitle').text('Analyzing video...');

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
                } else {
                    hideVideoInfo();
                    showToast('Could not detect video info', 'warning');
                }
            },
            error: function(xhr) {
                console.error('Platform detection error:', xhr);
                hideVideoInfo();
                showToast('Failed to detect platform. Please check the URL.', 'error');
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
                console.error('Quality options update error:', xhr);
            }
        });
    }

    function showVideoInfo(info) {
        let thumbnailUrl = info.thumbnail ? '/thumbnail-proxy/' + btoa(info.thumbnail) : '';
        
        if (thumbnailUrl) {
            $('#videoThumbnail').html(`
                <img src="${thumbnailUrl}" 
                     alt="Video thumbnail" 
                     class="w-full h-full object-cover rounded-lg"
                     onerror="this.onerror=null; this.src='/images/placeholder.jpg';">
            `);
        } else {
            $('#videoThumbnail').html(`
                <i class="fas fa-video text-4xl text-glass-secondary"></i>
            `);
        }

        $('#videoTitle').text(info.title || 'Unknown Title');
        $('#videoDuration').find('span').text(info.duration || '--:--');
        $('#videoUploader').find('span').text(info.uploader || 'Unknown');

        if (info.view_count) {
            $('#videoViews').removeClass('hidden').find('span').text(formatNumber(info.view_count));
        } else {
            $('#videoViews').addClass('hidden');
        }

        // Update platform badge
        const platformIcons = {
            'youtube': 'fab fa-youtube',
            'tiktok': 'fab fa-tiktok',
            'instagram': 'fab fa-instagram',
            'facebook': 'fab fa-facebook'
        };
        const icon = platformIcons[currentPlatform] || 'fas fa-video';
        $('#platformBadge').html(`<i class="${icon} text-glass-accent"></i>`);

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
                        
                        const statusColor = statusColors[download.status] || 'bg-gray-100 text-gray-800';
                        const statusIcon = statusIcons[download.status] || 'fa-question-circle';
                        
                        html += `
                            <div class="glassmorphism-card rounded-xl p-4 glass-hover transition-all duration-300 hover:scale-[1.02]">
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <!-- Platform Icon -->
                                    <div class="w-14 h-14 glassmorphism-card rounded-xl flex items-center justify-center flex-shrink-0 bg-gradient-to-br from-white to-gray-50">
                                        <i class="${download.platform_icon || 'fas fa-video'} text-2xl text-glass-accent"></i>
                                    </div>
                                    
                                    <!-- Download Info -->
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-glass-primary mb-1 line-clamp-2">${download.title || 'Unknown Title'}</h4>
                                        <div class="flex flex-wrap items-center gap-2 text-sm text-glass-secondary mb-2">
                                            <span class="flex items-center gap-1">
                                                <i class="fas fa-globe text-xs"></i>
                                                ${download.platform_display_name || download.platform}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <i class="fas fa-calendar text-xs"></i>
                                                ${download.created_at}
                                            </span>
                                            ${download.file_size ? `
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-database text-xs"></i>
                                                    ${download.file_size}
                                                </span>
                                            ` : ''}
                                        </div>
                                        
                                        <!-- Status and Actions -->
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full ${statusColor} flex items-center gap-1">
                                                <i class="fas ${statusIcon}"></i>
                                                ${download.status_badge || download.status}
                                            </span>
                                            ${download.status === 'completed' ? `
                                                <a href="/downloads/${download.id}/download" 
                                                   class="px-4 py-2 glassmorphism-button-accent text-white text-xs font-bold rounded-lg glass-hover flex items-center gap-2 transition-all duration-300">
                                                    <i class="fas fa-download"></i>
                                                    Download
                                                </a>
                                            ` : ''}
                                            ${download.status === 'processing' ? `
                                                <span class="px-3 py-1 text-xs text-glass-secondary flex items-center gap-2">
                                                    <div class="w-full bg-gray-200 rounded-full h-2 max-w-[100px]">
                                                        <div class="bg-gradient-to-r from-orange-400 to-orange-500 h-2 rounded-full animate-pulse" style="width: ${download.progress || 50}%"></div>
                                                    </div>
                                                    ${download.progress || 50}%
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
                        <div class="text-center py-16">
                            <div class="w-24 h-24 mx-auto mb-6 glassmorphism-card rounded-full flex items-center justify-center">
                                <i class="fas fa-download text-5xl text-glass-secondary"></i>
                            </div>
                            <h3 class="text-xl font-bold text-glass-primary mb-2">No downloads yet</h3>
                            <p class="text-glass-secondary max-w-md mx-auto">
                                Your downloaded media will appear here. Start by pasting a URL above! 🐱
                            </p>
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                console.error('Load recent downloads error:', xhr);
                $('#recentDownloads').html(`
                    <div class="text-center py-16">
                        <div class="w-24 h-24 mx-auto mb-6 glassmorphism-card rounded-full flex items-center justify-center bg-red-50">
                            <i class="fas fa-exclamation-triangle text-5xl text-red-500"></i>
                        </div>
                        <h3 class="text-xl font-bold text-glass-primary mb-2">Failed to load downloads</h3>
                        <p class="text-glass-secondary mb-4">Please try refreshing the page</p>
                        <button onclick="location.reload()" class="glassmorphism-button text-white font-bold py-2 px-6 rounded-lg">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Refresh Page
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
        $('#url').val('').focus();
        dragCounter = 0;
    }

    function setButtonLoading(button, isLoading) {
        const textEl = button.find('#downloadBtnText');
        
        if (isLoading) {
            button.prop('disabled', true);
            button.addClass('btn-loading');
            textEl.html('<span class="opacity-0">Processing...</span>');
        } else {
            button.prop('disabled', false);
            button.removeClass('btn-loading');
            textEl.text('Start Download');
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
            return (num / 1000000).toFixed(1) + 'M';
        } else if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
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
            success: 'from-green-400 to-green-500',
            error: 'from-red-400 to-red-500',
            warning: 'from-yellow-400 to-yellow-500',
            info: 'from-blue-400 to-blue-500'
        };

        const toast = $(`
            <div class="toast glassmorphism-card rounded-xl shadow-2xl p-4 max-w-sm border-2 border-white/30">
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

    // Auto-refresh downloads every 30 seconds if there are processing items
    setInterval(function() {
        if ($('#recentDownloads').find('.fa-spinner').length > 0) {
            loadRecentDownloads();
        }
    }, 30000);
});
</script>
@endpush