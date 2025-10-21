<?php

namespace App\Services;

use App\Models\Download;
use App\Models\Platform;

class InstagramDownloaderService extends BaseDownloaderService
{
    protected array $defaultSettings = [
        'quality' => 'best',
        'format' => 'mp4',
        'audio_only' => false,
        'stories' => false
    ];

    public function getVideoInfo(string $url): array
    {
        // Try direct HTML scraping first (most reliable)
        $info = $this->getVideoInfoFromHtml($url);
        if ($info) {
            return $info;
        }

        // Fallback to yt-dlp approaches
        $sessionid = $this->getSetting('sessionid', '');

        $commands = [];
        if (!empty($sessionid)) {
            // Primary approach with sessionid
            $commands[] = [
                'yt-dlp',
                '--dump-json',
                '--no-download',
                '--user-agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                '--referer', 'https://www.instagram.com/',
                '--no-check-certificate',
                '--extractor-args', 'instagram:sessionid=' . $sessionid,
                $url
            ];
        }

        // Fallback with cookies
        $commands[] = [
            'yt-dlp',
            '--dump-json',
            '--no-download',
            '--cookies', base_path('cookies.txt'),
            '--user-agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            '--referer', 'https://www.instagram.com/',
            '--no-check-certificate',
            $url
        ];

        // Fallback with browser cookies
        $commands[] = [
            'yt-dlp',
            '--dump-json',
            '--no-download',
            '--cookies-from-browser', 'chrome',
            '--user-agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            '--referer', 'https://www.instagram.com/',
            '--no-check-certificate',
            $url
        ];

        // Basic fallback
        $commands[] = [
            'yt-dlp',
            '--dump-json',
            '--no-download',
            '--user-agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            '--referer', 'https://www.instagram.com/',
            '--no-check-certificate',
            $url
        ];

        $lastError = null;
        foreach ($commands as $command) {
            $result = $this->executeCommand($command);

            if ($result['success']) {
                $info = json_decode($result['output'], true);
                if ($info && isset($info['title'])) {
                    return [
                        'title' => $info['title'] ?? 'Unknown',
                        'duration' => $this->formatDuration($info['duration'] ?? 0),
                        'thumbnail' => $info['thumbnail'] ?? null,
                        'description' => $info['description'] ?? null,
                        'uploader' => $info['uploader'] ?? null,
                        'view_count' => $info['view_count'] ?? null,
                        'upload_date' => $info['upload_date'] ?? null,
                        'formats' => $info['formats'] ?? []
                    ];
                }
            }
            $lastError = $result['error'];
        }

        // If all methods fail, try to extract basic info from URL
        return $this->getBasicInfoFromUrl($url);
    }

    protected function getVideoInfoFromHtml(string $url): ?array
    {
        // Extract post ID from URL
        $postId = null;
        if (preg_match('/\/p\/([A-Za-z0-9_-]+)/', $url, $matches)) {
            $postId = $matches[1];
        } elseif (preg_match('/\/reel\/([A-Za-z0-9_-]+)/', $url, $matches)) {
            $postId = $matches[1];
        }

        if (!$postId) {
            return null;
        }

        // Use curl to fetch the HTML content
        $command = [
            'curl',
            '-s',
            '-A', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            '--referer', 'https://www.instagram.com/',
            '--cookie-jar', base_path('cookies.txt'),
            $url
        ];

        $result = $this->executeCommand($command);

        if ($result['success'] && $result['output']) {
            $html = $result['output'];

            // Extract sharedData JSON
            if (preg_match('/window\._sharedData = ({.*?});<\/script>/s', $html, $matches)) {
                $sharedData = json_decode($matches[1], true);
                if ($sharedData && isset($sharedData['entry_data']['PostPage'][0]['graphql']['shortcode_media'])) {
                    $media = $sharedData['entry_data']['PostPage'][0]['graphql']['shortcode_media'];

                    $videoUrl = $media['video_url'] ?? null;
                    $displayUrl = $media['display_url'] ?? null;
                    $description = $media['edge_media_to_caption']['edges'][0]['node']['text'] ?? null;
                    $username = $media['owner']['username'] ?? 'Instagram User';
                    $viewCount = $media['video_view_count'] ?? null;
                    $timestamp = $media['taken_at_timestamp'] ?? null;
                    $isVideo = $media['is_video'] ?? false;

                    return [
                        'title' => $description ? substr($description, 0, 100) : 'Instagram Post ' . $postId,
                        'duration' => $isVideo ? $this->formatDuration($media['video_duration'] ?? 0) : '00:00',
                        'thumbnail' => $displayUrl,
                        'description' => $description,
                        'uploader' => $username,
                        'view_count' => $viewCount,
                        'upload_date' => $timestamp ? date('Y-m-d', $timestamp) : null,
                        'direct_url' => $videoUrl,
                        'formats' => $videoUrl ? [['url' => $videoUrl, 'format' => 'mp4']] : []
                    ];
                }
            }

            // Fallback to additional data if sharedData not found
            if (preg_match('/window\.__additionalDataLoaded\([^,]+,({.*?})\);<\/script>/s', $html, $matches)) {
                $additionalData = json_decode($matches[1], true);
                if ($additionalData && isset($additionalData['shortcode_media'])) {
                    $media = $additionalData['shortcode_media'];

                    $videoUrl = $media['video_url'] ?? null;
                    $displayUrl = $media['display_url'] ?? null;
                    $description = $media['edge_media_to_caption']['edges'][0]['node']['text'] ?? null;
                    $username = $media['owner']['username'] ?? 'Instagram User';
                    $viewCount = $media['video_view_count'] ?? null;
                    $timestamp = $media['taken_at_timestamp'] ?? null;
                    $isVideo = $media['is_video'] ?? false;

                    return [
                        'title' => $description ? substr($description, 0, 100) : 'Instagram Post ' . $postId,
                        'duration' => $isVideo ? $this->formatDuration($media['video_duration'] ?? 0) : '00:00',
                        'thumbnail' => $displayUrl,
                        'description' => $description,
                        'uploader' => $username,
                        'view_count' => $viewCount,
                        'upload_date' => $timestamp ? date('Y-m-d', $timestamp) : null,
                        'direct_url' => $videoUrl,
                        'formats' => $videoUrl ? [['url' => $videoUrl, 'format' => 'mp4']] : []
                    ];
                }
            }
        }

        return null;
    }

    protected function getBasicInfoFromUrl(string $url): array
    {
        // Extract post ID from URL
        $postId = null;
        if (preg_match('/\/p\/([A-Za-z0-9_-]+)/', $url, $matches)) {
            $postId = $matches[1];
        } elseif (preg_match('/\/reel\/([A-Za-z0-9_-]+)/', $url, $matches)) {
            $postId = $matches[1];
        }

        return [
            'title' => 'Instagram Post ' . ($postId ?: 'Unknown'),
            'duration' => '00:00',
            'thumbnail' => null,
            'description' => null,
            'uploader' => 'Instagram User',
            'view_count' => null,
            'upload_date' => null,
            'formats' => []
        ];
    }

    public function downloadVideo(string $url, array $options = []): Download
    {
        $download = $this->createDownloadRecord($url);

        try {
            // Get video info first
            $info = $this->getVideoInfo($url);

            // Download thumbnail locally for Instagram
            $localThumbnail = null;
            if ($info['thumbnail']) {
                $localThumbnail = $this->downloadThumbnailLocally($info['thumbnail'], $url);
            }

            $download->update([
                'title' => $info['title'],
                'thumbnail' => $localThumbnail ?: $info['thumbnail'],
                'duration' => $info['duration'],
                'metadata' => $info
            ]);

            $this->updateDownloadStatus($download, 'downloading');

            // Try multiple download approaches
            $success = false;
            $lastError = null;

            $downloadCommands = [
                // Primary with cookies
                $this->buildCommand($url, $options),
                // Fallback with browser cookies
                $this->buildFallbackCommand($url, $options, 'browser'),
                // Basic fallback
                $this->buildFallbackCommand($url, $options, 'basic')
            ];

            // Check if we have direct URL from info extraction
            if (isset($info['direct_url']) && $info['direct_url']) {
                $filePath = $this->downloadDirectUrl($info['direct_url'], $url, $options);
                if ($filePath && file_exists($filePath)) {
                    $fileSize = filesize($filePath);
                    $this->updateDownloadStatus($download, 'completed', [
                        'file_path' => $filePath,
                        'file_size' => $fileSize
                    ]);
                    $success = true;
                }
            }

            if (!$success) {
                // Try gallery-dl first
                $filePath = $this->downloadWithGalleryDl($url, $options);
                if ($filePath && file_exists($filePath)) {
                    $fileSize = filesize($filePath);
                    $this->updateDownloadStatus($download, 'completed', [
                        'file_path' => $filePath,
                        'file_size' => $fileSize
                    ]);
                    $success = true;
                } else {
                    // Fallback to yt-dlp approaches
                    foreach ($downloadCommands as $command) {
                        $result = $this->executeCommand($command);

                        if ($result['success']) {
                            $filePath = $this->extractFilePath($result['output']);
                            if ($filePath && file_exists($filePath)) {
                                $fileSize = filesize($filePath);
                                $this->updateDownloadStatus($download, 'completed', [
                                    'file_path' => $filePath,
                                    'file_size' => $fileSize
                                ]);
                                $success = true;
                                break;
                            }
                        }
                        $lastError = $result['error'];
                    }
                }
            }

            if (!$success) {
                $this->updateDownloadStatus($download, 'failed', [
                    'error_message' => $lastError ?: 'All download methods failed'
                ]);
            }

        } catch (\Exception $e) {
            $this->updateDownloadStatus($download, 'failed', [
                'error_message' => $e->getMessage()
            ]);
        }

        return $download;
    }

    protected function buildFallbackCommand(string $url, array $options = [], string $type = 'browser'): array
    {
        $quality = $options['quality'] ?? $this->getSetting('quality', 'best');
        $format = $options['format'] ?? $this->getSetting('format', 'mp4');
        $audioOnly = $options['audio_only'] ?? $this->getSetting('audio_only', false);
        $stories = $options['stories'] ?? $this->getSetting('stories', false);

        $command = ['yt-dlp'];

        if ($type === 'browser') {
            $command[] = '--cookies-from-browser';
            $command[] = 'chrome';
        } elseif ($type === 'basic' && file_exists(base_path('cookies.txt'))) {
            $command[] = '--cookies';
            $command[] = base_path('cookies.txt');
        }

        // User agent and headers
        $command[] = '--user-agent';
        $command[] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
        $command[] = '--referer';
        $command[] = 'https://www.instagram.com/';
        $command[] = '--no-check-certificate';

        // Instagram specific extractor args
        $command[] = '--extractor-args';
        $command[] = 'instagram:sessionid=your_session_id_here';

        // Output path
        $command[] = '-o';
        $command[] = $this->downloadPath . '/%(title)s.%(ext)s';

        // Quality selection
        if ($audioOnly) {
            $command[] = '--extract-audio';
            $command[] = '--audio-format';
            $command[] = $format;
        } else {
            $command[] = '--format';
            $command[] = $this->getQualityFormat($quality);
        }

        // Stories handling
        if ($stories) {
            $command[] = '--include-posts';
        }

        // Additional options
        $command[] = '--no-playlist';
        $command[] = '--ignore-errors';

        $command[] = $url;

        return $command;
    }

    protected function downloadWithGalleryDl(string $url, array $options = []): ?string
    {
        // Extract post ID from URL
        $postId = null;
        if (preg_match('/\/p\/([A-Za-z0-9_-]+)/', $url, $matches)) {
            $postId = $matches[1];
        } elseif (preg_match('/\/reel\/([A-Za-z0-9_-]+)/', $url, $matches)) {
            $postId = $matches[1];
        }

        if (!$postId) {
            return null;
        }

        // Create download directory
        $downloadDir = $this->downloadPath . '/instagram_' . $postId;
        if (!is_dir($downloadDir)) {
            mkdir($downloadDir, 0755, true);
        }

        // Gallery-dl command to download the post
        $command = [
            'gallery-dl',
            '--destination', $downloadDir,
            '--filename', '{id}_{num}.{extension}',
            $url
        ];

        $result = $this->executeCommand($command);

        if ($result['success']) {
            // Find the downloaded file
            $files = glob($downloadDir . '/*');
            foreach ($files as $file) {
                if (is_file($file) && (strpos($file, '.mp4') !== false || strpos($file, '.jpg') !== false || strpos($file, '.jpeg') !== false)) {
                    return $file;
                }
            }
        }

        return null;
    }

    protected function downloadDirectUrl(string $directUrl, string $originalUrl, array $options = []): ?string
    {
        // Extract post ID from original URL
        $postId = null;
        if (preg_match('/\/p\/([A-Za-z0-9_-]+)/', $originalUrl, $matches)) {
            $postId = $matches[1];
        } elseif (preg_match('/\/reel\/([A-Za-z0-9_-]+)/', $originalUrl, $matches)) {
            $postId = $matches[1];
        }

        if (!$postId) {
            $postId = 'unknown';
        }

        // Generate filename
        $extension = strpos($directUrl, '.mp4') !== false ? 'mp4' : 'jpg';
        $fileName = 'instagram_' . $postId . '.' . $extension;
        $filePath = $this->downloadPath . '/' . $fileName;

        // Use curl to download the direct URL
        $command = [
            'curl',
            '-s',
            '-A', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            '--referer', 'https://www.instagram.com/',
            '--cookie', base_path('cookies.txt'),
            '-o', $filePath,
            $directUrl
        ];

        $result = $this->executeCommand($command);

        if ($result['success'] && file_exists($filePath) && filesize($filePath) > 0) {
            return $filePath;
        }

        return null;
    }

    protected function buildCommand(string $url, array $options = []): array
    {
        $quality = $options['quality'] ?? $this->getSetting('quality', 'best');
        $format = $options['format'] ?? $this->getSetting('format', 'mp4');
        $audioOnly = $options['audio_only'] ?? $this->getSetting('audio_only', false);
        $stories = $options['stories'] ?? $this->getSetting('stories', false);

        $command = ['yt-dlp'];

        // Authentication - use cookies if available
        $cookiesFile = base_path('cookies.txt');
        if (file_exists($cookiesFile)) {
            $command[] = '--cookies';
            $command[] = $cookiesFile;
        }

        // User agent to mimic browser
        $command[] = '--user-agent';
        $command[] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

        // Referer
        $command[] = '--referer';
        $command[] = 'https://www.instagram.com/';

        // Output path
        $command[] = '-o';
        $command[] = $this->downloadPath . '/%(title)s.%(ext)s';

        // Quality selection
        if ($audioOnly) {
            $command[] = '--extract-audio';
            $command[] = '--audio-format';
            $command[] = $format;
        } else {
            $command[] = '--format';
            $command[] = $this->getQualityFormat($quality);
        }

        // Stories handling
        if ($stories) {
            $command[] = '--include-posts';
        }

        // Additional options
        $command[] = '--no-playlist';
        $command[] = '--ignore-errors';
        $command[] = '--no-check-certificate';

        $command[] = $url;

        return $command;
    }

    protected function getQualityFormat(string $quality): string
    {
        return match($quality) {
            'best' => 'b', // Use 'b' instead of 'best' to avoid warning
            'worst' => 'worst',
            default => 'b'
        };
    }

    protected function formatDuration(int $seconds): string
    {
        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;
        
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    protected function extractFilePath(string $output): ?string
    {
        $lines = explode("\n", $output);
        foreach ($lines as $line) {
            if (strpos($line, '[download] Destination:') !== false) {
                return trim(str_replace('[download] Destination:', '', $line));
            }
        }
        return null;
    }

    public function getAvailableFormats(string $url): array
    {
        $command = [
            'yt-dlp',
            '--list-formats',
            $url
        ];

        $result = $this->executeCommand($command);
        
        if (!$result['success']) {
            return [];
        }

        $formats = [];
        $lines = explode("\n", $result['output']);
        
        foreach ($lines as $line) {
            if (preg_match('/^(\d+)\s+(\w+)\s+(\d+x\d+|\w+)\s+(.+)/', $line, $matches)) {
                $formats[] = [
                    'id' => $matches[1],
                    'extension' => $matches[2],
                    'resolution' => $matches[3],
                    'note' => trim($matches[4])
                ];
            }
        }
        
        return $formats;
    }
}

