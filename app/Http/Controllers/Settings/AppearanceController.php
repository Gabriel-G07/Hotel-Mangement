<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppearanceController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:light,dark,system',
        ]);

        $user = Auth::user();

        Settings::updateOrCreate(
            ['user_id' => $user->id],
            ['theme' => $request->theme]
        );

        return response()->json(['message' => 'Theme updated successfully']);
    }

    public function getSettings()
    {
        $user = Auth::user();
        $settings = Settings::where('user_id', $user->id)->first();

        if (!$settings) {
            $settings = Settings::create([
                'user_id' => $user->id,
                'theme' => 'system', // Default theme if no settings exist
                // Set other defaults as needed
            ]);
        }

        return response()->json($settings);
    }
}
