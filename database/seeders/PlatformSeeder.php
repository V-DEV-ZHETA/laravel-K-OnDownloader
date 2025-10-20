<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        $platforms = [
            [
                'name' => 'youtube',
                'display_name' => 'YouTube',
                'icon' => 'fab fa-youtube',
                'is_active' => true,
                'default_settings' => json_encode([
                    'quality' => 'best',
                    'format' => 'mp4',
                    'audio_only' => false,
                    'subtitles' => false,
                    'embed_metadata' => true
                ]),
            ],
            [
                'name' => 'tiktok',
                'display_name' => 'TikTok',
                'icon' => 'fab fa-tiktok',
                'is_active' => true,
                'default_settings' => json_encode([
                    'quality' => 'best',
                    'format' => 'mp4',
                    'audio_only' => false
                ]),
            ],
            [
                'name' => 'instagram',
                'display_name' => 'Instagram',
                'icon' => 'fab fa-instagram',
                'is_active' => true,
                'default_settings' => json_encode([
                    'quality' => 'best',
                    'format' => 'mp4',
                    'audio_only' => false,
                    'sessionid' => ''
                ]),
            ],
            [
                'name' => 'facebook',
                'display_name' => 'Facebook',
                'icon' => 'fab fa-facebook',
                'is_active' => true,
                'default_settings' => json_encode([
                    'quality' => 'best',
                    'format' => 'mp4',
                    'audio_only' => false
                ]),
            ],
        ];

        foreach ($platforms as $platformData) {
            Platform::updateOrCreate(
                ['name' => $platformData['name']],
                $platformData
            );
        }
    }
}
