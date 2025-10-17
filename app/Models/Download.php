<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Download extends Model
{
    protected $fillable = [
        'url',
        'platform',
        'title',
        'thumbnail',
        'duration',
        'file_path',
        'file_size',
        'status',
        'error_message',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class, 'platform', 'name');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'downloading' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
            default => 'secondary'
        };
    }

    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) return 'Unknown';
        
        $bytes = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
