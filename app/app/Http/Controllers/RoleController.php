<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index(): Response
    {
        $roles = Role::with('permissions')
            ->withCount('users')
            ->orderBy('name')
            ->paginate(10);

        return Inertia::render('Admin/Settings/Roles/Index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): Response
    {
        $permissions = Permission::orderBy('name')->get();

        return Inertia::render('Admin/Settings/Roles/Create', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (!empty($validated['permissions'])) {
            $permissions = Permission::whereIn('id', $validated['permissions'])->get();
            $role->syncPermissions($permissions);
        }

        return redirect()->route('admin.settings.roles.index')
            ->with('message', 'Role berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role): Response
    {
        $role->load('permissions');
        $permissions = Permission::orderBy('name')->get();

        return Inertia::render('Admin/Settings/Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        // Prevent editing Super Admin role name
        if ($role->name === 'Super Admin' && $request->name !== 'Super Admin') {
            return back()->withErrors([
                'name' => 'Role Super Admin tidak dapat diubah namanya.',
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update(['name' => $validated['name']]);

        if (!empty($validated['permissions'])) {
            $permissions = Permission::whereIn('id', $validated['permissions'])->get();
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('admin.settings.roles.index')
            ->with('message', 'Role berhasil diperbarui.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deleting Super Admin role
        if ($role->name === 'Super Admin') {
            return back()->withErrors([
                'delete' => 'Role Super Admin tidak dapat dihapus.',
            ]);
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return back()->withErrors([
                'delete' => 'Role tidak dapat dihapus karena masih digunakan oleh ' . $role->users()->count() . ' user.',
            ]);
        }

        $role->delete();

        return redirect()->route('admin.settings.roles.index')
            ->with('message', 'Role berhasil dihapus.');
    }
}
