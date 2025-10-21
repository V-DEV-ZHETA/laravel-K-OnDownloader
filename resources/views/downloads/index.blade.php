@extends('layouts.app')

@section('title', 'Riwayat Download - NekoDrop')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="glassmorphism-card rounded-2xl p-6 md:p-8 mb-8 shadow-xl">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-history text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-glass-primary mb-1">
                        Riwayat Download
                    </h1>
                    <p class="text-glass-secondary">
                        Kelola semua download Anda di sini 🐱
                    </p>
                </div>
            </div>
            <a href="{{ route('downloads.create') }}"
               class="glassmorphism-button text-white font-bold py-3 px-6 rounded-xl glass-hover flex items-center gap-2 shadow-lg">
                <i class="fas fa-plus"></i>
                <span>Unduh Baru</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="glassmorphism-card rounded-xl p-5 glass-hover">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-glass-primary">{{ $downloads->where('status', 'completed')->count() }}</div>
                    <div class="text-sm text-glass-secondary">Selesai</div>
                </div>
            </div>
        </div>
        <div class="glassmorphism-card rounded-xl p-5 glass-hover">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-spinner fa-pulse text-white text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-glass-primary">{{ $downloads->where('status', 'downloading')->count() }}</div>
                    <div class="text-sm text-glass-secondary">Proses</div>
                </div>
            </div>
        </div>
        <div class="glassmorphism-card rounded-xl p-5 glass-hover">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-glass-primary">{{ $downloads->where('status', 'pending')->count() }}</div>
                    <div class="text-sm text-glass-secondary">Menunggu</div>
                </div>
            </div>
        </div>
        <div class="glassmorphism-card rounded-xl p-5 glass-hover">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-list text-white text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-glass-primary">{{ $downloads->total() }}</div>
                    <div class="text-sm text-glass-secondary">Total</div>
                </div>
            </div>
        </div>
    </div>

    @if($downloads->count() > 0)
        <!-- Filter & Search Bar -->
        <div class="glassmorphism-card rounded-xl p-4 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                               id="searchInput"
                               placeholder="Cari berdasarkan judul..."
                               class="glassmorphism-input w-full pl-11 pr-4 py-3 rounded-lg text-glass-primary">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-glass-secondary"></i>
                    </div>
                </div>
                <select id="filterStatus" class="glass-select px-4 py-3 rounded-lg text-glass-primary">
                    <option value="">Semua Status</option>
                    <option value="completed">Selesai</option>
                    <option value="downloading">Sedang Diproses</option>
                    <option value="pending">Menunggu</option>
                    <option value="failed">Gagal</option>
                </select>
                <select id="filterPlatform" class="glass-select px-4 py-3 rounded-lg text-glass-primary">
                    <option value="">Semua Platform</option>
                    <option value="youtube">YouTube</option>
                    <option value="tiktok">TikTok</option>
                    <option value="instagram">Instagram</option>
                    <option value="facebook">Facebook</option>
                </select>
            </div>
        </div>

        <!-- Downloads Grid (Desktop) / List (Mobile) -->
        <div class="space-y-4" id="downloadsContainer">
            @foreach($downloads as $download)
                <div class="glassmorphism-card rounded-xl p-5 glass-hover transition-all duration-300 download-item" 
                     data-status="{{ $download->status }}" 
                     data-platform="{{ $download->platform->name ?? $download->platform }}"
                     data-title="{{ strtolower($download->title) }}">
                    <div class="flex flex-col lg:flex-row gap-5">
                        <!-- Thumbnail & Platform Badge -->
                        <div class="relative flex-shrink-0">
                            @if($download->thumbnail)
                                <div class="w-full lg:w-48 h-32 rounded-xl overflow-hidden glassmorphism-card">
                                    <img class="w-full h-full object-cover"
                                         src="{{ strpos($download->thumbnail, 'http') === 0 ? $download->thumbnail : asset('storage/' . $download->thumbnail) }}"
                                         alt="{{ $download->title }}"
                                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gray-100\'><i class=\'fas fa-video text-4xl text-gray-400\'></i></div>';">
                                </div>
                            @else
                                <div class="w-full lg:w-48 h-32 glassmorphism-card rounded-xl flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                    <i class="fas fa-video text-5xl text-glass-secondary"></i>
                                </div>
                            @endif
                            
                            <!-- Platform Badge -->
                            <div class="absolute top-3 left-3 glassmorphism-card rounded-lg px-3 py-1.5 flex items-center gap-2">
                                <i class="{{ $download->platform->icon ?? 'fas fa-globe' }} text-glass-accent"></i>
                                <span class="text-xs font-semibold text-glass-primary">
                                    {{ $download->platform->display_name ?? ucfirst($download->platform) }}
                                </span>
                            </div>
                        </div>

                        <!-- Content Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col h-full">
                                <!-- Title & Status -->
                                <div class="flex-1">
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <h3 class="text-lg font-bold text-glass-primary line-clamp-2 flex-1">
                                            {{ $download->title }}
                                        </h3>
                                        
                                        <!-- Status Badge -->
                                        @if($download->status === 'completed')
                                            <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-green-100 text-green-700 flex items-center gap-1.5 whitespace-nowrap">
                                                <i class="fas fa-check-circle"></i>
                                                <span>Selesai</span>
                                            </span>
                                        @elseif($download->status === 'downloading')
                                            <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-blue-100 text-blue-700 flex items-center gap-1.5 whitespace-nowrap">
                                                <i class="fas fa-spinner fa-spin"></i>
                                                <span>Proses</span>
                                            </span>
                                        @elseif($download->status === 'failed')
                                            <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-red-100 text-red-700 flex items-center gap-1.5 whitespace-nowrap">
                                                <i class="fas fa-times-circle"></i>
                                                <span>Gagal</span>
                                            </span>
                                        @else
                                            <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-yellow-100 text-yellow-700 flex items-center gap-1.5 whitespace-nowrap">
                                                <i class="fas fa-clock"></i>
                                                <span>Menunggu</span>
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Meta Info -->
                                    <div class="flex flex-wrap items-center gap-4 text-sm text-glass-secondary mb-3">
                                        @if($download->duration)
                                            <span class="flex items-center gap-1.5">
                                                <i class="fas fa-clock text-xs"></i>
                                                {{ $download->duration }}
                                            </span>
                                        @endif
                                        <span class="flex items-center gap-1.5">
                                            <i class="fas fa-database text-xs"></i>
                                            {{ $download->formatted_file_size ?? 'N/A' }}
                                        </span>
                                        <span class="flex items-center gap-1.5">
                                            <i class="fas fa-calendar text-xs"></i>
                                            {{ $download->created_at->diffForHumans() }}
                                        </span>
                                    </div>

                                    <!-- Progress Bar (if downloading) -->
                                    @if($download->status === 'downloading' && isset($download->progress))
                                        <div class="mb-3">
                                            <div class="flex items-center justify-between text-xs text-glass-secondary mb-1">
                                                <span>Mengunduh...</span>
                                                <span class="font-semibold">{{ $download->progress }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                                <div class="bg-gradient-to-r from-blue-400 to-blue-500 h-2 rounded-full transition-all duration-300 animate-pulse" 
                                                     style="width: {{ $download->progress }}%"></div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Error Message (if failed) -->
                                    @if($download->status === 'failed' && $download->error_message)
                                        <div class="glassmorphism-alert-error rounded-lg p-3 mb-3">
                                            <p class="text-sm text-red-700 flex items-start gap-2">
                                                <i class="fas fa-exclamation-triangle mt-0.5"></i>
                                                <span>{{ $download->error_message }}</span>
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-white/30">
                                    @if($download->status === 'completed' && $download->file_path)
                                        <a href="{{ route('downloads.download', $download) }}"
                                           class="glassmorphism-button-accent text-white font-bold py-2 px-5 rounded-lg glass-hover flex items-center gap-2 text-sm">
                                            <i class="fas fa-download"></i>
                                            <span>Unduh File</span>
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('downloads.show', $download) }}"
                                       class="glassmorphism-card px-4 py-2 rounded-lg font-semibold text-glass-primary hover:bg-white/50 transition-all duration-300 glass-hover flex items-center gap-2 text-sm">
                                        <i class="fas fa-eye"></i>
                                        <span>Detail</span>
                                    </a>
                                    
                                    @if($download->status === 'failed')
                                        <button onclick="retryDownload({{ $download->id }})"
                                                class="glassmorphism-button-secondary text-glass-dark font-bold py-2 px-4 rounded-lg glass-hover flex items-center gap-2 text-sm">
                                            <i class="fas fa-redo"></i>
                                            <span>Coba Lagi</span>
                                        </button>
                                    @endif
                                    
                                    <button onclick="deleteDownload({{ $download->id }})"
                                            class="glassmorphism-card px-4 py-2 rounded-lg font-semibold text-red-600 hover:bg-red-50 transition-all duration-300 glass-hover flex items-center gap-2 text-sm ml-auto">
                                        <i class="fas fa-trash"></i>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $downloads->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="glassmorphism-card rounded-2xl p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="w-32 h-32 mx-auto mb-6 glassmorphism-card rounded-full flex items-center justify-center bg-gradient-to-br from-orange-50 to-yellow-50">
                    <i class="fas fa-download text-7xl text-glass-secondary"></i>
                </div>
                <h3 class="text-2xl font-bold text-glass-primary mb-3">Belum Ada Riwayat Download</h3>
                <p class="text-glass-secondary mb-8 text-lg">
                    Mulai unduh video dan audio favorit Anda sekarang! 🐱✨
                </p>
                <a href="{{ route('downloads.create') }}"
                   class="inline-flex items-center gap-3 glassmorphism-button text-white font-bold py-4 px-8 rounded-xl glass-hover shadow-lg">
                    <i class="fas fa-plus"></i>
                    <span>Mulai Download</span>
                </a>
            </div>
        </div>
    @endif

    <!-- Bulk Actions (Hidden, show when items selected) -->
    <div id="bulkActions" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-50">
        <div class="glassmorphism-card rounded-2xl p-4 shadow-2xl flex items-center gap-4">
            <span class="text-sm font-semibold text-glass-primary">
                <span id="selectedCount">0</span> item dipilih
            </span>
            <button class="glassmorphism-button text-white font-bold py-2 px-4 rounded-lg glass-hover text-sm">
                <i class="fas fa-download mr-2"></i>
                Unduh Semua
            </button>
            <button class="glassmorphism-card px-4 py-2 rounded-lg font-semibold text-red-600 hover:bg-red-50 transition-all duration-300 text-sm">
                <i class="fas fa-trash mr-2"></i>
                Hapus Semua
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>
        
        <div class="glassmorphism-card rounded-2xl p-8 max-w-md w-full relative z-10 shadow-2xl">
            <div class="text-center mb-6">
                <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-red-400 to-red-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-glass-primary mb-2">Hapus Download?</h3>
                <p class="text-glass-secondary">
                    Apakah Anda yakin ingin menghapus download ini? File yang sudah diunduh juga akan dihapus.
                </p>
            </div>
            
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()"
                        class="flex-1 glassmorphism-card px-6 py-3 rounded-xl font-bold text-glass-primary hover:bg-white/50 transition-all duration-300">
                    Batal
                </button>
                <button onclick="confirmDelete()"
                        class="flex-1 bg-gradient-to-r from-red-500 to-red-600 text-white font-bold py-3 px-6 rounded-xl glass-hover">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Smooth transitions */
.download-item {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.download-item.hiding {
    opacity: 0;
    transform: scale(0.95);
    pointer-events: none;
}

/* Modal animations */
#deleteModal.show {
    display: flex !important;
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>
@endpush

@push('scripts')
<script>
let deleteDownloadId = null;

$(document).ready(function() {
    // Search functionality
    $('#searchInput').on('input', function() {
        filterDownloads();
    });

    // Filter by status
    $('#filterStatus').on('change', function() {
        filterDownloads();
    });

    // Filter by platform
    $('#filterPlatform').on('change', function() {
        filterDownloads();
    });

    // Auto-refresh for downloading items
    setInterval(function() {
        if ($('.fa-spinner.fa-spin').length > 0) {
            refreshDownloadStatus();
        }
    }, 10000); // Check every 10 seconds
});

function filterDownloads() {
    const searchTerm = $('#searchInput').val().toLowerCase();
    const statusFilter = $('#filterStatus').val();
    const platformFilter = $('#filterPlatform').val();

    $('.download-item').each(function() {
        const $item = $(this);
        const title = $item.data('title');
        const status = $item.data('status');
        const platform = $item.data('platform');

        const matchesSearch = !searchTerm || title.includes(searchTerm);
        const matchesStatus = !statusFilter || status === statusFilter;
        const matchesPlatform = !platformFilter || platform === platformFilter;

        if (matchesSearch && matchesStatus && matchesPlatform) {
            $item.removeClass('hidden').addClass('fade-in');
        } else {
            $item.addClass('hidden').removeClass('fade-in');
        }
    });

    // Show empty state if no results
    updateEmptyState();
}

function updateEmptyState() {
    const visibleItems = $('.download-item:not(.hidden)').length;
    const $container = $('#downloadsContainer');
    
    if (visibleItems === 0) {
        if ($('#noResults').length === 0) {
            $container.append(`
                <div id="noResults" class="glassmorphism-card rounded-xl p-12 text-center fade-in">
                    <i class="fas fa-search text-6xl text-glass-secondary mb-4"></i>
                    <h3 class="text-xl font-bold text-glass-primary mb-2">Tidak Ada Hasil</h3>
                    <p class="text-glass-secondary">Coba ubah filter atau kata kunci pencarian Anda</p>
                </div>
            `);
        }
    } else {
        $('#noResults').remove();
    }
}

function deleteDownload(downloadId) {
    deleteDownloadId = downloadId;
    openDeleteModal();
}

function openDeleteModal() {
    $('#deleteModal').removeClass('hidden').addClass('show');
    $('body').addClass('overflow-hidden');
}

function closeDeleteModal() {
    $('#deleteModal').removeClass('show').addClass('hidden');
    $('body').removeClass('overflow-hidden');
    deleteDownloadId = null;
}

function confirmDelete() {
    if (!deleteDownloadId) return;

    $.ajax({
        url: `/downloads/${deleteDownloadId}`,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showToast('Download berhasil dihapus! 🗑️', 'success');
                
                // Animate removal
                const $item = $(`.download-item[data-download-id="${deleteDownloadId}"]`);
                $item.addClass('hiding');
                
                setTimeout(() => {
                    $item.remove();
                    updateEmptyState();
                    
                    // Reload if page is empty
                    if ($('.download-item').length === 0) {
                        setTimeout(() => location.reload(), 1000);
                    }
                }, 300);
            } else {
                showToast(response.message || 'Gagal menghapus download', 'error');
            }
        },
        error: function(xhr) {
            console.error('Delete error:', xhr);
            const response = xhr.responseJSON;
            showToast(response?.message || 'Terjadi kesalahan saat menghapus', 'error');
        },
        complete: function() {
            closeDeleteModal();
        }
    });
}

function retryDownload(downloadId) {
    showToast('Memulai ulang download... ⚡', 'info');
    
    $.ajax({
        url: `/downloads/${downloadId}/retry`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showToast('Download dimulai ulang! 🔄', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(response.message || 'Gagal memulai ulang download', 'error');
            }
        },
        error: function(xhr) {
            console.error('Retry error:', xhr);
            showToast('Gagal memulai ulang download', 'error');
        }
    });
}

function refreshDownloadStatus() {
    const downloadIds = [];
    $('.download-item[data-status="downloading"]').each(function() {
        const id = $(this).find('[onclick*="deleteDownload"]').attr('onclick').match(/\d+/)[0];
        downloadIds.push(id);
    });

    if (downloadIds.length === 0) return;

    $.ajax({
        url: '/downloads/status',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: JSON.stringify({ ids: downloadIds }),
        contentType: 'application/json',
        success: function(response) {
            if (response.success && response.downloads) {
                let hasChanges = false;
                
                response.downloads.forEach(download => {
                    const $item = $(`.download-item[data-status="downloading"]`).filter(function() {
                        return $(this).find('[onclick*="deleteDownload"]').attr('onclick').includes(download.id);
                    });
                    
                    if (download.status === 'completed' || download.status === 'failed') {
                        hasChanges = true;
                    }
                });
                
                if (hasChanges) {
                    showToast('Status download diperbarui! 🔄', 'info');
                    setTimeout(() => location.reload(), 2000);
                }
            }
        },
        error: function(xhr) {
            console.error('Status refresh error:', xhr);
        }
    });
}

function showToast(message, type = 'info') {
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    const colors = {
        success: 'from-green-400 to-green-500',
        error: 'from-red-400 to-red-500',
        warning: 'from-yellow-400 to-yellow-500',
        info: 'from-blue-400 to-blue-500'
    };

    const toast = $(`
        <div class="toast glassmorphism-card rounded-xl shadow-2xl p-4 max-w-sm border-2 border-white/30 mb-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br ${colors[type]} rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas ${icons[type]} text-white"></i>
                </div>
                <p class="flex-1 text-glass-primary font-medium">${message}</p>
                <button class="text-glass-secondary hover:text-glass-primary transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `);

    toast.find('button').on('click', function() {
        removeToast(toast);
    });

    // Create container if doesn't exist
    if ($('#toastContainer').length === 0) {
        $('body').append('<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-3 pointer-events-none"></div>');
    }

    $('#toastContainer').append(toast);
    setTimeout(() => toast.addClass('pointer-events-auto'), 100);

    setTimeout(() => {
        removeToast(toast);
    }, 5000);
}

function removeToast(toast) {
    toast.css({
        opacity: '0',
        transform: 'translateX(100%)',
        transition: 'all 0.3s ease-out'
    });
    
    setTimeout(() => {
        toast.remove();
    }, 300);
}

// Close modal on Escape key
$(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>
@endpush