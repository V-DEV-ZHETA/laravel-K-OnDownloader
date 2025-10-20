@extends('layouts.app')

@section('title', 'Download Media - K-OnDownloader')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="glassmorphism-card rounded-lg p-6">
        <h1 class="text-3xl font-bold text-glass-primary mb-6 text-center">
            <i class="fas fa-download mr-2 text-glass-accent"></i>
            Download Media
        </h1>

        <form id="downloadForm" class="space-y-6">
            <!-- URL Input with Drag & Drop -->
            <div>
                <label for="url" class="block text-sm font-medium text-glass-primary mb-2">
                    <i class="fas fa-link mr-1 text-glass-accent"></i>
                    Media URL
                </label>
                <div id="urlDropZone" class="relative">
                    <input type="url"
                           id="url"
                           name="url"
                           class="glassmorphism-input w-full px-4 py-3 rounded-lg text-glass-primary placeholder-glass-secondary"
                           placeholder="Paste URL or drag & drop here..."
                           required>
                    <div class="absolute inset-0 rounded-lg border-2 border-dashed border-glass-secondary opacity-0 hover:opacity-50 transition-opacity duration-300 pointer-events-none drag-over:border-glass-accent drag-over:opacity-100">
                        <div class="flex items-center justify-center h-full">
                            <div class="text-center">
                                <i class="fas fa-cloud-upload-alt text-2xl text-glass-accent mb-2"></i>
                                <p class="text-sm text-glass-primary">Drop URL here</p>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="mt-1 text-sm text-glass-secondary">Supports YouTube, TikTok, Instagram, and Facebook</p>
            </div>

            <!-- Platform Selection -->
            <div>
                <label for="platform" class="block text-sm font-medium text-glass-primary mb-2">
                    <i class="fas fa-globe mr-1 text-glass-accent"></i>
                    Platform (Auto-detect)
                </label>
                <select id="platform"
                        name="platform"
                        class="glass-select w-full px-4 py-3 rounded-lg text-glass-primary">
                    <option value="">Auto-detect platform</option>
                    @foreach($platforms as $platform)
                        <option value="{{ $platform->name }}" data-icon="{{ $platform->icon }}">
                            {{ $platform->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Video Info Display -->
            <div id="videoInfo" class="hidden glassmorphism-card rounded-lg p-4 fade-in">
                <div class="flex items-start space-x-4">
                    <div id="videoThumbnail" class="w-32 h-24 glassmorphism-card rounded-lg flex items-center justify-center">
                        <i class="fas fa-spinner fa-spin text-glass-accent"></i>
                    </div>
                    <div class="flex-1">
                        <h3 id="videoTitle" class="text-lg font-semibold text-glass-primary">Loading...</h3>
                        <p id="videoDuration" class="text-sm text-glass-secondary"></p>
                        <p id="videoUploader" class="text-sm text-glass-secondary"></p>
                        <div class="mt-2">
                            <span id="videoViews" class="badge-secondary text-xs"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Download Options -->
            <div id="downloadOptions" class="hidden fade-in">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Quality Selection - Hide for Instagram -->
                    <div id="qualitySection">
                        <label for="quality" class="block text-sm font-medium text-glass-primary mb-2">
                            <i class="fas fa-hd-video mr-1 text-glass-accent"></i>
                            Download Quality
                        </label>
                        <select id="quality"
                                name="quality"
                                class="glass-select w-full px-4 py-3 rounded-lg text-glass-primary">
                            <option value="best">Best Quality</option>
                            <option value="720p">720p HD</option>
                            <option value="480p">480p SD</option>
                            <option value="360p">360p</option>
                            <option value="240p">240p</option>
                            <option value="worst">Lowest Quality</option>
                        </select>
                    </div>

                    <!-- Format Selection -->
                    <div>
                        <label for="format" class="block text-sm font-medium text-glass-primary mb-2">
                            <i class="fas fa-file mr-1 text-glass-accent"></i>
                            Download Format
                        </label>
                        <select id="format"
                                name="format"
                                class="glass-select w-full px-4 py-3 rounded-lg text-glass-primary">
                            <option value="mp4">MP4 Video</option>
                            <option value="webm">WebM Video</option>
                            <option value="mp3">MP3 Audio</option>
                            <option value="wav">WAV Audio</option>
                            <option value="m4a">M4A Audio</option>
                        </select>
                    </div>
                </div>

                <!-- Additional Options -->
                <div class="space-y-4 mt-4">
                    <div class="flex items-center">
                        <input type="checkbox"
                               id="audioOnly"
                               name="audio_only"
                               class="h-4 w-4 text-glass-accent focus:ring-glass-accent border-glass-secondary rounded">
                        <label for="audioOnly" class="ml-2 block text-sm text-glass-primary">
                            <i class="fas fa-music mr-1 text-glass-accent"></i>
                            Audio Only
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit"
                        id="downloadBtn"
                        class="glassmorphism-button text-white font-bold py-3 px-8 rounded-lg glass-hover">
                    <i class="fas fa-download mr-2"></i>
                    Start Download
                </button>
            </div>
        </form>
    </div>

    <!-- Recent Downloads -->
    <div class="mt-8 glassmorphism-card rounded-lg p-6">
        <h2 class="text-2xl font-bold text-glass-primary mb-4">
            <i class="fas fa-history mr-2 text-glass-accent"></i>
            Recent Downloads
        </h2>
        <div id="recentDownloads" class="space-y-4">
            <!-- Recent downloads will be loaded here -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentPlatform = null;
    let dragCounter = 0;

    // Drag and Drop functionality
    const dropZone = document.getElementById('urlDropZone');
    const urlInput = document.getElementById('url');

    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    // Highlight drop zone when dragging over it
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    // Handle drop
    dropZone.addEventListener('drop', handleDrop, false);

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight(e) {
        dropZone.classList.add('drag-over');
    }

    function unhighlight(e) {
        dropZone.classList.remove('drag-over');
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const url = dt.getData('text/plain');

        if (url) {
            urlInput.value = url;
            urlInput.dispatchEvent(new Event('input'));
            showToast('URL pasted from drag & drop!', 'success');
        }
    }

    // Auto-detect platform when URL changes
    $('#url').on('input', function() {
        const url = $(this).val();
        if (url) {
            detectPlatform(url);
            $('#downloadOptions').removeClass('hidden');
        } else {
            hideVideoInfo();
            $('#downloadOptions').addClass('hidden');
        }
    });

    // Platform-specific options
    $('#platform').on('change', function() {
        currentPlatform = $(this).val();
        updatePlatformOptions();
    });

    // Audio only checkbox handler
    $('#audioOnly').on('change', function() {
        if ($(this).is(':checked')) {
            $('#format').html(`
                <option value="mp3">MP3 Audio</option>
                <option value="wav">WAV Audio</option>
                <option value="m4a">M4A Audio</option>
            `);
            $('#quality').html(`
                <option value="320kbps">320kbps</option>
                <option value="256kbps">256kbps</option>
                <option value="192kbps">192kbps</option>
                <option value="128kbps">128kbps</option>
            `);
            $('label[for="quality"]').html('<i class="fas fa-music mr-1 text-glass-accent"></i> Audio Quality');
        } else {
            $('#format').html(`
                <option value="mp4">MP4 Video</option>
                <option value="webm">WebM Video</option>
                <option value="mp3">MP3 Audio</option>
                <option value="wav">WAV Audio</option>
                <option value="m4a">M4A Audio</option>
            `);
            $('#quality').html(`
                <option value="best">Best Quality</option>
                <option value="720p">720p HD</option>
                <option value="480p">480p SD</option>
                <option value="360p">360p</option>
                <option value="240p">240p</option>
                <option value="worst">Lowest Quality</option>
            `);
            $('label[for="quality"]').html('<i class="fas fa-hd-video mr-1 text-glass-accent"></i> Video Quality');
        }
    });

    // Form submission
    $('#downloadForm').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            url: $('#url').val(),
            platform: $('#platform').val(),
            quality: $('#quality').val(),
            format: $('#format').val(),
            audio_only: $('#audioOnly').is(':checked')
        };

        const submitBtn = $('#downloadBtn');
        const originalText = submitBtn.html();

        showLoading(submitBtn);

        $.ajax({
            url: '/api/download',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function(response) {
                if (response.success) {
                    showToast('Download started successfully!', 'success');
                    $('#downloadForm')[0].reset();
                    hideVideoInfo();
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
                    errorMessage = 'CSRF token mismatch. Please refresh the page.';
                } else if (xhr.status === 422) {
                    errorMessage = 'Invalid input data. Please check your URL and settings.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error. Please try again later.';
                }

                showToast(errorMessage, 'error');
            },
            complete: function() {
                hideLoading(submitBtn, originalText);
            }
        });
    });

    // Load recent downloads on page load
    loadRecentDownloads();

    function detectPlatform(url) {
        // Show loading state
        $('#videoInfo').removeClass('hidden');
        $('#videoThumbnail').html('<i class="fas fa-spinner fa-spin text-glass-accent"></i>');
        $('#videoTitle').text('Detecting platform...');

        $.ajax({
            url: '/api/video-info',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ url: url }),
            success: function(response) {
                if (response.success) {
                    showVideoInfo(response.info);
                    // Auto-select platform if detected
                    if (response.platform) {
                        $('#platform').val(response.platform);
                        currentPlatform = response.platform;
                        updatePlatformOptions();
                        // Update quality options based on video formats
                        updateQualityOptions(url, response.platform);
                    }
                } else {
                    hideVideoInfo();
                    showToast('Could not detect platform or video info', 'warning');
                }
            },
            error: function(xhr) {
                console.error('Platform detection error:', xhr);
                hideVideoInfo();
                showToast('Failed to detect platform', 'error');
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
                    for (const [value, label] of Object.entries(response.quality_options)) {
                        qualityHtml += `<option value="${value}">${label}</option>`;
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
        // Use proxy for thumbnail to avoid CORS issues
        let thumbnailUrl = '';
        if (info.thumbnail) {
            thumbnailUrl = '/thumbnail-proxy/' + btoa(info.thumbnail);
        }

        $('#videoThumbnail').html(`<img src="${thumbnailUrl}" alt="Video thumbnail" class="w-full h-full object-cover rounded-lg">`);
        $('#videoTitle').text(info.title || 'Unknown Title');
        $('#videoDuration').text(info.duration || 'Unknown Duration');
        $('#videoUploader').text(info.uploader || 'Unknown Uploader');

        if (info.view_count) {
            $('#videoViews').text(`${info.view_count} views`).show();
        } else {
            $('#videoViews').hide();
        }

        $('#videoInfo').removeClass('hidden');
    }

    function hideVideoInfo() {
        $('#videoInfo').addClass('hidden');
    }

    function updatePlatformOptions() {
        // Remove quality selection for Instagram
        if (currentPlatform === 'instagram') {
            $('#qualitySection').remove();
        } else {
            // Re-add if not present and not Instagram
            if ($('#qualitySection').length === 0) {
                const qualityHtml = `
                    <div id="qualitySection">
                        <label for="quality" class="block text-sm font-medium text-glass-primary mb-2">
                            <i class="fas fa-hd-video mr-1 text-glass-accent"></i>
                            Download Quality
                        </label>
                        <select id="quality"
                                name="quality"
                                class="glass-select w-full px-4 py-3 rounded-lg text-glass-primary">
                            <option value="best">Best Quality</option>
                            <option value="720p">720p HD</option>
                            <option value="480p">480p SD</option>
                            <option value="360p">360p</option>
                            <option value="240p">240p</option>
                            <option value="worst">Lowest Quality</option>
                        </select>
                    </div>
                `;
                $('#downloadOptions .grid').prepend(qualityHtml);
            }
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
                        html += `
                            <div class="glassmorphism-card rounded-lg shadow-sm border border-glass-secondary p-4 glass-hover">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 glassmorphism-card rounded-lg flex items-center justify-center">
                                            <i class="${download.platform_icon || 'fas fa-video'} text-lg text-glass-accent"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-glass-primary truncate">${download.title || 'Unknown Title'}</h4>
                                            <p class="text-sm text-glass-secondary">${download.platform_display_name || download.platform} • ${download.created_at}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full glassmorphism-card ${download.status_badge_class || 'text-glass-secondary'}">${download.status_badge || download.status}</span>
                                        ${download.status === 'completed' ? `<a href="/downloads/${download.id}/download" class="text-glass-accent hover:text-glass-primary text-sm font-medium glass-hover p-2 rounded-lg tooltip" title="Download file"><i class="fas fa-download mr-1"></i>Download</a>` : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $('#recentDownloads').html(html);
                } else {
                    $('#recentDownloads').html(`
                        <div class="text-center text-glass-secondary py-8">
                            <i class="fas fa-download text-4xl mb-4"></i>
                            <p>No recent downloads yet</p>
                            <p class="text-sm mt-2">Your downloaded media will appear here</p>
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                console.error('Load recent downloads error:', xhr);
                $('#recentDownloads').html(`
                    <div class="text-center text-red-500 py-8">
                        <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                        <p>Failed to load recent downloads</p>
                    </div>
                `);
            }
        });
    }
});
</script>
@endpush
