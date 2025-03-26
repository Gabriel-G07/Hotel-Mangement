<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Roles;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class DutiesController extends Controller
{
    /**
     * Show the roles settings page.
     */
    public function index(): Response
    {
        $roles = Roles::whereNotIn('role_name', ['Unassigned', 'Developer', 'Guest'])->get();

        return Inertia::render('housekeeping/management/settings/roles', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a new role.
     */
    public function store( $request): RedirectResponse
    {
        $role = Roles::create($request->validated());

        $primaryKey = $role->getKeyName();
        $timestamps = ['created_at', 'updated_at'];

        foreach ($request->validated() as $key => $value) {
            if ($key !== $primaryKey && !in_array($key, $timestamps)) {
                AuditLog::create([
                    'table_name' => 'roles',
                    'record_id' => $role->role_id,
                    'action' => 'INSERT',
                    'new_value' => $value,
                    'changed_by' => Auth::user()->username,
                    'column_affected' => $key,
                ]);
            }
        }

        return to_route('housekeeping.management.settings.roles.index');
    }

    /**
     * Update an existing role.
     */
    public function update( $request, $role_id): RedirectResponse
    {
        $role = Roles::find($role_id);

        if (!$role) {
            return redirect()->route('housekeeping.management.settings.roles.index')->with('error', 'Role not found.');
        }

        $oldValues = $role->toArray();
        $primaryKey = $role->getKeyName();
        $timestamps = ['created_at', 'updated_at'];

        $role->update($request->validated());

        foreach ($request->validated() as $key => $newValue) {
            if ($key !== $primaryKey && !in_array($key, $timestamps) && $oldValues[$key] !== $newValue) {
                AuditLog::create([
                    'table_name' => 'roles',
                    'record_id' => $role->role_id,
                    'action' => 'UPDATE',
                    'old_value' => $oldValues[$key],
                    'new_value' => $newValue,
                    'changed_by' => Auth::user()->username,
                    'column_affected' => $key,
                ]);
            }
        }

        return redirect()->route('housekeeping.management.settings.roles.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Delete a role.
     */
    public function destroy(Request $request, $role_id): RedirectResponse
    {
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

        $role = Roles::find($role_id);

        if (!$role) {
            return redirect()->route('housekeeping.management.settings.roles.index')->with('error', 'Role not found.');
        }

        $unassignedRole = Roles::where('role_name', 'Unassigned')->first();

        if (!$unassignedRole) {
            return redirect()->route('housekeeping.management.settings.roles.index')->with('error', 'Unassigned role not found. Please create it first.');
        }

        User::where('role_id', $role->role_id)->update(['role_id' => $unassignedRole->role_id]);

        $oldValues = $role->toArray();
        $primaryKey = $role->getKeyName();
        $timestamps = ['created_at', 'updated_at'];

        $role->delete();

        foreach ($oldValues as $key => $value) {
            if ($key !== $primaryKey && !in_array($key, $timestamps)) {
                AuditLog::create([
                    'table_name' => 'roles',
                    'record_id' => $role->role_id,
                    'action' => 'DELETE',
                    'old_value' => $value,
                    'changed_by' => Auth::user()->username,
                    'column_affected' => $key,
                ]);
            }
        }

        return redirect()->route('housekeeping.management.settings.roles.index')->with('success', 'Role deleted successfully. Users reassigned to the Unassigned role.');
    }
}
