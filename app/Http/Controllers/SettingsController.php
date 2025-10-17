<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function index(): View
    {
        $platforms = Platform::with('settings')->get();
        return view('settings.index', compact('platforms'));
    }

    public function show(Platform $platform): View
    {
        $platform->load('settings');
        return view('settings.show', compact('platform'));
    }

    public function update(Request $request, Platform $platform): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $settings = $request->input('settings');
            
            foreach ($settings as $key => $value) {
                $platform->setSetting($key, $value);
            }

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reset(Platform $platform): JsonResponse
    {
        try {
            // Reset to default settings
            $defaultSettings = $platform->default_settings ?? [];
            
            foreach ($defaultSettings as $key => $value) {
                $platform->setSetting($key, $value);
            }

            return response()->json([
                'success' => true,
                'message' => 'Settings reset to default successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset settings: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDefaultSettings(): JsonResponse
    {
        $platforms = Platform::all();
        $defaultSettings = [];

        foreach ($platforms as $platform) {
            $defaultSettings[$platform->name] = [
                'display_name' => $platform->display_name,
                'settings' => $platform->default_settings ?? []
            ];
        }

        return response()->json([
            'success' => true,
            'default_settings' => $defaultSettings
        ]);
    }
}
