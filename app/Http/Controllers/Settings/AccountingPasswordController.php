<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class AccountingPasswordController extends Controller
{
    /**
     * Show the user's password settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('accounting/settings/password', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();
        $oldValues = $user->toArray();
        $primaryKey = $user->getKeyName();
        $timestamps = ['created_at', 'updated_at', 'email_verified_at'];

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        foreach ($validated as $key => $newValue) {
            if ($key === 'password') {
                AuditLog::create([
                    'table_name' => 'users',
                    'record_id' => $user->id,
                    'action' => 'UPDATE',
                    'old_value' => '********', // Mask the old password
                    'new_value' => '********', // Mask the new password
                    'changed_by' => Auth::user()->username,
                    'column_affected' => $key,
                ]);
            }
        }

        return back();
    }
}
