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

            return response()->json([
                'success' => true,
                'formats' => $formats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get available formats: ' . $e->getMessage()
            ], 500);
        }
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
}
