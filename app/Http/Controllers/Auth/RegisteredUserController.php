<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Settings; // Import the Settings model
use App\Models\AuditLog;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => 'required|string|lowercase|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'national_id_number' => 'required|string|max:50',
            'phone_number' => 'string|max:20',
            'email' => 'required|string|lowercase|email|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::where('email', $request->email)
                    ->where('national_id_number', $request->national_id_number)
                    ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User with provided credentials not found.']);
        }

        $user->update([
            'username' => $request->username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        $primaryKey = $user->getKeyName();
        $timestamps = ['created_at', 'updated_at'];

        foreach ($request->all() as $key => $value) {
            if ($key !== $primaryKey && !in_array($key, $timestamps) && $key !== 'password_confirmation') {
                AuditLog::create([
                    'table_name' => 'users',
                    'record_id' => $user->id,
                    'action' => 'UPDATE',
                    'new_value' => $value,
                    'changed_by' => $user->username,
                    'column_affected' => $key,
                ]);
            }
        }

        Auth::login($user);

        // Create default settings if they don't exist
        Settings::firstOrCreate(
            ['user_id' => $user->id],
            [
                'theme' => 'system',
                'screen_timeout' => 30,
                'font_style' => 'sans-serif',
                'font_size' => 16,
                'notifications_enabled' => true,
                'language' => 'en',
                'timezone' => 'UTC',
                'two_factor_auth' => false,
                'date_format' => 'Y-m-d',
                'time_format' => 'H:i',
            ]
        );

        // Role-based redirection
        switch ($user->role->role_name) {
            case 'Receptionist':
                return redirect()->intended('/reception/dashboard');
            case 'Accounting':
                return redirect()->intended('/accounting'); // Replace with your accounting route
            case 'Manager':
                return redirect()->intended('/management/dashboard');
            // Add more cases for other roles as needed
            default:
                return to_route('dashboard'); // Default to dashboard if no match
        }
    }
}
