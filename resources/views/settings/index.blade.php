@extends('layouts.app')

@section('title', 'Platform Settings - K-OnDownloader')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="glassmorphism-card rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-glass-primary">
                <i class="fas fa-sliders-h mr-2 text-glass-accent"></i>
                Platform Settings
            </h1>
            <button onclick="resetAllSettings()"
                    class="glassmorphism text-glass-primary font-bold py-2 px-4 rounded-lg glass-hover">
                <i class="fas fa-undo mr-1"></i>
                Reset All
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($platforms as $platform)
                <div class="glassmorphism-card rounded-lg p-6 glass-hover">
                    <div class="text-center mb-4">
                        <i class="{{ $platform->icon }} text-4xl mb-2
                            @if($platform->is_active) text-glass-accent @else text-glass-secondary @endif"></i>
                        <h3 class="text-lg font-semibold text-glass-primary">{{ $platform->display_name }}</h3>
                        <div class="flex items-center justify-center mt-2">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full glassmorphism-card
                                @if($platform->is_active) text-green-600 @else text-red-600 @endif">
                                @if($platform->is_active)
                                    <i class="fas fa-check mr-1"></i> Aktif
                                @else
                                    <i class="fas fa-times mr-1"></i> NonAktif
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <a href="{{ route('settings.show', $platform) }}"
                           class="w-full glassmorphism-button text-white font-bold py-2 px-4 rounded-lg glass-hover text-center block">
                            <i class="fas fa-cog mr-1"></i>
                            Konfigurasi
                        </a>

                        <button onclick="togglePlatform({{ $platform->id }})"
                                class="w-full @if($platform->is_active) bg-red-500 @else bg-green-500 @endif text-white font-bold py-2 px-4 rounded-lg glass-hover">
                            @if($platform->is_active)
                                <i class="fas fa-pause mr-1"></i>
                                Disable
                            @else
                                <i class="fas fa-play mr-1"></i>
                                Enable
                            @endif
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Settings Overview -->
    <div class="mt-8 glassmorphism-card rounded-lg p-6">
        <h2 class="text-2xl font-bold text-glass-primary mb-4">
            <i class="fas fa-info-circle mr-2 text-glass-accent"></i>
            Settings Overview
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($platforms as $platform)
                <div class="glassmorphism-card rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-glass-primary mb-3 flex items-center">
                        <i class="{{ $platform->icon }} mr-2 text-glass-accent"></i>
                        {{ $platform->display_name }}
                    </h3>

                    <div class="space-y-2">
                        @foreach($platform->settings as $setting)
                            <div class="flex justify-between items-center p-3 glassmorphism-card rounded-lg">
                                <span class="text-sm font-medium text-glass-primary">
                                    {{ ucfirst(str_replace('_', ' ', $setting->setting_key)) }}
                                </span>
                                <span class="text-sm text-glass-primary font-semibold">
                                    @if($setting->setting_type === 'boolean')
                                        {{ $setting->typed_value ? 'Yes' : 'No' }}
                                    @else
                                        {{ $setting->setting_value }}
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePlatform(platformId) {
    $.ajax({
        url: `/platforms/${platformId}/toggle`,
        method: 'PATCH',
        success: function(response) {
            if (response.success) {
                showAlert('Platform status updated successfully', 'success');
                location.reload();
            } else {
                showAlert(response.message || 'Failed to update platform status', 'error');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showAlert(response.message || 'An error occurred', 'error');
        }
    });
}

function resetAllSettings() {
    if (confirm('Are you sure you want to reset all settings to default? This action cannot be undone.')) {
        $.ajax({
            url: '{{ route("settings.defaults") }}',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    // Reset each platform
                    const platforms = Object.keys(response.default_settings);
                    let completed = 0;

                    platforms.forEach(function(platformName) {
                        $.ajax({
                            url: `/settings/${platformName}/reset`,
                            method: 'POST',
                            success: function() {
                                completed++;
                                if (completed === platforms.length) {
                                    showAlert('All settings have been reset to default', 'success');
                                    location.reload();
                                }
                            },
                            error: function() {
                                completed++;
                                if (completed === platforms.length) {
                                    showAlert('Some settings may not have been reset properly', 'error');
                                    location.reload();
                                }
                            }
                        });
                    });
                } else {
                    showAlert('Failed to get default settings', 'error');
                }
            },
            error: function() {
                showAlert('An error occurred while resetting settings', 'error');
            }
        });
    }
}
</script>
@endpush
