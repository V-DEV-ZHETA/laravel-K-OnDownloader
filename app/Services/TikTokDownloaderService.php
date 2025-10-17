<?php

namespace App\Services;

use App\Models\Download;
use App\Models\Platform;

class TikTokDownloaderService extends BaseDownloaderService
{
    protected array $defaultSettings = [
        'quality' => 'best',
        'format' => 'mp4',
        'audio_only' => false,
        'watermark' => false
    ];

    public function getVideoInfo(string $url): array
    {
        $command = [
            'yt-dlp',
            '--dump-json',
            '--no-download',
            $url
        ];

        $result = $this->executeCommand($command);
        
        if (!$result['success']) {
            throw new \Exception('Failed to get video info: ' . $result['error']);
        }

        $info = json_decode($result['output'], true);
        
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

    public function downloadVideo(string $url, array $options = []): Download
    {
        $download = $this->createDownloadRecord($url);
        
        try {
            // Get video info first
            $info = $this->getVideoInfo($url);
            $download->update([
                'title' => $info['title'],
                'thumbnail' => $info['thumbnail'],
                'duration' => $info['duration'],
                'metadata' => $info
            ]);

            $this->updateDownloadStatus($download, 'downloading');

            // Build download command
            $command = $this->buildCommand($url, $options);
            $result = $this->executeCommand($command);

            if ($result['success']) {
                $filePath = $this->extractFilePath($result['output']);
                $fileSize = file_exists($filePath) ? filesize($filePath) : null;
                
                $this->updateDownloadStatus($download, 'completed', [
                    'file_path' => $filePath,
                    'file_size' => $fileSize
                ]);
            } else {
                $this->updateDownloadStatus($download, 'failed', [
                    'error_message' => $result['error']
                ]);
            }

        } catch (\Exception $e) {
            $this->updateDownloadStatus($download, 'failed', [
                'error_message' => $e->getMessage()
            ]);
        }

        return $download;
    }

    protected function buildCommand(string $url, array $options = []): array
    {
        $quality = $options['quality'] ?? $this->getSetting('quality', 'best');
        $format = $options['format'] ?? $this->getSetting('format', 'mp4');
        $audioOnly = $options['audio_only'] ?? $this->getSetting('audio_only', false);
        $watermark = $options['watermark'] ?? $this->getSetting('watermark', false);

        $command = ['yt-dlp'];
        
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
        
        // Watermark handling - remove this option as it's not supported in newer yt-dlp versions
        // if (!$watermark) {
        //     $command[] = '--no-watermark';
        // }
        
        // Additional options
        $command[] = '--no-playlist';
        $command[] = '--ignore-errors';
        
        $command[] = $url;
        
        return $command;
    }

    protected function getQualityFormat(string $quality): string
    {
        return match($quality) {
            'best' => 'best',
            'worst' => 'worst',
            default => 'best'
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

