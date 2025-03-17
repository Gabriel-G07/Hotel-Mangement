<?php

namespace App\Http\Controllers\reception;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ReceptionUsersController extends Controller
{
    public function index()
    {
        $users = User::whereHas('role', function ($query) {
            $query->where('role_name', 'Guest');
        })->get(['id', 'first_name', 'last_name', 'national_id_number', 'phone_number', 'email', 'address']);
        return response()->json($users);
    }

    public function suggestions(Request $request)
    {
        $query = $request->query('query');
        $users = User::whereHas('role', function ($query) {
            $query->where('role_name', 'Guest');
        })->where(function ($query) use ($request) {
            $query->where('national_id_number', 'like', "%{$request->query('query')}%")
                ->orWhere('phone_number', 'like', "%{$request->query('query')}%");
        })->get(['id', 'first_name', 'last_name', 'national_id_number', 'phone_number', 'email', 'address']);
        return response()->json($users);
    }
}
