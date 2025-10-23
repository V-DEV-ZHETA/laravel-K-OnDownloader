@extends('layouts.app')

@section('title', 'Riwayat Download - NekoDrop')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb Navigation -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('downloads.create') }}" class="inline-flex items-center text-sm font-medium text-glass-secondary hover:text-glass-primary">
                    <i class="fas fa-home mr-2"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-glass-secondary mx-2"></i>
                    <span class="text-sm font-medium text-glass-primary">Riwayat Download</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header Section -->
    <div class="glassmorphism-card rounded-2xl p-6 md:p-8 mb-8 shadow-xl">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-purple-400 to-purple-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-history text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-glass-primary mb-1">
                        Riwayat Download
                    </h1>
                    <p class="text-glass-secondary">
                        Kelola semua download Anda di sini!
                    </p>
                </div>
            </div>
            <div class="flex gap-3">
                <button onclick="toggleBulkActions()" class="glassmorphism-card px-4 py-3 rounded-xl font-semibold text-glass-primary hover:bg-white/50 transition-all duration-300 glass-hover flex items-center gap-2">
                    <i class="fas fa-check-square"></i>
                    <span class="hidden sm:inline">Pilih</span>
                </button>
                <a href="{{ route('downloads.create') }}"
                   class="glassmorphism-button text-white font-bold py-3 px-6 rounded-xl glass-hover flex items-center gap-2 shadow-lg">
                    <i class="fas fa-plus"></i>
                    <span>Unduh Baru</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards with Mini Chart -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="glassmorphism-card rounded-xl p-5 glass-hover">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
                <div class="flex-1">
                    <div class="text-2xl font-bold text-glass-primary">{{ $downloads->where('status', 'completed')->count() }}</div>
                    <div class="text-sm text-glass-secondary">Selesai</div>
                    <div class="mt-2 h-8">
                        <canvas id="completedChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="glassmorphism-card rounded-xl p-5 glass-hover">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-spinner fa-pulse text-white text-xl"></i>
                </div>
                <div class="flex-1">
                    <div class="text-2xl font-bold text-glass-primary">{{ $downloads->where('status', 'downloading')->count() }}</div>
                    <div class="text-sm text-glass-secondary">Proses</div>
                    <div class="mt-2 h-8">
                        <canvas id="downloadingChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="glassmorphism-card rounded-xl p-5 glass-hover">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
                <div class="flex-1">
                    <div class="text-2xl font-bold text-glass-primary">{{ $downloads->where('status', 'pending')->count() }}</div>
                    <div class="text-sm text-glass-secondary">Menunggu</div>
                    <div class="mt-2 h-8">
                        <canvas id="pendingChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="glassmorphism-card rounded-xl p-5 glass-hover">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-list text-white text-xl"></i>
                </div>
                <div class="flex-1">
                    <div class="text-2xl font-bold text-glass-primary">{{ $downloads->total() }}</div>
                    <div class="text-sm text-glass-secondary">Total</div>
                    <div class="mt-2 h-8">
                        <canvas id="totalChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($downloads->count() > 0)
        <!-- Filter & Search Bar with Sorting -->
        <div class="glassmorphism-card rounded-xl p-4 mb-6">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                               id="searchInput"
                               placeholder="Cari berdasarkan judul, platform..."
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
                <select id="sortBy" class="glass-select px-4 py-3 rounded-lg text-glass-primary">
                    <option value="date-desc">Terbaru</option>
                    <option value="date-asc">Terlama</option>
                    <option value="title-asc">Judul (A-Z)</option>
                    <option value="title-desc">Judul (Z-A)</option>
                    <option value="size-desc">Ukuran Terbesar</option>
                    <option value="size-asc">Ukuran Terkecil</option>
                </select>
                <button onclick="toggleAdvancedSearch()" class="glassmorphism-card px-4 py-3 rounded-lg font-semibold text-glass-primary hover:bg-white/50 transition-all duration-300 glass-hover">
                    <i class="fas fa-sliders-h"></i>
                </button>
            </div>
            
            <!-- Advanced Search Panel -->
            <div id="advancedSearchPanel" class="hidden mt-4 pt-4 border-t border-white/30">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-glass-secondary mb-2">Tanggal Mulai</label>
                        <input type="date" id="dateFrom" class="glassmorphism-input w-full px-4 py-2 rounded-lg text-glass-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-glass-secondary mb-2">Tanggal Selesai</label>
                        <input type="date" id="dateTo" class="glassmorphism-input w-full px-4 py-2 rounded-lg text-glass-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-glass-secondary mb-2">Ukuran Minimum</label>
                        <select id="minSize" class="glass-select w-full px-4 py-2 rounded-lg text-glass-primary">
                            <option value="">Semua Ukuran</option>
                            <option value="1MB">> 1MB</option>
                            <option value="10MB">> 10MB</option>
                            <option value="50MB">> 50MB</option>
                            <option value="100MB">> 100MB</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <button onclick="resetFilters()" class="glassmorphism-card px-4 py-2 rounded-lg font-semibold text-glass-primary hover:bg-white/50 transition-all duration-300 mr-2">
                        Reset
                    </button>
                    <button onclick="applyAdvancedFilters()" class="glassmorphism-button text-white font-bold py-2 px-4 rounded-lg glass-hover">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- View Toggle and Selection Info -->
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-3">
                <span class="text-sm text-glass-secondary">Menampilkan {{ $downloads->firstItem() }}-{{ $downloads->lastItem() }} dari {{ $downloads->total() }} download</span>
                <div id="selectionInfo" class="hidden text-sm text-glass-primary font-medium">
                    <span id="selectedCount">0</span> item dipilih
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="setViewMode('grid')" id="gridViewBtn" class="p-2 rounded-lg glassmorphism-card text-glass-primary">
                    <i class="fas fa-th"></i>
                </button>
                <button onclick="setViewMode('list')" id="listViewBtn" class="p-2 rounded-lg text-glass-secondary">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>

        <!-- Downloads Grid/List Container -->
        <div class="space-y-4" id="downloadsContainer">
            @foreach($downloads as $download)
                <div class="glassmorphism-card rounded-xl p-5 glass-hover transition-all duration-300 download-item" 
                     data-status="{{ $download->status }}" 
                     data-platform="{{ $download->platform->name ?? $download->platform }}"
                     data-title="{{ strtolower($download->title) }}"
                     data-date="{{ $download->created_at->timestamp }}"
                     data-size="{{ $download->file_size ?? 0 }}"
                     data-id="{{ $download->id }}">
                    <div class="flex flex-col lg:flex-row gap-5">
                        <!-- Selection Checkbox -->
                        <div class="absolute top-4 left-4 z-10 hidden" id="checkbox-{{ $download->id }}">
                            <input type="checkbox" class="w-5 h-5 rounded" onchange="toggleItemSelection({{ $download->id }})">
                        </div>

                        <!-- Thumbnail & Platform Badge -->
                        <div class="relative flex-shrink-0">
                            @if($download->thumbnail)
                                <div class="w-full lg:w-48 h-32 rounded-xl overflow-hidden glassmorphism-card">
                                    <img class="w-full h-full object-cover lazy-load"
                                         data-src="{{ strpos($download->thumbnail, 'http') === 0 ? $download->thumbnail : asset('storage/' . $download->thumbnail) }}"
                                         alt="{{ $download->title }}"
                                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gray-100\'><i class=\'fas fa-video text-4xl text-gray-400\'></i></div>';">
                                </div>
                            @else
                                <div class="w-full lg:w-48 h-32 glassmorphism-card rounded-xl flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                    <i class="fas fa-video text-5xl text-glass-secondary"></i>
                                </div>
                            @endif
                            
                            <!-- Platform Badge -->
                            <div class="absolute top-3 left-3 bg-black/70 backdrop-blur-sm rounded-lg px-3 py-1.5 flex items-center gap-2 border border-white/20">
                                @if($download->platform && is_object($download->platform))
                                    <i class="{{ $download->platform->icon }} text-white text-sm"></i>
                                    <span class="text-xs font-bold text-white">
                                        {{ $download->platform->display_name ?? ucfirst($download->platform->name) }}
                                    </span>
                                @else
                                    <i class="fas fa-globe text-gray-400 text-sm"></i>
                                    <span class="text-xs font-bold text-white">
                                        {{ ucfirst($download->platform) }}
                                    </span>
                                @endif
                            </div>

                            <!-- Duration Badge -->
                            @if($download->duration)
                                <div class="absolute bottom-3 right-3 bg-black/70 backdrop-blur-sm rounded-lg px-3 py-1.5 border border-white/20">
                                    <span class="text-xs font-bold text-white">
                                        {{ $download->duration }}
                                    </span>
                                </div>
                            @endif
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
                                        <span class="flex items-center gap-1.5">
                                            <i class="fas fa-database text-xs"></i>
                                            {{ $download->formatted_file_size ?? 'N/A' }}
                                        </span>
                                        <span class="flex items-center gap-1.5">
                                            <i class="fas fa-calendar text-xs"></i>
                                            {{ $download->created_at->diffForHumans() }}
                                        </span>
                                        @if($download->quality)
                                            <span class="flex items-center gap-1.5">
                                                <i class="fas fa-tv text-xs"></i>
                                                {{ $download->quality }}
                                            </span>
                                        @endif
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
                                            <div class="flex justify-between text-xs text-glass-secondary mt-1">
                                                <span>{{ $download->downloaded_size ?? '0 MB' }} / {{ $download->formatted_file_size ?? 'N/A' }}</span>
                                                <span>{{ $download->speed ?? 'N/A' }}</span>
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
                                    
                                    @if($download->status === 'downloading')
                                        <button onclick="pauseDownload({{ $download->id }})"
                                                class="glassmorphism-button-secondary text-glass-dark font-bold py-2 px-4 rounded-lg glass-hover flex items-center gap-2 text-sm">
                                            <i class="fas fa-pause"></i>
                                            <span>Jeda</span>
                                        </button>
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
                                    
                                    <div class="relative">
                                        <button onclick="toggleMoreOptions({{ $download->id }})"
                                                class="glassmorphism-card px-4 py-2 rounded-lg font-semibold text-glass-primary hover:bg-white/50 transition-all duration-300 glass-hover flex items-center gap-2 text-sm">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        
                                        <!-- More Options Dropdown -->
                                        <div id="moreOptions-{{ $download->id }}" class="hidden absolute right-0 mt-2 w-48 glassmorphism-card rounded-lg shadow-xl z-20">
                                            <button onclick="shareDownload({{ $download->id }})" class="w-full text-left px-4 py-2 text-sm text-glass-primary hover:bg-white/50 transition-colors">
                                                <i class="fas fa-share-alt mr-2"></i> Bagikan
                                            </button>
                                            <button onclick="addToFolder({{ $download->id }})" class="w-full text-left px-4 py-2 text-sm text-glass-primary hover:bg-white/50 transition-colors">
                                                <i class="fas fa-folder-plus mr-2"></i> Tambah ke Folder
                                            </button>
                                            <button onclick="duplicateDownload({{ $download->id }})" class="w-full text-left px-4 py-2 text-sm text-glass-primary hover:bg-white/50 transition-colors">
                                                <i class="fas fa-copy mr-2"></i> Duplikat
                                            </button>
                                            <hr class="my-1 border-white/30">
                                            <button onclick="deleteDownload({{ $download->id }})" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                <i class="fas fa-trash mr-2"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination with Enhanced Navigation -->
        <div class="mt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-sm text-glass-secondary">
                Menampilkan {{ $downloads->firstItem() }}-{{ $downloads->lastItem() }} dari {{ $downloads->total() }} download
            </div>
            {{ $downloads->links() }}
        </div>
    @else
        <!-- Empty State with Illustration -->
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
                
                <!-- Quick Tips -->
                <div class="mt-8 text-left glassmorphism-card rounded-xl p-4">
                    <h4 class="font-bold text-glass-primary mb-2 flex items-center gap-2">
                        <i class="fas fa-lightbulb text-yellow-500"></i>
                        Tips Cepat
                    </h4>
                    <ul class="text-sm text-glass-secondary space-y-1">
                        <li>• Tempel link video dari platform yang didukung</li>
                        <li>• Pilih kualitas video yang Anda inginkan</li>
                        <li>• Unduh dalam format MP4 atau MP3</li>
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Bulk Actions Bar -->
    <div id="bulkActions" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-50">
        <div class="glassmorphism-card rounded-2xl p-4 shadow-2xl flex items-center gap-4">
            <span class="text-sm font-semibold text-glass-primary">
                <span id="selectedCount">0</span> item dipilih
            </span>
            <button onclick="downloadSelected()" class="glassmorphism-button text-white font-bold py-2 px-4 rounded-lg glass-hover text-sm">
                <i class="fas fa-download mr-2"></i>
                Unduh Semua
            </button>
            <button onclick="moveSelectedToFolder()" class="glassmorphism-card px-4 py-2 rounded-lg font-semibold text-glass-primary hover:bg-white/50 transition-all duration-300 text-sm">
                <i class="fas fa-folder mr-2"></i>
                Pindah ke Folder
            </button>
            <button onclick="deleteSelected()" class="glassmorphism-card px-4 py-2 rounded-lg font-semibold text-red-600 hover:bg-red-50 transition-all duration-300 text-sm">
                <i class="fas fa-trash mr-2"></i>
                Hapus Semua
            </button>
            <button onclick="clearSelection()" class="glassmorphism-card px-4 py-2 rounded-lg font-semibold text-glass-primary hover:bg-white/50 transition-all duration-300 text-sm">
                <i class="fas fa-times mr-2"></i>
                Batal
            </button>
        </div>
    </div>

    <!-- Folder Selection Modal -->
    <div id="folderModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeFolderModal()"></div>
            
            <div class="glassmorphism-card rounded-2xl p-8 max-w-md w-full relative z-10 shadow-2xl">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-folder text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-glass-primary mb-2">Pilih Folder</h3>
                    <p class="text-glass-secondary">
                        Pilih folder untuk menyimpan download ini
                    </p>
                </div>
                
                <div class="mb-6">
                    <div class="folder-option glassmorphism-card rounded-lg p-3 mb-3 flex items-center gap-3 cursor-pointer hover:bg-white/50 transition-colors" data-folder="Video Musik" onclick="selectFolder(this)">
                        <i class="fas fa-folder text-blue-500"></i>
                        <span class="font-medium text-glass-primary">Video Musik</span>
                    </div>
                    <div class="folder-option glassmorphism-card rounded-lg p-3 mb-3 flex items-center gap-3 cursor-pointer hover:bg-white/50 transition-colors" data-folder="Tutorial" onclick="selectFolder(this)">
                        <i class="fas fa-folder text-green-500"></i>
                        <span class="font-medium text-glass-primary">Tutorial</span>
                    </div>
                    <div class="folder-option glassmorphism-card rounded-lg p-3 mb-3 flex items-center gap-3 cursor-pointer hover:bg-white/50 transition-colors" data-folder="Hiburan" onclick="selectFolder(this)">
                        <i class="fas fa-folder text-purple-500"></i>
                        <span class="font-medium text-glass-primary">Hiburan</span>
                    </div>
                    <button onclick="showNewFolderForm()" class="w-full glassmorphism-card rounded-lg p-3 flex items-center justify-center gap-2 text-glass-primary hover:bg-white/50 transition-colors">
                        <i class="fas fa-plus"></i>
                        <span>Buat Folder Baru</span>
                    </button>
                </div>
                
                <div class="flex gap-3">
                    <button onclick="closeFolderModal()"
                            class="flex-1 glassmorphism-card px-6 py-3 rounded-xl font-bold text-glass-primary hover:bg-white/50 transition-all duration-300">
                        Batal
                    </button>
                    <button onclick="confirmFolderSelection()"
                            class="flex-1 glassmorphism-button text-white font-bold py-3 px-6 rounded-xl glass-hover">
                        Pilih
                    </button>
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
                    <h3 id="deleteModalTitle" class="text-2xl font-bold text-glass-primary mb-2">Hapus Download?</h3>
                    <p id="deleteModalMessage" class="text-glass-secondary">
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
#deleteModal.show, #folderModal.show {
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

/* List view styles */
.list-view .download-item {
    display: flex;
    align-items: center;
}

.list-view .download-item .flex-col {
    flex-direction: row;
}

.list-view .download-item .w-full.lg\\:w-48 {
    width: 120px;
    height: 80px;
}

.list-view .download-item .flex-1 {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.list-view .download-item .flex.flex-col {
    flex-direction: row;
    align-items: center;
}

.list-view .download-item .border-t {
    border-top: none;
    border-left: 1px solid rgba(255, 255, 255, 0.3);
    padding-left: 1rem;
    margin-left: 1rem;
}

/* Checkbox selection styles */
.download-item.selected {
    border: 2px solid rgba(99, 102, 241, 0.5);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

/* Lazy loading placeholder */
.lazy-load {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

/* Tooltip styles */
.tooltip {
    position: relative;
}

.tooltip:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background-color: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    white-space: nowrap;
    z-index: 10;
}
</style>
@endpush

@push('scripts')
<script>
let deleteDownloadId = null;
let selectedItems = new Set();
let bulkSelectionMode = false;
let currentViewMode = 'grid';

 $(document).ready(function() {
    // Initialize lazy loading
    initLazyLoading();
    
    // Initialize mini charts
    initMiniCharts();
    
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
    
    // Sort functionality
    $('#sortBy').on('change', function() {
        sortDownloads();
    });

    // Auto-refresh for downloading items
    setInterval(function() {
        if ($('.fa-spinner.fa-spin').length > 0) {
            refreshDownloadStatus();
        }
    }, 10000); // Check every 10 seconds
    
    // Close dropdowns when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.relative').length) {
            $('.relative .absolute').addClass('hidden');
        }
    });
});

function initLazyLoading() {
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy-load');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('.lazy-load').forEach(img => {
            imageObserver.observe(img);
        });
    } else {
        // Fallback for browsers that don't support IntersectionObserver
        document.querySelectorAll('.lazy-load').forEach(img => {
            img.src = img.dataset.src;
            img.classList.remove('lazy-load');
        });
    }
}

function initMiniCharts() {
    // Simple sparkline charts for stats cards
    const chartOptions = {
        type: 'line',
        data: {
            labels: ['', '', '', '', '', ''],
            datasets: [{
                data: [12, 19, 8, 15, 12, 17],
                borderColor: 'rgba(99, 102, 241, 0.5)',
                borderWidth: 2,
                fill: false,
                tension: 0.4,
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    display: false
                },
                y: {
                    display: false
                }
            }
        }
    };
    
    // Initialize charts if Chart.js is available
    if (typeof Chart !== 'undefined') {
        new Chart(document.getElementById('completedChart'), chartOptions);
        new Chart(document.getElementById('downloadingChart'), chartOptions);
        new Chart(document.getElementById('pendingChart'), chartOptions);
        new Chart(document.getElementById('totalChart'), chartOptions);
    }
}

function toggleBulkActions() {
    bulkSelectionMode = !bulkSelectionMode;

    if (bulkSelectionMode) {
        $('.download-item .absolute').removeClass('hidden');
        $('#bulkActions').removeClass('hidden');
    } else {
        $('.download-item .absolute').addClass('hidden');
        $('#bulkActions').addClass('hidden');
        clearSelection();
    }
}

function toggleItemSelection(id) {
    if (selectedItems.has(id)) {
        selectedItems.delete(id);
        $('.download-item[data-id="' + id + '"]').removeClass('selected');
    } else {
        selectedItems.add(id);
        $('.download-item[data-id="' + id + '"]').addClass('selected');
    }
    
    updateSelectionUI();
}

function updateSelectionUI() {
    $('#selectedCount').text(selectedItems.size);
    
    if (selectedItems.size > 0) {
        $('#selectionInfo').removeClass('hidden');
    } else {
        $('#selectionInfo').addClass('hidden');
    }
}

function clearSelection() {
    selectedItems.clear();
    $('.download-item').removeClass('selected');
    $('.download-item input[type="checkbox"]').prop('checked', false);
    updateSelectionUI();
}

function downloadSelected() {
    if (selectedItems.size === 0) return;
    
    showToast('Mengunduh ' + selectedItems.size + ' item...', 'info');
    
    // Implementation would depend on your backend
    selectedItems.forEach(id => {
        // Trigger download for each item
        window.open('/downloads/' + id + '/download', '_blank');
    });
    
    clearSelection();
}

function deleteSelected() {
    if (selectedItems.size === 0) return;

    // Show confirmation modal with bulk delete message
    deleteDownloadId = Array.from(selectedItems);
    openDeleteModal(true);
}

function moveSelectedToFolder() {
    if (selectedItems.size === 0) return;
    
    openFolderModal();
}

function setViewMode(mode) {
    currentViewMode = mode;
    
    if (mode === 'grid') {
        $('#downloadsContainer').removeClass('list-view');
        $('#gridViewBtn').addClass('text-glass-primary').removeClass('text-glass-secondary');
        $('#listViewBtn').addClass('text-glass-secondary').removeClass('text-glass-primary');
    } else {
        $('#downloadsContainer').addClass('list-view');
        $('#listViewBtn').addClass('text-glass-primary').removeClass('text-glass-secondary');
        $('#gridViewBtn').addClass('text-glass-secondary').removeClass('text-glass-primary');
    }
    
    // Save preference
    localStorage.setItem('downloadViewMode', mode);
}

function toggleMoreOptions(id) {
    $('#moreOptions-' + id).toggleClass('hidden');
    
    // Close other dropdowns
    $('.download-item .absolute').not('#moreOptions-' + id).addClass('hidden');
}

function toggleAdvancedSearch() {
    $('#advancedSearchPanel').toggleClass('hidden');
}

function resetFilters() {
    $('#searchInput').val('');
    $('#filterStatus').val('');
    $('#filterPlatform').val('');
    $('#sortBy').val('date-desc');
    $('#dateFrom').val('');
    $('#dateTo').val('');
    $('#minSize').val('');
    
    filterDownloads();
}

function applyAdvancedFilters() {
    filterDownloads();
    $('#advancedSearchPanel').addClass('hidden');
}

function sortDownloads() {
    const sortBy = $('#sortBy').val();
    const $container = $('#downloadsContainer');
    const $items = $('.download-item');
    
    $items.sort(function(a, b) {
        const $a = $(a);
        const $b = $(b);
        
        switch(sortBy) {
            case 'date-desc':
                return $b.data('date') - $a.data('date');
            case 'date-asc':
                return $a.data('date') - $b.data('date');
            case 'title-asc':
                return $a.data('title').localeCompare($b.data('title'));
            case 'title-desc':
                return $b.data('title').localeCompare($a.data('title'));
            case 'size-desc':
                return $b.data('size') - $a.data('size');
            case 'size-asc':
                return $a.data('size') - $b.data('size');
            default:
                return 0;
        }
    });
    
    $items.detach().appendTo($container);
}

function pauseDownload(id) {
    showToast('Download dijeda', 'info');
    
    $.ajax({
        url: `/downloads/${id}/pause`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(response.message || 'Gagal menjeda download', 'error');
            }
        },
        error: function(xhr) {
            showToast('Gagal menjeda download', 'error');
        }
    });
}

function addToFolder(id) {
    // Implementation would depend on your backend
    showToast('Menambahkan ke folder...', 'info');
}

function shareDownload(id) {
    // Simple share functionality using Web Share API if available
    if (navigator.share) {
        navigator.share({
            title: 'NekoDrop Download',
            text: 'Lihat download saya di NekoDrop',
            url: window.location.origin + '/downloads/' + id
        }).catch(console.error);
    } else {
        // Fallback: copy link to clipboard
        const url = window.location.origin + '/downloads/' + id;
        navigator.clipboard.writeText(url).then(() => {
            showToast('Link berhasil disalin ke clipboard! 📋', 'success');
        }).catch(() => {
            showToast('Gagal menyalin link', 'error');
        });
    }
}

function duplicateDownload(id) {
    showToast('Menduplikasi download...', 'info');
    
    $.ajax({
        url: `/downloads/${id}/duplicate`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showToast('Download berhasil diduplikasi!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(response.message || 'Gagal menduplikasi download', 'error');
            }
        },
        error: function(xhr) {
            showToast('Gagal menduplikasi download', 'error');
        }
    });
}

function openFolderModal() {
    $('#folderModal').removeClass('hidden').addClass('show');
    $('body').addClass('overflow-hidden');
}

function closeFolderModal() {
    $('#folderModal').removeClass('show').addClass('hidden');
    $('body').removeClass('overflow-hidden');
}

let selectedFolder = null;

function selectFolder(element) {
    $('.folder-option').removeClass('bg-white/50');
    $(element).addClass('bg-white/50');
    selectedFolder = $(element).data('folder');
}

function confirmFolderSelection() {
    if (!selectedFolder) {
        showToast('Pilih folder terlebih dahulu', 'warning');
        return;
    }

    $.ajax({
        url: '/downloads/bulk-update-folder',
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: JSON.stringify({
            ids: Array.from(selectedItems),
            folder: selectedFolder
        }),
        contentType: 'application/json',
        success: function(response) {
            if (response.success) {
                showToast(response.message, 'success');
                closeFolderModal();
                clearSelection();
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(response.message || 'Gagal memindahkan ke folder', 'error');
            }
        },
        error: function(xhr) {
            console.error('Bulk update folder error:', xhr);
            showToast('Gagal memindahkan ke folder', 'error');
        }
    });
}

function showNewFolderForm() {
    // Implementation would depend on your backend
    const folderName = prompt('Masukkan nama folder baru:');
    if (folderName) {
        showToast('Membuat folder baru...', 'info');
        // Create folder logic here
    }
}

function filterDownloads() {
    const searchTerm = $('#searchInput').val().toLowerCase();
    const statusFilter = $('#filterStatus').val();
    const platformFilter = $('#filterPlatform').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    const minSize = $('#minSize').val();

    $('.download-item').each(function() {
        const $item = $(this);
        const title = $item.data('title');
        const status = $item.data('status');
        const platform = $item.data('platform');
        const date = new Date($item.data('date') * 1000);
        const size = $item.data('size');

        const matchesSearch = !searchTerm || title.includes(searchTerm);
        const matchesStatus = !statusFilter || status === statusFilter;
        const matchesPlatform = !platformFilter || platform === platformFilter;
        const matchesDateFrom = !dateFrom || date >= new Date(dateFrom);
        const matchesDateTo = !dateTo || date <= new Date(dateTo);
        const matchesMinSize = !minSize || size >= parseSize(minSize);

        if (matchesSearch && matchesStatus && matchesPlatform && matchesDateFrom && matchesDateTo && matchesMinSize) {
            $item.removeClass('hidden').addClass('fade-in');
        } else {
            $item.addClass('hidden').removeClass('fade-in');
        }
    });

    // Show empty state if no results
    updateEmptyState();
}

function parseSize(sizeStr) {
    const units = {
        'B': 1,
        'KB': 1024,
        'MB': 1024 * 1024,
        'GB': 1024 * 1024 * 1024
    };
    
    const match = sizeStr.match(/^(\d+)(B|KB|MB|GB)$/);
    if (!match) return 0;
    
    return parseInt(match[1]) * units[match[2]];
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
                    <button onclick="resetFilters()" class="mt-4 glassmorphism-button text-white font-bold py-2 px-4 rounded-lg glass-hover">
                        Reset Filter
                    </button>
                </div>
            `);
        }
    } else {
        $('#noResults').remove();
    }
}

function deleteDownload(downloadId) {
    deleteDownloadId = downloadId;
    openDeleteModal(false);
}

function openDeleteModal(isBulk = false) {
    if (isBulk) {
        $('#deleteModalTitle').text('Hapus Semua Download?');
        $('#deleteModalMessage').text(`Apakah Anda yakin ingin menghapus ${selectedItems.size} download yang dipilih? File yang sudah diunduh juga akan dihapus.`);
    } else {
        $('#deleteModalTitle').text('Hapus Download?');
        $('#deleteModalMessage').text('Apakah Anda yakin ingin menghapus download ini? File yang sudah diunduh juga akan dihapus.');
    }

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

    const isBulk = Array.isArray(deleteDownloadId);
    const ids = isBulk ? deleteDownloadId : [deleteDownloadId];

    $.ajax({
        url: isBulk ? '/downloads/bulk-delete' : `/downloads/${deleteDownloadId}`,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type': isBulk ? 'application/json' : 'application/x-www-form-urlencoded'
        },
        data: isBulk ? JSON.stringify({ ids: ids }) : null,
        success: function(response) {
            if (response.success) {
                showToast(isBulk ? `${ids.length} download berhasil dihapus!` : 'Download berhasil dihapus! 🗑️', 'success');

                // Animate removal
                ids.forEach(id => {
                    const $item = $(`.download-item[data-id="${id}"]`);
                    $item.addClass('hiding');

                    setTimeout(() => {
                        $item.remove();
                        updateEmptyState();
                    }, 300);
                });

                // Reload if page is empty
                if ($('.download-item').length === 0) {
                    setTimeout(() => location.reload(), 1000);
                }
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
            clearSelection();
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
        downloadIds.push($(this).data('id'));
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
                    const $item = $(`.download-item[data-id="${download.id}"]`);
                    
                    if (download.status === 'completed' || download.status === 'failed') {
                        hasChanges = true;
                    }
                    
                    // Update progress bar if still downloading
                    if (download.status === 'downloading' && download.progress) {
                        $item.find('.bg-gradient-to-r').css('width', download.progress + '%');
                        $item.find('.font-semibold').text(download.progress + '%');
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
        closeFolderModal();
    }
});

// Load saved view mode
 $(document).ready(function() {
    const savedViewMode = localStorage.getItem('downloadViewMode');
    if (savedViewMode) {
        setViewMode(savedViewMode);
    }
});
</script>
@endpush    