@extends('layouts.app')

@section('title', 'Download Details - K-OnDownloader')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center">
                <a href="{{ route('downloads.index') }}" 
                   class="text-gray-600 hover:text-gray-800 mr-4">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-download mr-2"></i>
                        Download Details
                    </h1>
                    <p class="text-gray-600 mt-1">View download information and status</p>
                </div>
            </div>
            <div class="flex space-x-2">
                @if($download->status === 'completed' && $download->file_path)
                    <a href="{{ route('downloads.download', $download) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-download mr-1"></i>
                        Download File
                    </a>
                @endif
                <button onclick="deleteDownload({{ $download->id }})" 
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-trash mr-1"></i>
                    Delete
                </button>
            </div>
        </div>

        <!-- Download Status -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Download Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                        @if($download->status === 'completed') bg-green-100 text-green-800
                        @elseif($download->status === 'downloading') bg-blue-100 text-blue-800
                        @elseif($download->status === 'failed') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        @if($download->status === 'downloading')
                            <i class="fas fa-spinner fa-spin mr-1"></i>
                        @elseif($download->status === 'completed')
                            <i class="fas fa-check mr-1"></i>
                        @elseif($download->status === 'failed')
                            <i class="fas fa-times mr-1"></i>
                        @else
                            <i class="fas fa-clock mr-1"></i>
                        @endif
                        {{ ucfirst($download->status) }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Platform</label>
                    <div class="flex items-center mt-1">
                        <i class="{{ $download->platform->icon ?? 'fas fa-globe' }} mr-2"></i>
                        <span class="text-gray-900">{{ $download->platform->display_name ?? ucfirst($download->platform) }}</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">File Size</label>
                    <p class="text-gray-900">{{ $download->formatted_file_size }}</p>
                </div>
            </div>
        </div>

        <!-- Media Information -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Media Information</h3>
            <div class="flex items-start space-x-6">
                @if($download->thumbnail)
                    <img src="{{ $download->thumbnail }}" 
                         alt="{{ $download->title }}" 
                         class="w-48 h-36 object-cover rounded-lg">
                @else
                    <div class="w-48 h-36 bg-gray-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-video text-gray-400 text-4xl"></i>
                    </div>
                @endif
                <div class="flex-1">
                    <h4 class="text-xl font-semibold text-gray-900 mb-2">{{ $download->title }}</h4>
                    @if($download->duration)
                        <p class="text-gray-600 mb-2">
                            <i class="fas fa-clock mr-1"></i>
                            Duration: {{ $download->duration }}
                        </p>
                    @endif
                    <p class="text-gray-600 mb-2">
                        <i class="fas fa-link mr-1"></i>
                        <a href="{{ $download->url }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                            Original URL
                        </a>
                    </p>
                    <p class="text-gray-600">
                        <i class="fas fa-calendar mr-1"></i>
                        Downloaded: {{ $download->created_at->format('M d, Y H:i') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Download Details -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Download Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Download Started</label>
                    <p class="text-gray-900">{{ $download->created_at->format('M d, Y H:i:s') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                    <p class="text-gray-900">{{ $download->updated_at->format('M d, Y H:i:s') }}</p>
                </div>
                @if($download->file_path)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">File Path</label>
                        <p class="text-gray-900 font-mono text-sm">{{ $download->file_path }}</p>
                    </div>
                @endif
                @if($download->error_message)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Error Message</label>
                        <p class="text-red-600">{{ $download->error_message }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Metadata -->
        @if($download->metadata)
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($download->metadata as $key => $value)
                        @if($value && !in_array($key, ['title', 'thumbnail', 'duration']))
                            <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                                <span class="text-sm font-medium text-gray-700">
                                    {{ ucfirst(str_replace('_', ' ', $key)) }}
                                </span>
                                <span class="text-sm text-gray-900 font-semibold">
                                    {{ is_array($value) ? json_encode($value) : $value }}
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteDownload(downloadId) {
    if (confirm('Are you sure you want to delete this download?')) {
        $.ajax({
            url: `/downloads/${downloadId}`,
            method: 'DELETE',
            success: function(response) {
                if (response.success) {
                    showAlert('Download deleted successfully', 'success');
                    window.location.href = '{{ route("downloads.index") }}';
                } else {
                    showAlert(response.message || 'Failed to delete download', 'error');
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

