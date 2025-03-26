<?php

namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;

class AccountingPaymentsController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(): Response
    {
        $users = User::with('role')->where('is_verified', true)->get()->filter(function ($user) {
            return $user->role && $user->role->role_name !== 'Developer';
        })->map(function ($user) {
            return [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'role' => $user->role ? $user->role->role_name : 'Unassigned',
                'is_verified' => $user->is_verified,
            ];
        })->values();

        return Inertia::render('management/users/users_list', ['users' => $users]);
    }

    /**
     * Display a listing of unverified users.
     */
    public function unverified(): Response
    {
        $users = User::with('role')->where('is_verified', false)->get()->filter(function ($user) {
            return $user->role && $user->role->role_name !== 'Developer';
        })->map(function ($user) {
            return [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'role' => $user->role ? $user->role->role_name : 'Unassigned',
                'is_verified' => $user->is_verified,
            ];
        })->values();

        return Inertia::render('management/users/verify_users', ['users' => $users]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): Response
    {
        $roles = Roles::whereNotIn('role_name', ['Developer', 'Guest', 'Unassigned'])->get();
        return Inertia::render('management/users/add_users', ['roles' => $roles]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'role_id' => 'required|exists:user_roles,role_id',
            'national_id_number' => 'required|string|max:50|unique:' . User::class, // Add validation
        ]);

        $username = 'user_' . Str::random(8);
        $password = Str::random(12);

        $user = User::create([
            'username' => $username,
            'password' => Hash::make($password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'national_id_number' => $request->national_id_number,
        ]);

        // Optionally, you can send an email to the user with their username and password.

        return redirect()->route('management.users.add_users')->with('success', 'User created successfully. Password: ' . $password); //Return the password for testing.
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'username' => 'required|string|lowercase|username|max:255|unique:users,username,' . $user->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'national_id_number' => 'required|string|max:50',
            'phone_number' => 'string|max:20',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,role_id',
        ]);

        $oldValues = $user->toArray();
        $primaryKey = $user->getKeyName();
        $timestamps = ['created_at', 'updated_at', 'email_verified_at'];

        $user->update($request->all());

        foreach ($request->all() as $key => $newValue) {
            if ($key !== $primaryKey && !in_array($key, $timestamps) && $oldValues[$key] !== $newValue) {
                AuditLog::create([
                    'table_name' => 'users',
                    'action' => 'UPDATE',
                    'old_value' => $oldValues[$key],
                    'new_value' => $newValue,
                    'changed_by' => Auth::user()->username,
                    'column_affected' => $key,
                ]);
            }
        }

        return redirect()->route('management.users.users_list')->with('success', 'User updated successfully.');
    }

    /**
     * Verify the specified user.
     */
    public function verify(User $user): RedirectResponse
    {
        $user->update(['is_verified' => true]);

        AuditLog::create([
            'table_name' => 'users',
            'action' => 'UPDATE',
            'old_value' => $user->first_name . ' ' . $user->last_name,
            'new_value' => 'Verifying user',
            'changed_by' => Auth::user()->username,
            'column_affected' => 'is_verified',
            'record_id' => $user->id, // Add this line to include the record_id
        ]);

        return redirect()->route('management.users.verify_users')->with('success', 'User verified successfully.');
    }

    /**
     * Deactivate the specified user.
     */
    public function deactivate(User $user): RedirectResponse
    {
        $user->update(['is_verified' => false]);

        AuditLog::create([
            'table_name' => 'users',
            'action' => 'UPDATE',
            'old_value' => $user->first_name . ' ' . $user->last_name,
            'new_value' => 'Deactivating user',
            'changed_by' => Auth::user()->username,
            'column_affected' => 'is_verified',
        ]);

        return redirect()->route('management.users.users_list')->with('success', 'User deactivated successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return Inertia::render('management/users/users_list', ['selectedUser' => $user]);
    }
}
