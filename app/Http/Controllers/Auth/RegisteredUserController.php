<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            'username' => 'required|string|lowercase|max:255', //Removed username validation rule
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'national_id_number' => 'required|string|max:50',
            'phone_number' => 'string|max:20',
            'email' => 'required|string|lowercase|email|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Find the user with the provided email and national_id_number.
        $user = User::where('email', $request->email)
                    ->where('national_id_number', $request->national_id_number)
                    ->first();

        if (!$user) {
            // If the user does not exist, you might want to handle this case appropriately.
            // For example, you could return an error message or redirect to a different page.
            return back()->withErrors(['email' => 'User with provided credentials not found.']);
        }

        // Update the user's information.
        $user->update([
            'username' => $request->username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        // Log the user registration
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

        return to_route('dashboard');
    }
}
