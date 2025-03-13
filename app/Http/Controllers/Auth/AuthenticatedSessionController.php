<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Settings; // Import Settings model
use Illuminate\Support\Facades\Log; // Import Log facade

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Fetch user theme and store it in the session
        $user = Auth::user();
        $settings = Settings::where('user_id', $user->id)->first();
        $userTheme = $settings ? $settings->theme : 'system';
        session(['user_theme' => $userTheme]);
        Log::info('User theme on login:', ['user_id' => $user->id, 'theme' => $userTheme]);

        // Role-based redirection
        return $this->redirectBasedOnRole();
    }

    private function redirectBasedOnRole(): RedirectResponse
    {
        $user = Auth::user();

        switch ($user->role->role_name) {
            case 'Receptionist':
                return redirect()->intended('/reception/dashboard');
            case 'Accounting':
                return redirect()->intended('/accounting/dashboard');
            case 'Manager':
                return redirect()->intended('/management/dashboard');
            default:
                return redirect()->intended(route('management.dashboard', absolute: false));
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
