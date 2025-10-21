@extends('layouts.app')

@section('title', 'Platform - NekoDrop')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="glassmorphism-card rounded-2xl p-6 md:p-8 mb-8 shadow-xl">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-glass-primary mb-1">
                        Platform Media
                    </h1>
                    <p class="text-glass-secondary">
                        Kelola platform yang didukung untuk download!!
                    </p>
                </div>
            </div>
            <a href="{{ route('settings.index') }}"
               class="glassmorphism-button text-white font-bold py-3 px-6 rounded-xl glass-hover flex items-center gap-2 shadow-lg">
                <i class="fas fa-sliders-h"></i>
                <span>Pengaturan</span>
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="glassmorphism-card rounded-xl p-5 glass-hover">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-glass-primary">{{ $platforms->where('is_active', true)->count() }}</div>
                    <div class="text-sm text-glass-secondary">Aktif</div>
                </div>
            </div>
        </div>
        
        <div class="glassmorphism-card rounded-xl p-5 glass-hover">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-red-400 to-red-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-white text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-glass-primary">{{ $platforms->where('is_active', false)->count() }}</div>
                    <div class="text-sm text-glass-secondary">Nonaktif</div>
                </div>
            </div>
        </div>
        
        <div class="glassmorphism-card rounded-xl p-5 glass-hover">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-list text-white text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-glass-primary">{{ $platforms->count() }}</div>
                    <div class="text-sm text-glass-secondary">Total</div>
                </div>
            </div>
        </div>
        
        <div class="glassmorphism-card rounded-xl p-5 glass-hover">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-download text-white text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-glass-primary">100%</div>
                    <div class="text-sm text-glass-secondary">Dukungan</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="glassmorphism-card rounded-xl p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" 
                           id="searchPlatform"
                           placeholder="Cari platform..."
                           class="glassmorphism-input w-full pl-11 pr-4 py-3 rounded-lg text-glass-primary">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-glass-secondary"></i>
                </div>
            </div>
            <select id="filterStatus" class="glass-select px-4 py-3 rounded-lg text-glass-primary">
                <option value="">Semua Status</option>
                <option value="active">Aktif Saja</option>
                <option value="inactive">Nonaktif Saja</option>
            </select>
        </div>
    </div>

    <!-- Platforms Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="platformsContainer">
        @foreach($platforms as $platform)
            <div class="platform-item glassmorphism-card rounded-2xl p-6 glass-hover transition-all duration-300" 
                 data-name="{{ strtolower($platform->display_name) }}"
                 data-status="{{ $platform->is_active ? 'active' : 'inactive' }}">
                
                <!-- Platform Icon & Status Badge -->
                <div class="relative mb-6">
                    <div class="w-20 h-20 mx-auto rounded-2xl flex items-center justify-center
                        {{ $platform->is_active 
                            ? 'bg-gradient-to-br from-green-400 to-green-500' 
                            : 'bg-gradient-to-br from-gray-300 to-gray-400' }}
                        shadow-lg transition-all duration-300 group-hover:scale-110">
                        <i class="{{ $platform->icon }} text-black text-5xl"></i>
                    </div>
                    
                    <!-- Status Badge -->
                    <div class="absolute -top-2 -right-2">
                        @if($platform->is_active)
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center border-4 border-white shadow-lg">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                        @else
                            <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center border-4 border-white shadow-lg">
                                <i class="fas fa-times text-white text-xs"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Platform Info -->
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-glass-primary mb-2">
                        {{ $platform->display_name }}
                    </h3>
                    
                    <span class="inline-flex px-3 py-1.5 text-xs font-bold rounded-full
                        {{ $platform->is_active 
                            ? 'bg-green-100 text-green-700' 
                            : 'bg-red-100 text-red-700' }}">
                        @if($platform->is_active)
                            <i class="fas fa-check-circle mr-1"></i>
                            <span>Platform Aktif</span>
                        @else
                            <i class="fas fa-pause-circle mr-1"></i>
                            <span>Platform Nonaktif</span>
                        @endif
                    </span>
                </div>

                <!-- Platform Stats (if available) -->
                @if(isset($platform->downloads_count))
                    <div class="glassmorphism-card rounded-xl p-3 mb-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-glass-secondary">Total Download</span>
                            <span class="font-bold text-glass-primary">{{ number_format($platform->downloads_count) }}</span>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="space-y-2">
                    <a href="{{ route('platforms.show', $platform) }}"
                       class="w-full glassmorphism-button-secondary text-glass-dark font-bold py-3 px-4 rounded-xl glass-hover flex items-center justify-center gap-2">
                        <i class="fas fa-eye"></i>
                        <span>Lihat Detail</span>
                    </a>

                    <button onclick="togglePlatform({{ $platform->id }}, this)"
                            class="w-full font-bold py-3 px-4 rounded-xl glass-hover flex items-center justify-center gap-2 transition-all duration-300
                            {{ $platform->is_active 
                                ? 'bg-red-500 text-white' 
                                : 'bg-green-500 text-white' }}">
                        @if($platform->is_active)
                            <i class="fas fa-pause"></i>
                            <span>Nonaktifkan</span>
                        @else
                            <i class="fas fa-play"></i>
                            <span>Aktifkan</span>
                        @endif
                    </button>
                </div>

                <!-- Additional Info -->
                <div class="mt-4 pt-4 border-t border-white/30">
                    <div class="flex items-center justify-between text-xs text-glass-secondary">
                        @if($platform->version)
                            <span class="px-2 py-1 glassmorphism-card rounded-full">
                                v{{ $platform->version }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="hidden glassmorphism-card rounded-2xl p-12 text-center">
        <div class="max-w-md mx-auto">
            <div class="w-24 h-24 mx-auto mb-6 glassmorphism-card rounded-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                <i class="fas fa-search text-5xl text-glass-secondary"></i>
            </div>
            <h3 class="text-2xl font-bold text-glass-primary mb-3">Platform Tidak Ditemukan</h3>
            <p class="text-glass-secondary text-lg">
                Coba ubah filter atau kata kunci pencarian Anda
            </p>
        </div>
    </div>

    <!-- Help Section -->
    <div class="mt-8 glassmorphism-card rounded-2xl p-6 md:p-8 bg-gradient-to-br from-orange-50 to-yellow-50">
        <div class="flex flex-col md:flex-row items-center gap-6">
            <div class="w-20 h-20 bg-gradient-to-br from-orange-400 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
                <i class="fas fa-question-circle text-orange-500 text-5xl"></i>
            </div>
            <div class="flex-1 text-center md:text-left">
                <h3 class="text-xl font-bold text-glass-primary mb-2">Butuh Bantuan?</h3>
                <p class="text-glass-secondary mb-4">
                    Platform yang aktif akan tersedia untuk proses download. Nonaktifkan platform yang tidak diperlukan untuk mengoptimalkan performa.
                </p>
            </div>
            <a href="{{ route('settings.index') }}" 
               class="glassmorphism-button text-white font-bold py-3 px-6 rounded-xl glass-hover flex items-center gap-2 flex-shrink-0">
                <i class="fas fa-cog"></i>
                <span>Pengaturan Lanjutan</span>
            </a>
        </div>
    </div>
</div>

<!-- Toggle Confirmation Modal -->
<div id="toggleModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeToggleModal()"></div>
        
        <div class="glassmorphism-card rounded-2xl p-8 max-w-md w-full relative z-10 shadow-2xl">
            <div class="text-center mb-6">
                <div id="modalIcon" class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center">
                    <i class="fas text-white text-3xl"></i>
                </div>
                <h3 id="modalTitle" class="text-2xl font-bold text-glass-primary mb-2"></h3>
                <p id="modalMessage" class="text-glass-secondary"></p>
            </div>
            
            <div class="flex gap-3">
                <button onclick="closeToggleModal()"
                        class="flex-1 glassmorphism-card px-6 py-3 rounded-xl font-bold text-glass-primary hover:bg-white/50 transition-all duration-300">
                    Batal
                </button>
                <button id="confirmToggle"
                        class="flex-1 text-white font-bold py-3 px-6 rounded-xl glass-hover">
                    Konfirmasi
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.platform-item {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.platform-item.hiding {
    opacity: 0;
    transform: scale(0.95);
    pointer-events: none;
}

/* Modal animations */
#toggleModal.show {
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

/* Hover effect for platform icon */
.platform-item:hover .w-20.h-20 {
    transform: scale(1.1) rotate(5deg);
}
</style>
@endpush

@push('scripts')
<script>
let togglePlatformId = null;
let toggleButton = null;

$(document).ready(function() {
    // Search functionality
    $('#searchPlatform').on('input', function() {
        filterPlatforms();
    });

    // Filter by status
    $('#filterStatus').on('change', function() {
        filterPlatforms();
    });
});

function filterPlatforms() {
    const searchTerm = $('#searchPlatform').val().toLowerCase();
    const statusFilter = $('#filterStatus').val();

    let visibleCount = 0;

    $('.platform-item').each(function() {
        const $item = $(this);
        const name = $item.data('name');
        const status = $item.data('status');

        const matchesSearch = !searchTerm || name.includes(searchTerm);
        const matchesStatus = !statusFilter || status === statusFilter;

        if (matchesSearch && matchesStatus) {
            $item.removeClass('hidden').addClass('fade-in');
            visibleCount++;
        } else {
            $item.addClass('hidden').removeClass('fade-in');
        }
    });

    // Show/hide empty state
    if (visibleCount === 0) {
        $('#emptyState').removeClass('hidden');
        $('#platformsContainer').addClass('hidden');
    } else {
        $('#emptyState').addClass('hidden');
        $('#platformsContainer').removeClass('hidden');
    }
}

function togglePlatform(platformId, button) {
    togglePlatformId = platformId;
    toggleButton = button;
    
    const $button = $(button);
    const isActive = $button.find('.fa-pause').length > 0;
    
    // Set modal content
    const $modalIcon = $('#modalIcon');
    const $modalTitle = $('#modalTitle');
    const $modalMessage = $('#modalMessage');
    const $confirmButton = $('#confirmToggle');
    
    if (isActive) {
        // Deactivate
        $modalIcon.removeClass('bg-gradient-to-br from-green-400 to-green-500')
                  .addClass('bg-gradient-to-br from-red-400 to-red-500');
        $modalIcon.find('i').removeClass().addClass('fas fa-pause-circle text-white text-3xl');
        $modalTitle.text('Nonaktifkan Platform?');
        $modalMessage.text('Platform ini tidak akan tersedia untuk download setelah dinonaktifkan.');
        $confirmButton.removeClass('bg-gradient-to-r from-green-500 to-green-600')
                     .addClass('bg-gradient-to-r from-red-500 to-red-600');
        $confirmButton.text('Ya, Nonaktifkan');
    } else {
        // Activate
        $modalIcon.removeClass('bg-gradient-to-br from-red-400 to-red-500')
                  .addClass('bg-gradient-to-br from-green-400 to-green-500');
        $modalIcon.find('i').removeClass().addClass('fas fa-play-circle text-white text-3xl');
        $modalTitle.text('Aktifkan Platform?');
        $modalMessage.text('Platform ini akan tersedia untuk download setelah diaktifkan.');
        $confirmButton.removeClass('bg-gradient-to-r from-red-500 to-red-600')
                     .addClass('bg-gradient-to-r from-green-500 to-green-600');
        $confirmButton.text('Ya, Aktifkan');
    }
    
    openToggleModal();
}

function openToggleModal() {
    $('#toggleModal').removeClass('hidden').addClass('show');
    $('body').addClass('overflow-hidden');
}

function closeToggleModal() {
    $('#toggleModal').removeClass('show').addClass('hidden');
    $('body').removeClass('overflow-hidden');
    togglePlatformId = null;
    toggleButton = null;
}

$('#confirmToggle').on('click', function() {
    if (!togglePlatformId) return;
    
    const $button = $(this);
    $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...');
    
    $.ajax({
        url: `/platforms/${togglePlatformId}/toggle`,
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showToast('Status platform berhasil diperbarui! ✅', 'success');
                
                // Reload after short delay
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showToast(response.message || 'Gagal memperbarui status platform', 'error');
                $button.prop('disabled', false).text('Konfirmasi');
            }
        },
        error: function(xhr) {
            console.error('Toggle error:', xhr);
            const response = xhr.responseJSON;
            showToast(response?.message || 'Terjadi kesalahan saat memperbarui status', 'error');
            $button.prop('disabled', false).text('Konfirmasi');
        },
        complete: function() {
            closeToggleModal();
        }
    });
});

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

// Close modal on Escape key
$(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
        closeToggleModal();
    }
});
</script>
@endpush