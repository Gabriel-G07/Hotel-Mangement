<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckManagerRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedRoles = ['Developer', 'Manager'];

        if (!Auth::check() || !in_array(Auth::user()->role->role_name, $allowedRoles)) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/')->with('error', 'Unauthorized management access.');
        }

        return $next($request);
    }
}
