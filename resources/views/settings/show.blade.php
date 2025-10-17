@extends('layouts.app')

@section('title', $platform->display_name . ' Settings - K-OnDownloader')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center">
                <a href="{{ route('settings.index') }}" 
                   class="text-gray-600 hover:text-gray-800 mr-4">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="{{ $platform->icon }} mr-3"></i>
                        {{ $platform->display_name }} Settings
                    </h1>
                    <p class="text-gray-600 mt-1">Configure download options for {{ $platform->display_name }}</p>
                </div>
            </div>
            <button onclick="resetSettings()" 
                    class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-undo mr-1"></i>
                Reset to Default
            </button>
        </div>

        <form id="settingsForm" class="space-y-6">
            <!-- Quality Settings -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-hd-video mr-2"></i>
                    Quality Settings
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="quality" class="block text-sm font-medium text-gray-700 mb-2">
                            Default Quality
                        </label>
                        <select id="quality" 
                                name="settings[quality]" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="best">Best Quality</option>
                            <option value="720p">720p HD</option>
                            <option value="480p">480p SD</option>
                            <option value="360p">360p</option>
                            <option value="240p">240p</option>
                            <option value="worst">Worst Quality</option>
                        </select>
                    </div>

                    <div>
                        <label for="format" class="block text-sm font-medium text-gray-700 mb-2">
                            Default Format
                        </label>
                        <select id="format" 
                                name="settings[format]" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="mp4">MP4 Video</option>
                            <option value="webm">WebM Video</option>
                            <option value="mp3">MP3 Audio</option>
                            <option value="wav">WAV Audio</option>
                            <option value="m4a">M4A Audio</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Platform-specific Settings -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-cog mr-2"></i>
                    Platform-specific Settings
                </h3>
                
                <div class="space-y-4">
                    @if($platform->name === 'youtube')
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="subtitles" 
                                   name="settings[subtitles]" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="subtitles" class="ml-2 block text-sm text-gray-700">
                                Download Subtitles
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="embed_metadata" 
                                   name="settings[embed_metadata]" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="embed_metadata" class="ml-2 block text-sm text-gray-700">
                                Embed Metadata
                            </label>
                        </div>
                    @endif

                    @if($platform->name === 'tiktok')
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="watermark" 
                                   name="settings[watermark]" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="watermark" class="ml-2 block text-sm text-gray-700">
                                Include Watermark
                            </label>
                        </div>
                    @endif

                    @if($platform->name === 'instagram')
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="stories" 
                                   name="settings[stories]" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="stories" class="ml-2 block text-sm text-gray-700">
                                Include Stories
                            </label>
                        </div>
                    @endif

                    @if($platform->name === 'facebook')
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="subtitles" 
                                   name="settings[subtitles]" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="subtitles" class="ml-2 block text-sm text-gray-700">
                                Download Subtitles
                            </label>
                        </div>
                    @endif

                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="audio_only" 
                               name="settings[audio_only]" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="audio_only" class="ml-2 block text-sm text-gray-700">
                            Audio Only Mode
                        </label>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="text-center">
                <button type="submit" 
                        id="saveBtn"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition duration-200 ease-in-out transform hover:scale-105">
                    <i class="fas fa-save mr-2"></i>
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load current settings
    loadCurrentSettings();

    // Form submission
    $('#settingsForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const saveBtn = $('#saveBtn');
        const originalText = saveBtn.html();
        
        showLoading(saveBtn);
        
        $.ajax({
            url: '{{ route("settings.update", $platform) }}',
            method: 'PUT',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showAlert('Settings saved successfully!', 'success');
                } else {
                    showAlert(response.message || 'Failed to save settings', 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showAlert(response.message || 'An error occurred', 'error');
            },
            complete: function() {
                hideLoading(saveBtn, originalText);
            }
        });
    });
});

function loadCurrentSettings() {
    $.ajax({
        url: '{{ route("platforms.settings", $platform) }}',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const settings = response.settings;
                
                // Set form values
                Object.keys(settings).forEach(function(key) {
                    const element = $(`[name="settings[${key}]"]`);
                    if (element.length) {
                        if (element.is(':checkbox')) {
                            element.prop('checked', settings[key].value);
                        } else {
                            element.val(settings[key].value);
                        }
                    }
                });
            }
        },
        error: function() {
            showAlert('Failed to load current settings', 'error');
        }
    });
}

function resetSettings() {
    if (confirm('Are you sure you want to reset settings to default?')) {
        $.ajax({
            url: '{{ route("settings.reset", $platform) }}',
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    showAlert('Settings reset to default successfully!', 'success');
                    loadCurrentSettings();
                } else {
                    showAlert(response.message || 'Failed to reset settings', 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showAlert(response.message || 'An error occurred', 'error');
            }
        });
    }
}
</script>
@endpush

