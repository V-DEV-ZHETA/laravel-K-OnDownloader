<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Platform extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'icon',
        'is_active',
        'default_settings'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_settings' => 'array'
    ];

    public function settings(): HasMany
    {
        return $this->hasMany(PlatformSetting::class, 'platform', 'name');
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class, 'platform', 'name');
    }

    public function getSetting(string $key, $default = null)
    {
        $setting = $this->settings()->where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    public function setSetting(string $key, $value, string $type = 'string', ?string $description = null)
    {
        return $this->settings()->updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'setting_type' => $type,
                'description' => $description
            ]
        );
    }
}
