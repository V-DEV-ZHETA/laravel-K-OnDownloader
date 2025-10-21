<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Services\DownloaderServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class DownloadController extends Controller
{
    public function index(Request $request)
    {
        $downloads = Download::with('platform')->latest()->paginate(20);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'downloads' => $downloads->items(),
                'pagination' => [
                    'current_page' => $downloads->currentPage(),
                    'last_page' => $downloads->lastPage(),
                    'per_page' => $downloads->perPage(),
                    'total' => $downloads->total()
                ]
            ]);
        }
        
        return view('downloads.index', compact('downloads'));
    }

    public function create(): View
    {
        $platforms = \App\Models\Platform::where('is_active', true)->get();
        return view('downloads.create', compact('platforms'));
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'platform' => 'nullable|string',
            'quality' => 'nullable|string',
            'format' => 'nullable|string',
            'audio_only' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $url = $request->input('url');
            $platform = $request->input('platform');
            
            // Auto-detect platform if not provided
            if (!$platform) {
                $platform = DownloaderServiceFactory::detectPlatform($url);
                if (!$platform) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unsupported platform. Please specify a platform.'
                    ], 400);
                }
            }

            $service = DownloaderServiceFactory::create($platform);
            
            $options = [
                'quality' => $request->input('quality'),
                'format' => $request->input('format'),
                'audio_only' => $request->input('audio_only', false)
            ];

            $download = $service->downloadVideo($url, $options);

            return response()->json([
                'success' => true,
                'download' => $download,
                'message' => 'Download started successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to start download: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Download $download): View
    {
        return view('downloads.show', compact('download'));
    }

    public function getVideoInfo(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'platform' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $url = $request->input('url');
            $platform = $request->input('platform');
            
            if (!$platform) {
                $platform = DownloaderServiceFactory::detectPlatform($url);
                if (!$platform) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unsupported platform'
                    ], 400);
                }
            }

            $service = DownloaderServiceFactory::create($platform);
            $info = $service->getVideoInfo($url);

            return response()->json([
                'success' => true,
                'info' => $info,
                'platform' => $platform
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get video info: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAvailableFormats(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'platform' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $url = $request->input('url');
            $platform = $request->input('platform');

            $service = DownloaderServiceFactory::create($platform);
            $formats = $service->getAvailableFormats($url);

            // Generate quality options based on available formats
            $qualityOptions = $this->generateQualityOptions($formats, $platform);

            return response()->json([
                'success' => true,
                'formats' => $formats,
                'quality_options' => $qualityOptions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get available formats: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateQualityOptions(array $formats, string $platform): array
    {
        $maxHeight = $formats['max_height'] ?? 0;
        unset($formats['max_height']); // Remove the max_height from formats array

        $qualityOptions = [];

        // Always include best and worst
        $qualityOptions['best'] = 'Kualitas Terbaik';
        $qualityOptions['worst'] = 'Kualitas Terendah';

        // Add resolutions based on max height available
        if ($maxHeight >= 2160) {
            $qualityOptions['2160p'] = '2160p 4K';
        }
        if ($maxHeight >= 1440) {
            $qualityOptions['1440p'] = '1440p QHD';
        }
        if ($maxHeight >= 1080) {
            $qualityOptions['1080p'] = '1080p FHD';
        }
        if ($maxHeight >= 720) {
            $qualityOptions['720p'] = '720p HD';
        }
        if ($maxHeight >= 480) {
            $qualityOptions['480p'] = '480p SD';
        }
        if ($maxHeight >= 360) {
            $qualityOptions['360p'] = '360p';
        }
        if ($maxHeight >= 240) {
            $qualityOptions['240p'] = '240p';
        }

        return $qualityOptions;
    }

    public function download(Download $download)
    {
        if ($download->status !== 'completed' || !file_exists($download->file_path)) {
            abort(404, 'File not found');
        }

        return response()->download($download->file_path);
    }

    public function destroy(Download $download): JsonResponse
    {
        try {
            // Delete file if exists
            if ($download->file_path && file_exists($download->file_path)) {
                unlink($download->file_path);
            }

            $download->delete();

            return response()->json([
                'success' => true,
                'message' => 'Download deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete download: ' . $e->getMessage()
            ], 500);
        }
    }

    public function thumbnailProxy(string $encoded_url)
    {
        try {
            // Decode the base64 encoded URL
            $thumbnailUrl = base64_decode($encoded_url);

            if (!$thumbnailUrl) {
                abort(404, 'Invalid thumbnail URL');
            }

            // Validate URL format
            if (!filter_var($thumbnailUrl, FILTER_VALIDATE_URL)) {
                abort(404, 'Invalid thumbnail URL');
            }

            // Fetch the thumbnail from the original URL
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => [
                        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                        'Referer: https://www.instagram.com/'
                    ],
                    'timeout' => 10
                ]
            ]);

            $imageData = @file_get_contents($thumbnailUrl, false, $context);

            if ($imageData === false) {
                abort(404, 'Thumbnail not found');
            }

            // Determine content type based on URL or default to jpeg
            $contentType = 'image/jpeg';
            $extension = pathinfo(parse_url($thumbnailUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
            if ($extension) {
                $mimeTypes = [
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'webp' => 'image/webp'
                ];
                $contentType = $mimeTypes[$extension] ?? 'image/jpeg';
            }

            // Return the image with appropriate headers
            return response($imageData)
                ->header('Content-Type', $contentType)
                ->header('Cache-Control', 'public, max-age=3600')
                ->header('Access-Control-Allow-Origin', '*');

        } catch (\Exception $e) {
            abort(404, 'Thumbnail proxy error');
        }
    }
}
