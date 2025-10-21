<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Download Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the media downloader.
    |
    */

    'download_path' => env('DOWNLOAD_PATH', storage_path('app/downloads')),
    
    'max_file_size' => env('MAX_DOWNLOAD_SIZE', 1073741824), // 1GB in bytes
    
    'timeout' => env('DOWNLOAD_TIMEOUT', 300), // 5 minutes
    
    'yt_dlp_path' => env('YT_DLP_PATH', 'yt-dlp'),
    
    'ffmpeg_path' => env('FFMPEG_PATH', 'ffmpeg'),
    
    'supported_platforms' => [
        'youtube' => [
            'name' => 'YouTube',
            'icon' => 'fab fa-youtube',
            'domains' => ['youtube.com', 'youtu.be'],
            'default_quality' => 'best',
            'default_format' => 'mp4',
        ],
        'tiktok' => [
            'name' => 'TikTok',
            'icon' => 'fab fa-tiktok',
            'domains' => ['tiktok.com'],
            'default_quality' => 'best',
            'default_format' => 'mp4',
        ],
        'instagram' => [
            'name' => 'Instagram',
            'icon' => 'fab fa-instagram',
            'domains' => ['instagram.com'],
            'default_quality' => 'best',
            'default_format' => 'mp4',
        ],
        'facebook' => [
            'name' => 'Facebook',
            'icon' => 'fab fa-facebook',
            'domains' => ['facebook.com', 'fb.watch'],
            'default_quality' => 'best',
            'default_format' => 'mp4',
        ],
    ],
    
    'quality_options' => [
        'best' => 'Best Quality',
        '720p' => '720p HD',
        '480p' => '480p SD',
        '360p' => '360p',
        '240p' => '240p',
        'worst' => 'Worst Quality',
    ],
    
    'format_options' => [
        'mp4' => 'MP4 Video',
        'mp3' => 'MP3 Audio',
        'wav' => 'WAV Audio',
        'm4a' => 'M4A Audio',
    ],
];

