<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckReceptionistRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->role->role_name !== 'Receptionist') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}
