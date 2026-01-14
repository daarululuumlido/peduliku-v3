<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index(Request $request): Response
    {
        $query = Permission::query();

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        // Group by module (first part of permission name)
        $permissions = $query->orderBy('name')->paginate(20);

        // Get grouped permissions for display
        $groupedPermissions = Permission::orderBy('name')
            ->get()
            ->groupBy(function ($permission) {
                return explode('.', $permission->name)[0];
            });

        return Inertia::render('Admin/Settings/Permissions/Index', [
            'permissions' => $permissions,
            'groupedPermissions' => $groupedPermissions,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Settings/Permissions/Create');
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name', 'regex:/^[a-z_]+\.[a-z_]+$/'],
        ], [
            'name.regex' => 'Format nama permission harus: module.action (contoh: users.create)',
        ]);

        Permission::create(['name' => $validated['name']]);

        return redirect()->route('admin.settings.permissions.index')
            ->with('message', 'Permission berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission): Response
    {
        return Inertia::render('Admin/Settings/Permissions/Edit', [
            'permission' => $permission,
        ]);
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,'.$permission->id, 'regex:/^[a-z_]+\.[a-z_]+$/'],
        ], [
            'name.regex' => 'Format nama permission harus: module.action (contoh: users.create)',
        ]);

        $permission->update(['name' => $validated['name']]);

        return redirect()->route('admin.settings.permissions.index')
            ->with('message', 'Permission berhasil diperbarui.');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Permission $permission)
    {
        // Check if permission is used by any role
        if ($permission->roles()->count() > 0) {
            return back()->withErrors([
                'delete' => 'Permission tidak dapat dihapus karena masih digunakan oleh '.$permission->roles()->count().' role.',
            ]);
        }

        $permission->delete();

        return redirect()->route('admin.settings.permissions.index')
            ->with('message', 'Permission berhasil dihapus.');
    }
}
