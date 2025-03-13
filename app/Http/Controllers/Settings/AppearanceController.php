<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class AppearanceController extends Controller
{
    public function update(Request $request)
    {
        try {
            $user = Auth::user();
            $settings = Settings::where('user_id', $user->id)->first();

            $oldTheme = $settings->theme;

            $settings->update($request->all());

            AuditLog::create([
                'table_name' => 'user_settings',
                'record_id' => $settings->setting_id,
                'action' => 'UPDATE',
                'old_value' => json_encode($oldTheme),
                'new_value' => json_encode($request->all()),
                'changed_by' => Auth::user()->username,
                'column_affected' => 'theme',
            ]);

            return response()->json(['message' => 'Theme updated successfully']);
        } catch (\Throwable $e) {
            Log::error('Error updating theme:', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to update theme'], 500);
        }
    }

    public function getSettings()
    {
        $user = Auth::user();
        $settings = Settings::where('user_id', $user->id)->first();

        if (!$settings) {
            $settings = Settings::create([
                'user_id' => $user->id,
                'theme' => 'system',
            ]);
        }

        return response()->json($settings);
    }

    public function renderAppearancePage()
    {
        $user = Auth::user();
        $settings = Settings::where('user_id', $user->id)->first();
        $userTheme = $settings ? $settings->theme : null;

        return Inertia::render('reception/settings/appearance', ['user_theme' => $userTheme]);
    }
}
