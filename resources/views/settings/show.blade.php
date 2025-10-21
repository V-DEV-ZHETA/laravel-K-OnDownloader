@extends('layouts.app')

@section('title', $platform->display_name . ' Settings - K-OnDownloader')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="glassmorphism-card rounded-lg p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 sm:mb-6">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('settings.index') }}"
                   class="text-glass-secondary hover:text-glass-primary mr-4 glass-hover p-2 rounded-lg">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-glass-primary flex items-center">
                        <i class="{{ $platform->icon }} mr-3 text-glass-accent"></i>
                        {{ $platform->display_name }}
                    </h1>
                    <p class="text-glass-secondary mt-1">Configure download options for {{ $platform->display_name }}</p>
                </div>
            </div>
            <button onclick="resetSettings()"
                    class="glassmorphism text-glass-primary font-bold py-2 px-4 rounded-lg glass-hover">
                <i class="fas fa-undo mr-1"></i>
                Reset to Default
            </button>
        </div>

        <form id="settingsForm" class="space-y-4 sm:space-y-6">
            <!-- Quality Settings -->
            <div class="glassmorphism-card rounded-lg p-6">
                <h3 class="text-lg font-semibold text-glass-primary mb-4">
                    <i class="fas fa-hd-video mr-2 text-glass-accent"></i>
                    Quality Settings
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label for="quality" class="block text-sm font-medium text-glass-primary mb-2">
                            Default Quality
                        </label>
                        <select id="quality"
                                name="settings[quality]"
                                class="glass-select w-full px-4 py-3 rounded-lg text-glass-primary">
                            <option value="best">Best Quality</option>
                            <option value="720p">720p HD</option>
                            <option value="480p">480p SD</option>
                            <option value="360p">360p</option>
                            <option value="240p">240p</option>
                            <option value="worst">Worst Quality</option>
                        </select>
                    </div>

                    <div>
                        <label for="format" class="block text-sm font-medium text-glass-primary mb-2">
                            Default Format
                        </label>
                        <select id="format"
                                name="settings[format]"
                                class="glass-select w-full px-4 py-3 rounded-lg text-glass-primary">
                            <option value="mp4">MP4 Video</option>
                            <option value="mp3">MP3 Audio</option>
                            <option value="wav">WAV Audio</option>
                            <option value="m4a">M4A Audio</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Platform-specific Settings -->
            <div class="glassmorphism-card rounded-lg p-6">
                <h3 class="text-lg font-semibold text-glass-primary mb-4">
                    <i class="fas fa-cog mr-2 text-glass-accent"></i>
                    Platform-specific Settings
                </h3>

                <div class="space-y-3 sm:space-y-4">
                    @if($platform->name === 'youtube')
                        <div class="flex flex-wrap items-center gap-2">
                            <input type="checkbox"
                                   id="subtitles"
                                   name="settings[subtitles]"
                                   class="h-4 w-4 text-glass-accent focus:ring-glass-accent border-glass-secondary rounded">
                            <label for="subtitles" class="ml-2 block text-sm text-glass-primary">
                                Download Subtitles
                            </label>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <input type="checkbox"
                                   id="embed_metadata"
                                   name="settings[embed_metadata]"
                                   class="h-4 w-4 text-glass-accent focus:ring-glass-accent border-glass-secondary rounded">
                            <label for="embed_metadata" class="ml-2 block text-sm text-glass-primary">
                                Embed Metadata
                            </label>
                        </div>
                    @endif

                    @if($platform->name === 'tiktok')
                        <div class="flex flex-wrap items-center gap-2">
                            <input type="checkbox"
                                   id="watermark"
                                   name="settings[watermark]"
                                   class="h-4 w-4 text-glass-accent focus:ring-glass-accent border-glass-secondary rounded">
                            <label for="watermark" class="ml-2 block text-sm text-glass-primary">
                                Include Watermark
                            </label>
                        </div>
                    @endif

                    @if($platform->name === 'instagram')
                        <div class="flex flex-wrap items-center gap-2">
                            <input type="checkbox"
                                   id="stories"
                                   name="settings[stories]"
                                   class="h-4 w-4 text-glass-accent focus:ring-glass-accent border-glass-secondary rounded">
                            <label for="stories" class="ml-2 block text-sm text-glass-primary">
                                Include Stories
                            </label>
                        </div>

                        <div>
                            <label for="sessionid" class="block text-sm font-medium text-glass-primary mb-2">
                                Instagram Session ID
                            </label>
                            <input type="password"
                                   id="sessionid"
                                   name="settings[sessionid]"
                                   placeholder="Enter your Instagram session ID"
                                   class="glassmorphism-input w-full px-4 py-3 rounded-lg text-glass-primary placeholder-glass-secondary">
                            <p class="text-xs text-glass-secondary mt-1">
                                Required for private posts. Get from browser dev tools after logging in.
                            </p>
                        </div>
                    @endif

                    @if($platform->name === 'facebook')
                        <div class="flex flex-wrap items-center gap-2">
                            <input type="checkbox"
                                   id="subtitles"
                                   name="settings[subtitles]"
                                   class="h-4 w-4 text-glass-accent focus:ring-glass-accent border-glass-secondary rounded">
                            <label for="subtitles" class="ml-2 block text-sm text-glass-primary">
                                Download Subtitles
                            </label>
                        </div>
                    @endif

                    <div class="flex flex-wrap items-center gap-2">
                        <input type="checkbox"
                               id="audio_only"
                               name="settings[audio_only]"
                               class="h-4 w-4 text-glass-accent focus:ring-glass-accent border-glass-secondary rounded">
                        <label for="audio_only" class="ml-2 block text-sm text-glass-primary">
                            Audio Only Mode
                        </label>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="text-center">
                <button type="submit"
                        id="saveBtn"
                        class="glassmorphism-button text-white font-bold py-3 px-8 rounded-lg glass-hover">
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
