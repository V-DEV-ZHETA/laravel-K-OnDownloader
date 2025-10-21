@extends('layouts.app')

@section('title', 'Detail Download - NekoDrop')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Button & Header -->
    <div class="mb-6">
        <a href="{{ route('downloads.index') }}"
           class="inline-flex items-center gap-2 text-glass-primary hover:text-glass-accent transition-colors duration-300 group mb-4">
            <div class="w-10 h-10 glassmorphism-card rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-arrow-left"></i>
            </div>
            <span class="font-semibold">Kembali ke Riwayat</span>
        </a>
    </div>

    <!-- Status Banner -->
    <div class="glassmorphism-card rounded-2xl p-6 mb-8 shadow-xl">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center
                    @if($download->status === 'completed') bg-gradient-to-br from-green-400 to-green-500
                    @elseif($download->status === 'downloading') bg-gradient-to-br from-blue-400 to-blue-500
                    @elseif($download->status === 'failed') bg-gradient-to-br from-red-400 to-red-500
                    @else bg-gradient-to-br from-yellow-400 to-yellow-500 @endif">
                    @if($download->status === 'downloading')
                        <i class="fas fa-spinner fa-spin text-white text-2xl"></i>
                    @elseif($download->status === 'completed')
                        <i class="fas fa-check-circle text-white text-2xl"></i>
                    @elseif($download->status === 'failed')
                        <i class="fas fa-times-circle text-white text-2xl"></i>
                    @else
                        <i class="fas fa-clock text-white text-2xl"></i>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-glass-primary mb-1">
                        @if($download->status === 'completed')
                            Download Selesai! 🎉
                        @elseif($download->status === 'downloading')
                            Sedang Mengunduh...
                        @elseif($download->status === 'failed')
                            Download Gagal
                        @else
                            Menunggu Diproses
                        @endif
                    </h1>
                    <p class="text-glass-secondary">
                        {{ $download->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3">
                @if($download->status === 'completed' && $download->file_path)
                    <a href="{{ route('downloads.download', $download) }}"
                       class="glassmorphism-button-accent text-white font-bold py-3 px-6 rounded-xl glass-hover flex items-center gap-2 shadow-lg">
                        <i class="fas fa-download"></i>
                        <span>Unduh File</span>
                    </a>
                @endif
                
                @if($download->status === 'failed')
                    <button onclick="retryDownload({{ $download->id }})"
                            class="glassmorphism-button-secondary text-glass-dark font-bold py-3 px-6 rounded-xl glass-hover flex items-center gap-2">
                        <i class="fas fa-redo"></i>
                        <span>Coba Lagi</span>
                    </button>
                @endif
                
                <button onclick="shareDownload()"
                        class="glassmorphism-card px-5 py-3 rounded-xl font-semibold text-glass-primary hover:bg-white/50 transition-all duration-300 glass-hover flex items-center gap-2">
                    <i class="fas fa-share-alt"></i>
                    <span class="hidden sm:inline">Bagikan</span>
                </button>
                
                <button onclick="deleteDownload({{ $download->id }})"
                        class="glassmorphism-card px-5 py-3 rounded-xl font-semibold text-red-600 hover:bg-red-50 transition-all duration-300 glass-hover flex items-center gap-2">
                    <i class="fas fa-trash"></i>
                    <span class="hidden sm:inline">Hapus</span>
                </button>
            </div>
        </div>

        <!-- Progress Bar (if downloading) -->
        @if($download->status === 'downloading' && isset($download->progress))
            <div class="mt-6">
                <div class="flex items-center justify-between text-sm text-glass-secondary mb-2">
                    <span class="font-semibold">Progress Download</span>
                    <span class="font-bold">{{ $download->progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-400 to-blue-500 h-3 rounded-full transition-all duration-300 animate-pulse" 
                         style="width: {{ $download->progress }}%"></div>
                </div>
            </div>
        @endif

        <!-- Error Message (if failed) -->
        @if($download->status === 'failed' && $download->error_message)
            <div class="mt-6 glassmorphism-alert-error rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl mt-1"></i>
                    <div class="flex-1">
                        <h4 class="font-bold text-red-700 mb-1">Pesan Error:</h4>
                        <p class="text-red-600">{{ $download->error_message }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Media Preview -->
            <div class="glassmorphism-card rounded-2xl p-6 shadow-xl">
                <h2 class="text-xl font-bold text-glass-primary mb-4 flex items-center gap-2">
                    <i class="fas fa-play-circle text-glass-accent"></i>
                    <span>Pratinjau Media</span>
                </h2>
                
                <div class="space-y-4">
                    <!-- Thumbnail -->
                    @if($download->thumbnail)
                        <div class="relative rounded-xl overflow-hidden glassmorphism-card group">
                            <img src="{{ strpos($download->thumbnail, 'http') === 0 ? $download->thumbnail : asset('storage/' . $download->thumbnail) }}"
                                 alt="{{ $download->title }}"
                                 class="w-full h-64 object-cover"
                                 onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-64 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200\'><i class=\'fas fa-video text-6xl text-gray-400\'></i></div>';">

                            <!-- Play Overlay -->
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                <div class="w-20 h-20 bg-white/90 rounded-full flex items-center justify-center">
                                    <i class="fas fa-play text-3xl text-glass-accent ml-1"></i>
                                </div>
                            </div>

                            <!-- Platform Badge -->
                            <div class="absolute top-4 left-4 glassmorphism-card rounded-lg px-3 py-2 flex items-center gap-2">
                                <i class="{{ $download->platform->icon ?? 'fas fa-globe' }} text-glass-accent"></i>
                                <span class="text-sm font-bold text-glass-primary">
                                    {{ $download->platform->display_name ?? ucfirst($download->platform) }}
                                </span>
                            </div>

                            <!-- Duration Badge -->
                            @if($download->duration)
                                <div class="absolute bottom-4 right-4 bg-black/80 rounded-lg px-3 py-1.5">
                                    <span class="text-white text-sm font-bold">{{ $download->duration }}</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="w-full h-64 glassmorphism-card rounded-xl flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                            <div class="text-center">
                                <i class="fas fa-video text-6xl text-glass-secondary mb-3"></i>
                                <p class="text-glass-secondary font-semibold">Tidak ada pratinjau</p>
                            </div>
                        </div>
                    @endif

                    <!-- Play Video Button -->
                    @if($download->status === 'completed' && $download->file_path && in_array(strtolower(pathinfo($download->file_path, PATHINFO_EXTENSION)), ['mp4', 'webm', 'ogg', 'avi', 'mov', 'wmv', 'flv', 'mkv']))
                        <button onclick="playVideo('{{ asset('storage/' . $download->file_path) }}', '{{ $download->title }}')"
                                class="glassmorphism-button-accent text-white font-bold py-3 px-6 rounded-xl glass-hover flex items-center justify-center gap-2 w-full sm:w-auto">
                            <i class="fas fa-play"></i>
                            <span>Putar Video</span>
                        </button>
                    @endif

                    <!-- Title & Description -->
                    <div>
                        <h3 class="text-2xl font-bold text-glass-primary mb-3 leading-tight">
                            {{ $download->title }}
                        </h3>
                        
                        <!-- Meta Info -->
                        <div class="flex flex-wrap items-center gap-4 text-sm text-glass-secondary">
                            @if($download->uploader)
                                <span class="flex items-center gap-1.5">
                                    <i class="fas fa-user"></i>
                                    {{ $download->uploader }}
                                </span>
                            @endif
                            @if($download->view_count)
                                <span class="flex items-center gap-1.5">
                                    <i class="fas fa-eye"></i>
                                    {{ number_format($download->view_count) }} views
                                </span>
                            @endif
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-calendar"></i>
                                {{ $download->created_at->format('d M Y') }}
                            </span>
                        </div>
                    </div>

                    <!-- URL -->
                    <div class="glassmorphism-card rounded-xl p-4">
                        <label class="block text-sm font-bold text-glass-primary mb-2">URL Sumber:</label>
                        <a href="{{ $download->url }}" 
                           target="_blank" 
                           class="text-glass-accent hover:text-glass-primary break-all text-sm flex items-start gap-2 group">
                            <i class="fas fa-external-link-alt mt-1 group-hover:scale-110 transition-transform"></i>
                            <span class="flex-1">{{ $download->url }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Technical Details -->
            <div class="glassmorphism-card rounded-2xl p-6 shadow-xl">
                <h2 class="text-xl font-bold text-glass-primary mb-4 flex items-center gap-2">
                    <i class="fas fa-cog text-glass-accent"></i>
                    <span>Detail Teknis</span>
                </h2>

                <div class="grid grid-cols-2 gap-4">
                    <div class="glassmorphism-card rounded-xl p-4">
                        <div class="text-xs text-glass-secondary mb-1">Format File</div>
                        <div class="text-lg font-bold text-glass-primary">
                            {{ strtoupper(pathinfo($download->file_path ?? '', PATHINFO_EXTENSION) ?: 'N/A') }}
                        </div>
                    </div>
                    
                    <div class="glassmorphism-card rounded-xl p-4">
                        <div class="text-xs text-glass-secondary mb-1">Ukuran File</div>
                        <div class="text-lg font-bold text-glass-primary">
                            {{ $download->formatted_file_size ?? 'N/A' }}
                        </div>
                    </div>
                    
                    <div class="glassmorphism-card rounded-xl p-4">
                        <div class="text-xs text-glass-secondary mb-1">Kualitas</div>
                        <div class="text-lg font-bold text-glass-primary">
                            {{ $download->quality ?? 'Best' }}
                        </div>
                    </div>
                    
                    <div class="glassmorphism-card rounded-xl p-4">
                        <div class="text-xs text-glass-secondary mb-1">Platform</div>
                        <div class="text-lg font-bold text-glass-primary flex items-center gap-2">
                            <i class="{{ $download->platform->icon ?? 'fas fa-globe' }}"></i>
                            {{ $download->platform->display_name ?? ucfirst($download->platform) }}
                        </div>
                    </div>
                </div>

                @if($download->file_path)
                    <div class="mt-4 glassmorphism-card rounded-xl p-4">
                        <div class="text-xs text-glass-secondary mb-2">Lokasi File:</div>
                        <code class="text-xs text-glass-primary bg-gray-100 px-3 py-2 rounded-lg block overflow-x-auto">
                            {{ $download->file_path }}
                        </code>
                    </div>
                @endif
            </div>

            <!-- Metadata (if available) -->
            @if(isset($download->metadata) && is_array($download->metadata) && count($download->metadata) > 0)
                <div class="glassmorphism-card rounded-2xl p-6 shadow-xl">
                    <h2 class="text-xl font-bold text-glass-primary mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-glass-accent"></i>
                        <span>Informasi Tambahan</span>
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($download->metadata as $key => $value)
                            @if($value && !in_array($key, ['title', 'thumbnail', 'duration', 'url']))
                                <div class="glassmorphism-card rounded-lg p-4">
                                    <div class="text-xs text-glass-secondary mb-1">
                                        {{ ucfirst(str_replace('_', ' ', $key)) }}
                                    </div>
                                    <div class="text-sm font-semibold text-glass-primary break-all">
                                        {{ is_array($value) ? json_encode($value) : $value }}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="glassmorphism-card rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-glass-primary mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-line text-glass-accent"></i>
                    <span>Statistik</span>
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-white/30">
                        <span class="text-sm text-glass-secondary">Status</span>
                        <span class="px-3 py-1 text-xs font-bold rounded-full
                            @if($download->status === 'completed') bg-green-100 text-green-700
                            @elseif($download->status === 'downloading') bg-blue-100 text-blue-700
                            @elseif($download->status === 'failed') bg-red-100 text-red-700
                            @else bg-yellow-100 text-yellow-700 @endif">
                            {{ ucfirst($download->status) }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between py-3 border-b border-white/30">
                        <span class="text-sm text-glass-secondary">Dimulai</span>
                        <span class="text-sm font-semibold text-glass-primary">
                            {{ $download->created_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between py-3 border-b border-white/30">
                        <span class="text-sm text-glass-secondary">Diperbarui</span>
                        <span class="text-sm font-semibold text-glass-primary">
                            {{ $download->updated_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between py-3">
                        <span class="text-sm text-glass-secondary">Waktu Proses</span>
                        <span class="text-sm font-semibold text-glass-primary">
                            {{ $download->created_at->diffForHumans($download->updated_at, true) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Download Again -->
            <div class="glassmorphism-card rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-glass-primary mb-3">Unduh dengan Kualitas Lain?</h3>
                <p class="text-sm text-glass-secondary mb-4">
                    Unduh ulang media ini dengan kualitas atau format berbeda
                </p>
                <a href="{{ route('downloads.create') }}?url={{ urlencode($download->url) }}"
                   class="glassmorphism-button w-full text-white font-bold py-3 px-4 rounded-xl glass-hover flex items-center justify-center gap-2">
                    <i class="fas fa-redo"></i>
                    <span>Unduh Ulang</span>
                </a>
            </div>

            <!-- Help Card -->
            <div class="glassmorphism-card rounded-2xl p-6 shadow-xl bg-gradient-to-br from-orange-50 to-yellow-50">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-orange-400 to-orange-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-cat text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-glass-primary mb-2">Butuh Bantuan?</h3>
                    <p class="text-sm text-glass-secondary mb-4">
                        Jika ada masalah dengan download, silakan hubungi support kami
                    </p>
                    <button class="glassmorphism-card w-full px-4 py-2 rounded-lg font-semibold text-glass-primary hover:bg-white/70 transition-all duration-300">
                        <i class="fas fa-question-circle mr-2"></i>
                        Pusat Bantuan
                    </button>
                </div>
            </div>
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
                    Apakah Anda yakin ingin menghapus download ini? File yang sudah diunduh juga akan dihapus dan tidak dapat dikembalikan.
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

<!-- Share Modal -->
<div id="shareModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeShareModal()"></div>
        
        <div class="glassmorphism-card rounded-2xl p-8 max-w-md w-full relative z-10 shadow-2xl">
            <div class="text-center mb-6">
                <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-share-alt text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-glass-primary mb-2">Bagikan Media</h3>
                <p class="text-glass-secondary">Bagikan link media ini ke teman Anda</p>
            </div>
            
            <div class="glassmorphism-card rounded-xl p-4 mb-4">
                <input type="text" 
                       id="shareUrl" 
                       value="{{ $download->url }}" 
                       readonly
                       class="w-full bg-transparent text-sm text-glass-primary outline-none">
            </div>
            
            <div class="grid grid-cols-4 gap-3 mb-4">
                <button onclick="shareToWhatsApp()" class="glassmorphism-card p-4 rounded-xl glass-hover">
                    <i class="fab fa-whatsapp text-2xl text-green-500"></i>
                </button>
                <button onclick="shareToTwitter()" class="glassmorphism-card p-4 rounded-xl glass-hover">
                    <i class="fab fa-twitter text-2xl text-blue-400"></i>
                </button>
                <button onclick="shareToFacebook()" class="glassmorphism-card p-4 rounded-xl glass-hover">
                    <i class="fab fa-facebook text-2xl text-blue-600"></i>
                </button>
                <button onclick="copyShareUrl()" class="glassmorphism-card p-4 rounded-xl glass-hover">
                    <i class="fas fa-copy text-2xl text-glass-accent"></i>
                </button>
            </div>
            
            <button onclick="closeShareModal()"
                    class="w-full glassmorphism-card px-6 py-3 rounded-xl font-bold text-glass-primary hover:bg-white/50 transition-all duration-300">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let deleteDownloadId = null;

function deleteDownload(downloadId) {
    deleteDownloadId = downloadId;
    openDeleteModal();
}

function openDeleteModal() {
    $('#deleteModal').removeClass('hidden').addClass('flex');
    $('body').addClass('overflow-hidden');
}

function closeDeleteModal() {
    $('#deleteModal').removeClass('flex').addClass('hidden');
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
                setTimeout(() => {
                    window.location.href = '{{ route("downloads.index") }}';
                }, 1500);
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
                setTimeout(() => location.reload(), 2000);
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

function shareDownload() {
    openShareModal();
}

function openShareModal() {
    $('#shareModal').removeClass('hidden').addClass('flex');
    $('body').addClass('overflow-hidden');
}

function closeShareModal() {
    $('#shareModal').removeClass('flex').addClass('hidden');
    $('body').removeClass('overflow-hidden');
}

function shareToWhatsApp() {
    const url = $('#shareUrl').val();
    const text = 'Lihat video ini: ';
    window.open(`https://wa.me/?text=${encodeURIComponent(text + url)}`, '_blank');
}

function shareToTwitter() {
    const url = $('#shareUrl').val();
    const text = 'Lihat video ini!';
    window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`, '_blank');
}

function shareToFacebook() {
    const url = $('#shareUrl').val();
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
}

function copyShareUrl() {
    const urlInput = document.getElementById('shareUrl');
    urlInput.select();
    document.execCommand('copy');
    showToast('Link berhasil disalin! 📋', 'success');
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

// Auto-refresh if downloading
$(document).ready(function() {
    @if($download->status === 'downloading')
        setInterval(function() {
            checkDownloadStatus();
        }, 5000); // Check every 5 seconds
    @endif
});

function checkDownloadStatus() {
    $.ajax({
        url: '/downloads/{{ $download->id }}/status',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                // If status changed, reload page
                if (response.status !== '{{ $download->status }}') {
                    showToast('Status download diperbarui! 🔄', 'info');
                    setTimeout(() => location.reload(), 1500);
                }
                
                // Update progress bar if exists
                if (response.progress && response.progress !== {{ $download->progress ?? 0 }}) {
                    updateProgressBar(response.progress);
                }
            }
        },
        error: function(xhr) {
            console.error('Status check error:', xhr);
        }
    });
}

function updateProgressBar(progress) {
    const $progressBar = $('.bg-gradient-to-r.from-blue-400');
    const $progressText = $progressBar.closest('.mt-6').find('.font-bold');
    
    if ($progressBar.length && $progressText.length) {
        $progressBar.css('width', progress + '%');
        $progressText.text(progress + '%');
    }
}

// Close modals on Escape key
$(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
        closeShareModal();
    }
});

// Copy to clipboard fallback for older browsers
if (!document.execCommand) {
    function copyShareUrl() {
        const urlInput = document.getElementById('shareUrl');

        // Modern API
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(urlInput.value).then(() => {
                showToast('Link berhasil disalin! 📋', 'success');
            }).catch(err => {
                console.error('Copy failed:', err);
                showToast('Gagal menyalin link', 'error');
            });
        } else {
            // Fallback
            urlInput.select();
            try {
                document.execCommand('copy');
                showToast('Link berhasil disalin! 📋', 'success');
            } catch (err) {
                showToast('Gagal menyalin link', 'error');
            }
        }
    }
}

function playVideo(videoUrl, title) {
    // Create video modal
    const modal = $(`
        <div id="videoModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeVideoModal()"></div>

                <div class="glassmorphism-card rounded-2xl p-6 max-w-4xl w-full relative z-10 shadow-2xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-glass-primary">${title}</h3>
                        <button onclick="closeVideoModal()" class="text-glass-secondary hover:text-glass-primary transition-colors">
                            <i class="fas fa-times text-2xl"></i>
                        </button>
                    </div>

                    <div class="relative bg-black rounded-xl overflow-hidden">
                        <video id="videoPlayer" controls class="w-full max-h-96" preload="metadata">
                            <source src="${videoUrl}" type="video/mp4">
                            Browser Anda tidak mendukung pemutaran video.
                        </video>

                        <!-- Loading overlay -->
                        <div id="videoLoading" class="absolute inset-0 bg-black/50 flex items-center justify-center">
                            <div class="text-center">
                                <i class="fas fa-spinner fa-spin text-white text-3xl mb-2"></i>
                                <p class="text-white">Memuat video...</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-4 text-sm text-glass-secondary">
                        <span>Klik di luar modal untuk menutup</span>
                        <div class="flex gap-2">
                            <button onclick="toggleFullscreen()" class="glassmorphism-card px-3 py-1 rounded-lg text-xs">
                                <i class="fas fa-expand"></i> Fullscreen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `);

    $('body').append(modal);
    $('body').addClass('overflow-hidden');

    const video = modal.find('#videoPlayer')[0];
    const loading = modal.find('#videoLoading');

    // Hide loading when video can play
    video.addEventListener('canplay', function() {
        loading.fadeOut();
    });

    // Handle video errors
    video.addEventListener('error', function() {
        loading.html(`
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-red-400 text-3xl mb-2"></i>
                <p class="text-red-400">Gagal memuat video</p>
            </div>
        `);
    });

    // Auto-play when loaded
    video.addEventListener('loadedmetadata', function() {
        video.play().catch(e => {
            console.log('Autoplay prevented:', e);
        });
    });
}

function closeVideoModal() {
    const modal = $('#videoModal');
    if (modal.length) {
        const video = modal.find('#videoPlayer')[0];
        if (video) {
            video.pause();
            video.currentTime = 0;
        }
        modal.remove();
        $('body').removeClass('overflow-hidden');
    }
}

function toggleFullscreen() {
    const video = document.getElementById('videoPlayer');
    if (video) {
        if (video.requestFullscreen) {
            video.requestFullscreen();
        } else if (video.webkitRequestFullscreen) {
            video.webkitRequestFullscreen();
        } else if (video.msRequestFullscreen) {
            video.msRequestFullscreen();
        }
    }
}

// Close video modal on Escape key
$(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
        closeVideoModal();
    }
});
</script>
@endpush

@push('styles')
<style>
/* Animation for modals */
#deleteModal.flex,
#shareModal.flex {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Smooth transitions */
.transition-all {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 10px;
    height: 10px;
}

::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, var(--color-primary-400) 0%, var(--color-primary-600) 100%);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, var(--color-primary-500) 0%, var(--color-primary-700) 100%);
}

/* Hover effect for thumbnail */
.group:hover .opacity-0 {
    opacity: 1;
}

/* Pulse animation for progress bar */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Responsive utilities */
@media (max-width: 640px) {
    .text-2xl {
        font-size: 1.5rem;
    }
    
    .text-3xl {
        font-size: 1.875rem;
    }
}
</style>
@endpush