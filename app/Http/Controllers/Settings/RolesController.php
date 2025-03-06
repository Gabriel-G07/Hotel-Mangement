<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\RolesUpdateRequest;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class RolesController extends Controller
{
    /**
     * Show the roles settings page.
     */
    public function index(Request $request): Response
    {
        $roles = Roles::whereNotIn('role_name', ['Unassigned', 'Developer'])->get();

        return Inertia::render('settings/roles', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a new role.
     */
    public function store(RolesUpdateRequest $request): RedirectResponse
    {
        Roles::create($request->validated());

        return to_route('roles.index');
    }

    /**
     * Update an existing role.
     */
    public function update(RolesUpdateRequest $request, $role_id): RedirectResponse
    {
        $role = Roles::find($role_id);

        if (!$role) {
            return redirect()->route('roles.index')->with('error', 'Role not found.');
        }

        $role->update($request->validated());

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Delete a role.
     */
    public function destroy(Request $request, $role_id): RedirectResponse
    {
        // Validate the password
        $request->validate([
            'password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, Auth::user()->password)) {
                        $fail('The password is incorrect.');
                    }
                },
            ],
        ]);

        // Find the role by ID
        $role = Roles::find($role_id);

        if (!$role) {
            return redirect()->route('roles.index')->with('error', 'Role not found.');
        }

        // Find the "Unassigned" role
        $unassignedRole = Roles::where('role_name', 'Unassigned')->first();

        if (!$unassignedRole) {
            return redirect()->route('roles.index')->with('error', 'Unassigned role not found. Please create it first.');
        }

        // Reassign users with the deleted role to the "Unassigned" role
        User::where('role_id', $role->role_id)->update(['role_id' => $unassignedRole->role_id]);

        // Delete the role
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully. Users reassigned to the Unassigned role.');
    }
}
