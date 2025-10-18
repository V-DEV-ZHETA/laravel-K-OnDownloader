@extends('layouts.app')

@section('title', 'Download Media - K-OnDownloader')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6 text-center">
            {{-- <i class="fas fa-download mr-2"></i> --}}
            {{-- Download Media --}}
        </h1>
        
        <form id="downloadForm" class="space-y-6">
            <!-- URL Input -->
            <div>
                <label for="url" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-link mr-1"></i>
                    Media URL
                </label>
                <input type="url" 
                       id="url" 
                       name="url" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Tempel Url Youtube, Tiktok, Instagram, Dan Facebook Disini..."
                       required>
                <p class="mt-1 text-sm text-gray-500">Supports YouTube, TikTok, Instagram, Dan Facebook</p>
            </div>

            <!-- Platform Selection -->
            <div>
                <label for="platform" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-globe mr-1"></i>
                    Platform (Deteksi Otomatis)
                </label>
                <select id="platform" 
                        name="platform" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Auto-detect platform</option>
                    @foreach($platforms as $platform)
                        <option value="{{ $platform->name }}" data-icon="{{ $platform->icon }}">
                            {{ $platform->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Video Info Display -->
            <div id="videoInfo" class="hidden bg-gray-50 rounded-lg p-4">
                <div class="flex items-start space-x-4">
                    <img id="videoThumbnail" src="" alt="Video thumbnail" class="w-32 h-24 object-cover rounded-lg">
                    <div class="flex-1">
                        <h3 id="videoTitle" class="text-lg font-semibold text-gray-900"></h3>
                        <p id="videoDuration" class="text-sm text-gray-600"></p>
                        <p id="videoUploader" class="text-sm text-gray-500"></p>
                    </div>
                </div>
            </div>

            <!-- Download Options -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Quality Selection -->
                <div>
                    <label for="quality" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-hd-video mr-1"></i>
                        Kualitas Unduhan
                    </label>
                    <select id="quality" 
                            name="quality" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="best">Kualitas Terbaik</option>
                        <option value="720p">720p HD</option>
                        <option value="480p">480p SD</option>
                        <option value="360p">360p</option>
                        <option value="240p">240p</option>
                        <option value="worst">Kualitas Terendah</option>
                    </select>
                </div>

                <!-- Format Selection -->
                <div>
                    <label for="format" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-file mr-1"></i>
                        Format Unduhan
                    </label>
                    <select id="format" 
                            name="format" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="mp4">MP4 Video</option>
                        <option value="webm">WebM Video</option>
                        <option value="mp3">MP3 Audio</option>
                        <option value="wav">WAV Audio</option>
                        <option value="m4a">M4A Audio</option>
                    </select>
                </div>
            </div>

            <!-- Additional Options -->
            <div class="space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="audioOnly" 
                           name="audio_only" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="audioOnly" class="ml-2 block text-sm text-gray-700">
                        <i class="fas fa-music mr-1"></i>
                        Audio Saja 
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" 
                        id="downloadBtn"
                        class="bg-green-400 hover:bg-green-500 text-white font-bold py-3 px-8 rounded-lg transition duration-200 ease-in-out transform hover:scale-105">
                    <i class="fas fa-download mr-2"></i>
                    Mulai Download
                </button>
            </div>
        </form>
    </div>

    <!-- Recent Downloads -->
    <div class="mt-8 bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">
            <i class="fas fa-history mr-2"></i>
            Histori Download
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

    // Auto-detect platform when URL changes
    $('#url').on('input', function() {
        const url = $(this).val();
        if (url) {
            detectPlatform(url);
        } else {
            hideVideoInfo();
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
        } else {
            $('#format').html(`
                <option value="mp4">MP4 Video</option>
                <option value="webm">WebM Video</option>
                <option value="mp3">MP3 Audio</option>
                <option value="wav">WAV Audio</option>
                <option value="m4a">M4A Audio</option>
            `);
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
                    showAlert('Download started successfully!', 'success');
                    $('#downloadForm')[0].reset();
                    hideVideoInfo();
                    loadRecentDownloads();
                } else {
                    showAlert(response.message || 'Failed to start download', 'error');
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

                showAlert(errorMessage, 'error');
            },
            complete: function() {
                hideLoading(submitBtn, originalText);
            }
        });
    });

    // Load recent downloads on page load
    loadRecentDownloads();

    function detectPlatform(url) {
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
                    }
                }
            },
            error: function(xhr) {
                console.error('Platform detection error:', xhr);
                hideVideoInfo();
            }
        });
    }

    function showVideoInfo(info) {
        $('#videoThumbnail').attr('src', info.thumbnail || '');
        $('#videoTitle').text(info.title || 'Unknown Title');
        $('#videoDuration').text(info.duration || 'Unknown Duration');
        $('#videoUploader').text(info.uploader || 'Unknown Uploader');
        $('#videoInfo').removeClass('hidden');
    }

    function hideVideoInfo() {
        $('#videoInfo').addClass('hidden');
    }

    function updatePlatformOptions() {
        // Platform-specific options can be added here
        // For now, we'll keep the same options for all platforms
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
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <i class="${download.platform_icon || 'fas fa-video'} text-lg"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900 truncate">${download.title || 'Unknown Title'}</h4>
                                            <p class="text-sm text-gray-500">${download.platform_display_name || download.platform} • ${download.created_at}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full ${download.status_badge_class || 'bg-gray-100 text-gray-800'}">${download.status_badge || download.status}</span>
                                        ${download.status === 'completed' ? `<a href="/downloads/${download.id}/download" class="text-green-400 hover:text-green-500 text-sm font-medium">Download</a>` : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $('#recentDownloads').html(html);
                } else {
                    $('#recentDownloads').html(`
                        <div class="text-center text-gray-500 py-8">
                            <i class="fas fa-download text-4xl mb-4"></i>
                            <p>No recent downloads yet</p>
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
