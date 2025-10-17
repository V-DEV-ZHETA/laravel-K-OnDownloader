<?php

namespace App\Services;

use App\Models\Platform;
use InvalidArgumentException;

class DownloaderServiceFactory
{
    public static function create(string $platformName): BaseDownloaderService
    {
        $platform = Platform::where('name', $platformName)->first();
        
        if (!$platform) {
            throw new InvalidArgumentException("Platform '{$platformName}' not found");
        }

        return match($platformName) {
            'youtube' => new YouTubeDownloaderService($platform),
            'tiktok' => new TikTokDownloaderService($platform),
            'instagram' => new InstagramDownloaderService($platform),
            'facebook' => new FacebookDownloaderService($platform),
            default => throw new InvalidArgumentException("Unsupported platform: {$platformName}")
        };
    }

    public static function getSupportedPlatforms(): array
    {
        return [
            'youtube' => 'YouTube',
            'tiktok' => 'TikTok',
            'instagram' => 'Instagram',
            'facebook' => 'Facebook'
        ];
    }

    public static function detectPlatform(string $url): ?string
    {
        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            return 'youtube';
        }
        
        if (strpos($url, 'tiktok.com') !== false) {
            return 'tiktok';
        }
        
        if (strpos($url, 'instagram.com') !== false) {
            return 'instagram';
        }
        
        if (strpos($url, 'facebook.com') !== false || strpos($url, 'fb.watch') !== false) {
            return 'facebook';
        }
        
        return null;
    }
}

