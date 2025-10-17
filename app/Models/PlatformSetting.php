<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformSetting extends Model
{
    protected $fillable = [
        'platform',
        'setting_key',
        'setting_value',
        'setting_type',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class, 'platform', 'name');
    }

    public function getTypedValueAttribute()
    {
        return match($this->setting_type) {
            'boolean' => filter_var($this->setting_value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->setting_value,
            'json' => json_decode($this->setting_value, true),
            default => $this->setting_value
        };
    }
}
