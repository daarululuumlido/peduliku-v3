<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create module access permission
        $permission = Permission::firstOrCreate(['name' => 'module.admin.access']);

        // Assign to Super Admin (Super Admin already has all permissions via Gate::before)
        // But we add it explicitly for visibility
        $superAdmin = Role::findByName('Super Admin');
        if ($superAdmin && ! $superAdmin->hasPermissionTo('module.admin.access')) {
            $superAdmin->givePermissionTo($permission);
        }

        // Assign to Staff HR
        $staffHR = Role::findByName('Staff HR');
        if ($staffHR && ! $staffHR->hasPermissionTo('module.admin.access')) {
            $staffHR->givePermissionTo($permission);
        }

        // Assign to Viewer
        $viewer = Role::findByName('Viewer');
        if ($viewer && ! $viewer->hasPermissionTo('module.admin.access')) {
            $viewer->givePermissionTo($permission);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permission = Permission::findByName('module.admin.access');
        if ($permission) {
            $permission->delete();
        }
    }
};
