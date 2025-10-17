@extends('layouts.app')

@section('title', $platform->display_name . ' Details - K-OnDownloader')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center">
                <a href="{{ route('platforms.index') }}" 
                   class="text-gray-600 hover:text-gray-800 mr-4">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="{{ $platform->icon }} mr-3"></i>
                        {{ $platform->display_name }} Details
                    </h1>
                    <p class="text-gray-600 mt-1">Platform information and statistics</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('settings.show', $platform) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-cog mr-1"></i>
                    Settings
                </a>
                <button onclick="togglePlatform({{ $platform->id }})" 
                        class="@if($platform->is_active) bg-red-600 hover:bg-red-700 @else bg-green-600 hover:bg-green-700 @endif text-white font-bold py-2 px-4 rounded-lg transition duration-200">
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
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Platform Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                        @if($platform->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                        @if($platform->is_active)
                            <i class="fas fa-check mr-1"></i> Active
                        @else
                            <i class="fas fa-times mr-1"></i> Inactive
                        @endif
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Created</label>
                    <p class="text-gray-900">{{ $platform->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Current Settings -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($platform->settings as $setting)
                    <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                        <span class="text-sm font-medium text-gray-700">
                            {{ ucfirst(str_replace('_', ' ', $setting->setting_key)) }}
                        </span>
                        <span class="text-sm text-gray-900 font-semibold">
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
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Default Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($platform->default_settings as $key => $value)
                        <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                            <span class="text-sm font-medium text-gray-700">
                                {{ ucfirst(str_replace('_', ' ', $key)) }}
                            </span>
                            <span class="text-sm text-gray-900 font-semibold">
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

