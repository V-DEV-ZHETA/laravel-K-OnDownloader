<?php

namespace App\Services;

use App\Models\Download;
use App\Models\Platform;

class YouTubeDownloaderService extends BaseDownloaderService
{
    protected array $defaultSettings = [
        'quality' => 'best',
        'format' => 'mp4',
        'audio_only' => false,
        'subtitles' => false,
        'embed_metadata' => true
    ];

    // Supported audio formats
    protected array $audioFormats = ['mp3', 'm4a', 'wav', 'opus', 'flac'];

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
                $filePath = $this->extractFilePath($result['output'], $options);
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
        $subtitles = $options['subtitles'] ?? $this->getSetting('subtitles', false);
        $embedMetadata = $options['embed_metadata'] ?? $this->getSetting('embed_metadata', true);

        // Auto-detect audio only mode based on format
        if (in_array(strtolower($format), $this->audioFormats)) {
            $audioOnly = true;
        }

        $command = ['yt-dlp'];

        // Output path with proper extension
        $command[] = '-o';
        if ($audioOnly) {
            $command[] = $this->downloadPath . '/%(title)s.' . $format;
        } else {
            $command[] = $this->downloadPath . '/%(title)s.%(ext)s';
        }

        // Audio download configuration
        if ($audioOnly) {
            $command[] = '--extract-audio';
            $command[] = '--audio-format';
            $command[] = $format;
            $command[] = '--audio-quality';
            $command[] = '0'; // Best audio quality
            
            // For MP3, add additional quality settings
            if (strtolower($format) === 'mp3') {
                $command[] = '--postprocessor-args';
                $command[] = 'ffmpeg:-b:a 320k'; // High quality MP3 (320kbps)
            }
            
            $command[] = '--format';
            $command[] = 'bestaudio/best';
        } else {
            // Video download configuration
            $command[] = '--format';
            $command[] = $this->getQualityFormat($quality);
        }

        // Metadata embedding
        if ($embedMetadata) {
            $command[] = '--embed-metadata';
            if (!$audioOnly) {
                $command[] = '--embed-thumbnail';
            } else {
                // For audio files, embed thumbnail as cover art
                $command[] = '--embed-thumbnail';
                $command[] = '--ppa';
                $command[] = 'EmbedThumbnail+ffmpeg_o:-c:v mjpeg -vf crop="\'if(gt(ih,iw),iw,ih)\':\'if(gt(iw,ih),ih,iw)\'"';
            }
        }

        // Subtitles - only for video downloads
        if (!$audioOnly && $subtitles) {
            $command[] = '--write-subs';
            $command[] = '--write-auto-subs';
            $command[] = '--sub-lang';
            $command[] = 'en,id'; // English and Indonesian subtitles
        }

        // Additional options
        $command[] = '--no-playlist';
        $command[] = '--ignore-errors';
        $command[] = '--no-warnings';

        $command[] = $url;

        return $command;
    }

    protected function getQualityFormat(string $quality): string
    {
        return match($quality) {
            'best' => 'bestvideo+bestaudio/best',
            '2160p' => 'bestvideo[height<=2160]+bestaudio/best[height<=2160]',
            '1440p' => 'bestvideo[height<=1440]+bestaudio/best[height<=1440]',
            '1080p' => 'bestvideo[height<=1080]+bestaudio/best[height<=1080]',
            '720p' => 'bestvideo[height<=720]+bestaudio/best[height<=720]',
            '480p' => 'bestvideo[height<=480]+bestaudio/best[height<=480]',
            '360p' => 'bestvideo[height<=360]+bestaudio/best[height<=360]',
            '240p' => 'bestvideo[height<=240]+bestaudio/best[height<=240]',
            'worst' => 'worstvideo+worstaudio/worst',
            default => 'bestvideo+bestaudio/best'
        };
    }

    protected function formatDuration(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
        
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    protected function extractFilePath(string $output, array $options = []): ?string
    {
        $lines = explode("\n", $output);
        $format = $options['format'] ?? 'mp4';
        $audioOnly = in_array(strtolower($format), $this->audioFormats);
        
        // Look for the final destination after all post-processing
        $destinations = [];
        foreach ($lines as $line) {
            if (strpos($line, '[download] Destination:') !== false) {
                $destinations[] = trim(str_replace('[download] Destination:', '', $line));
            }
            if (strpos($line, '[ExtractAudio] Destination:') !== false) {
                return trim(str_replace('[ExtractAudio] Destination:', '', $line));
            }
            if (strpos($line, '[ffmpeg] Destination:') !== false) {
                return trim(str_replace('[ffmpeg] Destination:', '', $line));
            }
        }
        
        // If audio only, look for file with correct extension
        if ($audioOnly && !empty($destinations)) {
            $lastDestination = end($destinations);
            $pathInfo = pathinfo($lastDestination);
            $expectedPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.' . $format;
            
            if (file_exists($expectedPath)) {
                return $expectedPath;
            }
        }
        
        return !empty($destinations) ? end($destinations) : null;
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

        $maxHeight = 0;
        foreach ($lines as $line) {
            if (preg_match('/^(\d+)\s+(\w+)\s+(\d+x\d+|\w+)\s+(.+)/', $line, $matches)) {
                $resolution = $matches[3];
                if (preg_match('/(\d+)x(\d+)/', $resolution, $resMatches)) {
                    $height = (int)$resMatches[2];
                    $maxHeight = max($maxHeight, $height);
                }
                $formats[] = [
                    'id' => $matches[1],
                    'extension' => $matches[2],
                    'resolution' => $resolution,
                    'note' => trim($matches[4])
                ];
            }
        }

        $formats['max_height'] = $maxHeight;

        return $formats;
    }

    /**
     * Download audio only in specified format
     */
    public function downloadAudio(string $url, string $format = 'mp3', array $options = []): Download
    {
        $options['audio_only'] = true;
        $options['format'] = $format;
        
        return $this->downloadVideo($url, $options);
    }

    /**
     * Get supported audio formats
     */
    public function getSupportedAudioFormats(): array
    {
        return $this->audioFormats;
    }
}