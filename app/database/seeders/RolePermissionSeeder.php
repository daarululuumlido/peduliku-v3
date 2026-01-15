<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for Phase 1 modules
        $permissions = [
            // User Management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.suspend',

            // Role Management
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Permission Management
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',

            // Orang (People) Management
            'orang.view',
            'orang.create',
            'orang.edit',
            'orang.delete',
            'orang.restore',

            // Kartu Keluarga Management
            'kartu_keluarga.view',
            'kartu_keluarga.create',
            'kartu_keluarga.edit',
            'kartu_keluarga.delete',

            // Alamat Management
            'alamat.view',
            'alamat.create',
            'alamat.edit',
            'alamat.delete',

            // Document Management
            'dokumen.view',
            'dokumen.upload',
            'dokumen.delete',

            // Audit Log
            'auditors.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Super Admin role (has all permissions via Gate::before)
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);

        // Create Staff HR role
        $staffHRRole = Role::firstOrCreate(['name' => 'Staff HR']);
        $staffHRRole->givePermissionTo([
            'orang.view', 'orang.create', 'orang.edit', 'orang.delete', 'orang.restore',
            'kartu_keluarga.view', 'kartu_keluarga.create', 'kartu_keluarga.edit', 'kartu_keluarga.delete',
            'alamat.view', 'alamat.create', 'alamat.edit', 'alamat.delete',
            'dokumen.view', 'dokumen.upload', 'dokumen.delete',
            'auditors.view',
        ]);

        // Create Viewer role (read-only)
        $viewerRole = Role::firstOrCreate(['name' => 'Viewer']);
        $viewerRole->givePermissionTo([
            'orang.view',
            'kartu_keluarga.view',
            'alamat.view',
            'dokumen.view',
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
}
