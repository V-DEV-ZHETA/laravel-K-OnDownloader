<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PlatformController extends Controller
{
    public function index(): View
    {
        $platforms = Platform::with('settings')->get();
        return view('platforms.index', compact('platforms'));
    }

    public function show(Platform $platform): View
    {
        $platform->load('settings');

        // Ensure default_settings is an array
        if (is_string($platform->default_settings)) {
            $platform->default_settings = json_decode($platform->default_settings, true) ?? [];
        }

        return view('platforms.show', compact('platform'));
    }

    public function toggle(Platform $platform): JsonResponse
    {
        $platform->update(['is_active' => !$platform->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Platform status updated successfully',
            'is_active' => $platform->is_active
        ]);
    }

    public function getSettings(Platform $platform): JsonResponse
    {
        $settings = $platform->settings()->where('is_active', true)->get();
        
        $formattedSettings = [];
        foreach ($settings as $setting) {
            $formattedSettings[$setting->setting_key] = [
                'value' => $setting->typed_value,
                'type' => $setting->setting_type,
                'description' => $setting->description
            ];
        }

        return response()->json([
            'success' => true,
            'settings' => $formattedSettings
        ]);
    }
}
