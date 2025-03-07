<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Models\AuditLog;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('management/settings/profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Verify the user's password.
     */
    public function verifyPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        return to_route('management.settings.profile.edit')->with('success', 'Password verified successfully.');
    }

    /**
     * Update the user's profile settings.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $oldValues = $user->toArray();
        $primaryKey = $user->getKeyName();
        $timestamps = ['created_at', 'updated_at', 'email_verified_at'];

        // Update the user's profile information
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        foreach ($request->validated() as $key => $newValue) {
            if ($key !== $primaryKey && !in_array($key, $timestamps) && $oldValues[$key] !== $newValue) {
                AuditLog::create([
                    'table_name' => 'users',
                    'record_id' => $user->id,
                    'action' => 'UPDATE',
                    'old_value' => $oldValues[$key],
                    'new_value' => $newValue,
                    'changed_by' => Auth::user()->username,
                    'column_affected' => $key,
                ]);
            }
        }

        return to_route('management.settings.profile.edit')->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $oldValues = $user->toArray();
        $primaryKey = $user->getKeyName();
        $timestamps = ['created_at', 'updated_at', 'email_verified_at'];

        Auth::logout();

        $user->delete();

        foreach ($oldValues as $key => $value) {
            if ($key !== $primaryKey && !in_array($key, $timestamps)) {
                AuditLog::create([
                    'table_name' => 'users',
                    'record_id' => $user->id,
                    'action' => 'DELETE',
                    'old_value' => $value,
                    'changed_by' => Auth::user()->username,
                    'column_affected' => $key,
                ]);
            }
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
