@extends('layouts.app')

@section('title', 'Pengaturan Platform - NekoDrop')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="glassmorphism-card rounded-2xl p-6 md:p-8 mb-8 shadow-xl">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-glass-primary mb-1">
                        Pengaturan Platform
                    </h1>
                    <p class="text-glass-secondary">
                        Kelola konfigurasi setiap platform!!
                    </p>
                </div>
            </div>
            <button onclick="resetAllSettings()"
                    class="glassmorphism-card px-6 py-3 rounded-xl font-bold text-glass-primary hover:bg-white/50 transition-all duration-300 glass-hover flex items-center gap-2">
                <i class="fas fa-undo"></i>
                <span>Reset Semua</span>
            </button>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <button onclick="enableAllPlatforms()" class="glassmorphism-card rounded-xl p-5 glass-hover text-center">
            <i class="fas fa-check-double text-3xl text-green-500 mb-2"></i>
            <div class="text-sm font-bold text-glass-primary">Aktifkan Semua</div>
        </button>
        
        <button onclick="disableAllPlatforms()" class="glassmorphism-card rounded-xl p-5 glass-hover text-center">
            <i class="fas fa-ban text-3xl text-red-500 mb-2"></i>
            <div class="text-sm font-bold text-glass-primary">Nonaktifkan Semua</div>
        </button>
        
        <button onclick="exportSettings()" class="glassmorphism-card rounded-xl p-5 glass-hover text-center">
            <i class="fas fa-download text-3xl text-blue-500 mb-2"></i>
            <div class="text-sm font-bold text-glass-primary">Ekspor Pengaturan</div>
        </button>
        
        <button onclick="importSettings()" class="glassmorphism-card rounded-xl p-5 glass-hover text-center">
            <i class="fas fa-upload text-3xl text-orange-500 mb-2"></i>
            <div class="text-sm font-bold text-glass-primary">Impor Pengaturan</div>
        </button>
    </div>

    <!-- Platform Configuration Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
        @foreach($platforms as $platform)
            <div class="glassmorphism-card rounded-2xl p-6 glass-hover transition-all duration-300">
                <!-- Platform Icon & Status -->
                <div class="relative mb-6">
                    <div class="w-20 h-20 mx-auto rounded-2xl flex items-center justify-center
                        {{ $platform->is_active 
                            ? 'bg-gradient-to-br from-green-400 to-green-500' 
                            : 'bg-gradient-to-br from-gray-300 to-gray-400' }}
                        shadow-lg transition-all duration-300">
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
                            <span>Aktif</span>
                        @else
                            <i class="fas fa-pause-circle mr-1"></i>
                            <span>Nonaktif</span>
                        @endif
                    </span>
                </div>

                <!-- Settings Count -->
                <div class="glassmorphism-card rounded-xl p-3 mb-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-glass-secondary flex items-center gap-2">
                            <i class="fas fa-cog"></i>
                            <span>Pengaturan</span>
                        </span>
                        <span class="font-bold text-glass-primary">{{ $platform->settings->count() }}</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-2">
                    {{-- <a href="{{ route('settings.show', $platform) }}"
                       class="w-full glassmorphism-button text-white font-bold py-3 px-4 rounded-xl glass-hover flex items-center justify-center gap-2">
                        <i class="fas fa-cog"></i>
                        <span>Konfigurasi</span>
                    </a> --}}

                    {{-- <button onclick="togglePlatform({{ $platform->id }})"
                            class="w-full font-bold py-3 px-4 rounded-xl glass-hover flex items-center justify-center gap-2 transition-all duration-300
                            {{ $platform->is_active 
                                ? 'bg-gradient-to-r from-red-500 to-red-600 text-white hover:from-red-600 hover:to-red-700' 
                                : 'bg-gradient-to-r from-green-500 to-green-600 text-white hover:from-green-600 hover:to-green-700' }}">
                        @if($platform->is_active)
                            <i class="fas fa-pause"></i>
                            <span>Nonaktifkan</span>
                        @else
                            <i class="fas fa-play"></i>
                            <span>Aktifkan</span>
                        @endif
                    </button> --}}
                </div>
            </div>
        @endforeach
    </div>

    <!-- Settings Overview -->
    <div class="glassmorphism-card rounded-2xl p-6 md:p-8 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-glass-primary flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                <span>Ringkasan Pengaturan</span>
            </h2>
            <button onclick="toggleOverview()" class="glassmorphism-card px-4 py-2 rounded-lg font-semibold text-glass-primary hover:bg-white/50 transition-all duration-300">
                <i id="overviewToggleIcon" class="fas fa-chevron-up"></i>
            </button>
        </div>

        <div id="settingsOverview" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($platforms as $platform)
                <div class="glassmorphism-card rounded-xl p-5 glass-hover">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center
                            {{ $platform->is_active 
                                ? 'bg-gradient-to-br from-green-400 to-green-500' 
                                : 'bg-gradient-to-br from-gray-300 to-gray-400' }}">
                            <i class="{{ $platform->icon }} text-white text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-glass-primary">
                                {{ $platform->display_name }}
                            </h3>
                            <p class="text-xs text-glass-secondary">
                                {{ $platform->settings->count() }} pengaturan terkonfigurasi
                            </p>
                        </div>
                    </div>

                    @if($platform->settings->count() > 0)
                        <div class="space-y-2">
                            @foreach($platform->settings as $setting)
                                <div class="glassmorphism-card rounded-lg p-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-semibold text-glass-primary">
                                            {{ ucfirst(str_replace('_', ' ', $setting->setting_key)) }}
                                        </span>
                                        <span class="text-sm font-bold text-glass-accent">
                                            @if($setting->setting_type === 'boolean')
                                                @if($setting->typed_value)
                                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">
                                                        <i class="fas fa-check"></i> Ya
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">
                                                        <i class="fas fa-times"></i> Tidak
                                                    </span>
                                                @endif
                                            @else
                                                {{ $setting->setting_value }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6">
                            <i class="fas fa-cog text-4xl text-glass-secondary mb-2"></i>
                            <p class="text-sm text-glass-secondary">Belum ada pengaturan</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Help Section -->
    <div class="mt-8 glassmorphism-card rounded-2xl p-6 md:p-8 bg-gradient-to-br from-orange-50 to-yellow-50">
        <div class="flex flex-col md:flex-row items-center gap-6">
            <div class="w-20 h-20 bg-gradient-to-br from-orange-400 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
                <i class="fas fa-lightbulb text-yellow-500 text-5xl"></i>
            </div>
            <div class="flex-1 text-center md:text-left">
                <h3 class="text-xl font-bold text-glass-primary mb-2">Tips Pengaturan</h3>
                <ul class="text-glass-secondary space-y-1 text-sm">
                    <li>• Aktifkan hanya platform yang Anda gunakan untuk performa optimal</li>
                    <li>• Gunakan "Reset Semua" untuk mengembalikan ke pengaturan default</li>
                    <li>• Ekspor pengaturan Anda untuk backup atau migrasi</li>
                    <li>• Periksa ringkasan pengaturan untuk memastikan konfigurasi yang benar</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Reset Confirmation Modal -->
<div id="resetModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeResetModal()"></div>
        
        <div class="glassmorphism-card rounded-2xl p-8 max-w-md w-full relative z-10 shadow-2xl">
            <div class="text-center mb-6">
                <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-glass-primary mb-2">Reset Semua Pengaturan?</h3>
                <p class="text-glass-secondary">
                    Apakah Anda yakin ingin mengembalikan semua pengaturan ke default? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            
            <div class="flex gap-3">
                <button onclick="closeResetModal()"
                        class="flex-1 glassmorphism-card px-6 py-3 rounded-xl font-bold text-glass-primary hover:bg-white/50 transition-all duration-300">
                    Batal
                </button>
                <button onclick="confirmResetAll()"
                        class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold py-3 px-6 rounded-xl glass-hover">
                    Ya, Reset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toggle Platform Modal -->
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
/* Modal animations */
#resetModal.show,
#toggleModal.show {
    display: flex !important;
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Overview toggle animation */
#settingsOverview {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

#settingsOverview.collapsed {
    max-height: 0;
    opacity: 0;
    overflow: hidden;
}
</style>
@endpush

@push('scripts')
<script>
let togglePlatformId = null;

function toggleOverview() {
    const overview = $('#settingsOverview');
    const icon = $('#overviewToggleIcon');
    
    overview.toggleClass('collapsed');
    
    if (overview.hasClass('collapsed')) {
        icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
    } else {
        icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
    }
}

function togglePlatform(platformId) {
    togglePlatformId = platformId;
    
    // Find platform button to determine current status
    const $button = $(`button[onclick="togglePlatform(${platformId})"]`);
    const isActive = $button.find('.fa-pause').length > 0;
    
    // Set modal content
    const $modalIcon = $('#modalIcon');
    const $modalTitle = $('#modalTitle');
    const $modalMessage = $('#modalMessage');
    const $confirmButton = $('#confirmToggle');
    
    if (isActive) {
        $modalIcon.removeClass('bg-gradient-to-br from-green-400 to-green-500')
                  .addClass('bg-gradient-to-br from-red-400 to-red-500');
        $modalIcon.find('i').removeClass().addClass('fas fa-pause-circle text-white text-3xl');
        $modalTitle.text('Nonaktifkan Platform?');
        $modalMessage.text('Platform ini tidak akan tersedia untuk download setelah dinonaktifkan.');
        $confirmButton.removeClass('bg-gradient-to-r from-green-500 to-green-600')
                     .addClass('bg-gradient-to-r from-red-500 to-red-600');
        $confirmButton.text('Ya, Nonaktifkan');
    } else {
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
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(response.message || 'Gagal memperbarui status platform', 'error');
                $button.prop('disabled', false).text('Konfirmasi');
            }
        },
        error: function(xhr) {
            console.error('Toggle error:', xhr);
            const response = xhr.responseJSON;
            showToast(response?.message || 'Terjadi kesalahan', 'error');
            $button.prop('disabled', false).text('Konfirmasi');
        },
        complete: function() {
            closeToggleModal();
        }
    });
});

function resetAllSettings() {
    openResetModal();
}

function openResetModal() {
    $('#resetModal').removeClass('hidden').addClass('show');
    $('body').addClass('overflow-hidden');
}

function closeResetModal() {
    $('#resetModal').removeClass('show').addClass('hidden');
    $('body').removeClass('overflow-hidden');
}

function confirmResetAll() {
    const $button = $('#resetModal button:last-child');
    $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Mereset...');
    
    $.ajax({
        url: '{{ route("settings.defaults") }}',
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                const platforms = Object.keys(response.default_settings);
                let completed = 0;
                let failed = 0;

                platforms.forEach(function(platformName) {
                    $.ajax({
                        url: `/settings/${platformName}/reset`,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function() {
                            completed++;
                            checkResetComplete();
                        },
                        error: function() {
                            completed++;
                            failed++;
                            checkResetComplete();
                        }
                    });
                });

                function checkResetComplete() {
                    if (completed === platforms.length) {
                        if (failed === 0) {
                            showToast('Semua pengaturan berhasil direset! 🔄', 'success');
                        } else {
                            showToast(`${platforms.length - failed} dari ${platforms.length} pengaturan berhasil direset`, 'warning');
                        }
                        setTimeout(() => location.reload(), 1500);
                    }
                }
            } else {
                showToast('Gagal mendapatkan pengaturan default', 'error');
                $button.prop('disabled', false).text('Ya, Reset');
            }
        },
        error: function() {
            showToast('Terjadi kesalahan saat mereset pengaturan', 'error');
            $button.prop('disabled', false).text('Ya, Reset');
        },
        complete: function() {
            closeResetModal();
        }
    });
}

function enableAllPlatforms() {
    if (confirm('Aktifkan semua platform?')) {
        showToast('Mengaktifkan semua platform... ⚡', 'info');
        // Implementation here
    }
}

function disableAllPlatforms() {
    if (confirm('Nonaktifkan semua platform?')) {
        showToast('Menonaktifkan semua platform... ⚡', 'info');
        // Implementation here
    }
}

function exportSettings() {
    showToast('Mengekspor pengaturan... 📥', 'info');
    // Implementation here
}

function importSettings() {
    showToast('Fitur impor akan segera hadir! 🚀', 'info');
    // Implementation here
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

    setTimeout(() => removeToast(toast), 5000);
}

function removeToast(toast) {
    toast.css({
        opacity: '0',
        transform: 'translateX(100%)',
        transition: 'all 0.3s ease-out'
    });
    setTimeout(() => toast.remove(), 300);
}

// Close modals on Escape key
$(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
        closeToggleModal();
        closeResetModal();
    }
});
</script>
@endpush