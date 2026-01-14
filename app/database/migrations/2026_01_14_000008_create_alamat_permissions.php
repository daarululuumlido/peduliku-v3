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

        $permissions = [
            'alamat.view',
            'alamat.create',
            'alamat.edit',
            'alamat.delete',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        // Assign to Super Admin (optional if Gate::before is used, but good for completeness)
        $role = Role::findByName('Super Admin');
        if ($role) {
            $role->givePermissionTo($permissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'alamat.view',
            'alamat.create',
            'alamat.edit',
            'alamat.delete',
        ];

        foreach ($permissions as $name) {
            $permission = Permission::findByName($name);
            if ($permission) {
                $permission->delete();
            }
        }
    }
};
