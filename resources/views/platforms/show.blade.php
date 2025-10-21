@extends('layouts.app')

@section('title', $platform->display_name . ' Details - K-OnDownloader')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="glassmorphism-card rounded-lg p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 sm:mb-6">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('platforms.index') }}"
                   class="text-glass-secondary hover:text-glass-primary mr-4 glass-hover p-2 rounded-lg">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-glass-primary flex items-center">
                        <i class="{{ $platform->icon }} mr-3 text-glass-accent"></i>
                        {{ $platform->display_name }}
                    </h1>
                    <p class="text-glass-secondary mt-1">Platform information and statistics</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('settings.show', $platform) }}"
                   class="glassmorphism-button text-white font-bold py-2 px-4 rounded-lg glass-hover">
                    <i class="fas fa-cog mr-1"></i>
                    Settings
                </a>
                <button onclick="togglePlatform({{ $platform->id }})"
                        class="@if($platform->is_active) glassmorphism-button @else glassmorphism @endif text-white font-bold py-2 px-4 rounded-lg glass-hover">
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

        <!-- Platform Status -->
        <div class="glassmorphism-card rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-glass-primary mb-4">Platform Status</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-sm font-medium text-glass-primary">Status</label>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full glassmorphism-card
                        @if($platform->is_active) text-green-600 @else text-red-600 @endif">
                        @if($platform->is_active)
                            <i class="fas fa-check mr-1"></i> Active
                        @else
                            <i class="fas fa-times mr-1"></i> Inactive
                        @endif
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-glass-primary">Created</label>
                    <p class="text-glass-primary">{{ $platform->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Current Settings -->
        <div class="glassmorphism-card rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-glass-primary mb-4">Current Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

        <!-- Default Settings -->
        @if($platform->default_settings)
            <div class="glassmorphism-card rounded-lg p-6">
                <h3 class="text-lg font-semibold text-glass-primary mb-4">Default Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($platform->default_settings as $key => $value)
                        <div class="flex justify-between items-center p-3 glassmorphism-card rounded-lg">
                            <span class="text-sm font-medium text-glass-primary">
                                {{ ucfirst(str_replace('_', ' ', $key)) }}
                            </span>
                            <span class="text-sm text-glass-primary font-semibold">
                                @if(is_bool($value))
                                    {{ $value ? 'Yes' : 'No' }}
                                @else
                                    {{ $value }}
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
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
</script>
@endpush
