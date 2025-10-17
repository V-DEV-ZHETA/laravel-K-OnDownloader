@extends('layouts.app')

@section('title', 'Platforms - K-OnDownloader')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-cogs mr-2"></i>
                Platforms
            </h1>
            <a href="{{ route('settings.index') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-sliders-h mr-1"></i>
                Manage Settings
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($platforms as $platform)
                <div class="bg-gray-50 rounded-lg p-6 card-hover">
                    <div class="text-center mb-4">
                        <i class="{{ $platform->icon }} text-4xl mb-2 
                            @if($platform->is_active) text-blue-600 @else text-gray-400 @endif"></i>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $platform->display_name }}</h3>
                        <div class="flex items-center justify-center mt-2">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($platform->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                @if($platform->is_active)
                                    <i class="fas fa-check mr-1"></i> Active
                                @else
                                    <i class="fas fa-times mr-1"></i> Inactive
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <a href="{{ route('platforms.show', $platform) }}" 
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 text-center block">
                            <i class="fas fa-eye mr-1"></i>
                            View Details
                        </a>
                        
                        <button onclick="togglePlatform({{ $platform->id }})" 
                                class="w-full @if($platform->is_active) bg-red-600 hover:bg-red-700 @else bg-green-600 hover:bg-green-700 @endif text-white font-bold py-2 px-4 rounded-lg transition duration-200">
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

