<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRoles
{
    public function __invoke(Request $request, Closure $next, $role): Response
    {
        $roleMethod = $this->getRoleMethod($role);

        if (method_exists($this, $roleMethod)) {
            return $this->$roleMethod($request, $next);
        }

        return redirect('/')->with('error', 'Unauthorized access.');
    }

    protected function getRoleMethod($role)
    {
        return str_replace(' ', '_', strtolower($role));
    }

    protected function guest(Request $request, Closure $next): Response
    {
        if (Auth::user()->role->role_name !== 'Guest') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }

    protected function manager(Request $request, Closure $next): Response
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

    protected function reception(Request $request, Closure $next): Response
    {
        if (Auth::user()->role->role_name !== 'Receptionist') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }

    protected function accounting(Request $request, Closure $next): Response
    {
        if (Auth::user()->role->role_name !== 'Accountant') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }

    protected function restaurant_manager(Request $request, Closure $next): Response
    {
        if (Auth::user()->role->role_name !== 'Restaurant Manager') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }

    protected function housekeeping_manager(Request $request, Closure $next): Response
    {
        if (Auth::user()->role->role_name !== 'Housekeeping Manager') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }

    protected function restaurant_till(Request $request, Closure $next): Response
    {
        if (Auth::user()->role->role_name !== 'Restaurant Till Operator') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }

    protected function housekeeper(Request $request, Closure $next): Response
    {
        if (Auth::user()->role->role_name !== 'Housekeeping') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}
