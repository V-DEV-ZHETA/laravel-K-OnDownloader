<?php

namespace App\Services;

use App\Models\Download;
use App\Models\Platform;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

abstract class BaseDownloaderService
{
    protected Platform $platform;
    protected string $downloadPath;
    protected array $defaultSettings = [];

    public function __construct(Platform $platform)
    {
        $this->platform = $platform;
        $this->downloadPath = storage_path('app/downloads');
        $this->ensureDownloadDirectory();
    }

    abstract public function getVideoInfo(string $url): array;
    abstract public function downloadVideo(string $url, array $options = []): Download;
    abstract protected function buildCommand(string $url, array $options = []): array;

    protected function ensureDownloadDirectory(): void
    {
        if (!is_dir($this->downloadPath)) {
            mkdir($this->downloadPath, 0755, true);
        }
    }

    protected function executeCommand(array $command): array
    {
        $process = new Process($command);
        $process->setTimeout(300); // 5 minutes timeout
        
        try {
            $process->run();
            
            return [
                'success' => $process->isSuccessful(),
                'output' => $process->getOutput(),
                'error' => $process->getErrorOutput(),
                'exit_code' => $process->getExitCode()
            ];
        } catch (\Exception $e) {
            Log::error('Command execution failed', [
                'command' => implode(' ', $command),
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'output' => '',
                'error' => $e->getMessage(),
                'exit_code' => -1
            ];
        }
    }

    protected function getSetting(string $key, $default = null)
    {
        return $this->platform->getSetting($key, $default);
    }

    protected function parseVideoInfo(string $output): array
    {
        // Default implementation - can be overridden by specific platforms
        $lines = explode("\n", $output);
        $info = [];
        
        foreach ($lines as $line) {
            if (strpos($line, 'title:') !== false) {
                $info['title'] = trim(str_replace('title:', '', $line));
            } elseif (strpos($line, 'duration:') !== false) {
                $info['duration'] = trim(str_replace('duration:', '', $line));
            } elseif (strpos($line, 'thumbnail:') !== false) {
                $info['thumbnail'] = trim(str_replace('thumbnail:', '', $line));
            }
        }
        
        return $info;
    }

    protected function createDownloadRecord(string $url, array $info = []): Download
    {
        return Download::create([
            'url' => $url,
            'platform' => $this->platform->name,
            'title' => $info['title'] ?? null,
            'thumbnail' => $info['thumbnail'] ?? null,
            'duration' => $info['duration'] ?? null,
            'status' => 'pending',
            'metadata' => $info
        ]);
    }

    protected function updateDownloadStatus(Download $download, string $status, array $data = []): void
    {
        $download->update(array_merge(['status' => $status], $data));
    }

    protected function getQualityOptions(): array
    {
        return [
            'best' => 'Best Quality',
            'worst' => 'Worst Quality',
            '720p' => '720p HD',
            '480p' => '480p SD',
            '360p' => '360p',
            '240p' => '240p'
        ];
    }

    protected function getFormatOptions(): array
    {
        return [
            'mp4' => 'MP4 Video',
            'webm' => 'WebM Video',
            'mp3' => 'MP3 Audio',
            'wav' => 'WAV Audio',
            'm4a' => 'M4A Audio'
        ];
    }
}

