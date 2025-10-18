@extends('layouts.app')

@section('title', 'Downloads - K-OnDownloader')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-list mr-2"></i>
                Histori Download
            </h1>
            <a href="{{ route('downloads.create') }}" 
               class="bg-green-400 hover:bg-green-500 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-plus mr-1"></i>
                Unduh Konten Baru
            </a>
        </div>

        @if($downloads->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Media
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Platform
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ukuran
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Download
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($downloads as $download)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($download->thumbnail)
                                            <img class="h-12 w-12 rounded-lg object-cover" 
                                                 src="{{ $download->thumbnail }}" 
                                                 alt="{{ $download->title }}">
                                        @else
                                            <div class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-video text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ Str::limit($download->title, 50) }}
                                            </div>
                                            @if($download->duration)
                                                <div class="text-sm text-gray-500">
                                                    {{ $download->duration }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <i class="{{ $download->platform->icon ?? 'fas fa-globe' }} text-lg mr-2"></i>
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $download->platform->display_name ?? ucfirst($download->platform) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
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
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $download->formatted_file_size }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $download->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @if($download->status === 'completed' && $download->file_path)
                                            <a href="{{ route('downloads.download', $download) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('downloads.show', $download) }}" 
                                           class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button onclick="deleteDownload({{ $download->id }})" 
                                                class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $downloads->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-download text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No downloads yet</h3>
                <p class="text-gray-500 mb-6">Start downloading your favorite videos and audio!</p>
                <a href="{{ route('downloads.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-plus mr-1"></i>
                    Start Download
                </a>
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
                    location.reload();
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

