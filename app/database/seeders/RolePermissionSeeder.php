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

            // Phase 2: HRIS Module
            // Struktur Organisasi
            'hris.struktur_organisasi.view',
            'hris.struktur_organisasi.create',
            'hris.struktur_organisasi.edit',
            'hris.struktur_organisasi.delete',
            'hris.struktur_organisasi.clone',

            // Unit Organisasi
            'hris.unit_organisasi.view',
            'hris.unit_organisasi.create',
            'hris.unit_organisasi.edit',
            'hris.unit_organisasi.delete',
            'hris.unit_organisasi.reorder',

            // Master Jabatan
            'hris.jabatan.view',
            'hris.jabatan.create',
            'hris.jabatan.edit',
            'hris.jabatan.delete',

            // Pegawai Management
            'hris.pegawai.view',
            'hris.pegawai.create',
            'hris.pegawai.edit',
            'hris.pegawai.delete',
            'hris.pegawai.activate',
            'hris.pegawai.assign_jabatan',

            // Riwayat Pendidikan
            'hris.pendidikan.view',
            'hris.pendidikan.create',
            'hris.pendidikan.edit',
            'hris.pendidikan.delete',

            // Riwayat Keluarga
            'hris.keluarga.view',
            'hris.keluarga.create',
            'hris.keluarga.edit',
            'hris.keluarga.delete',

            // Riwayat Ibadah
            'hris.ibadah.view',
            'hris.ibadah.create',
            'hris.ibadah.edit',
            'hris.ibadah.delete',

            // Catatan Kepegawaian
            'hris.catatan.view',
            'hris.catatan.create',
            'hris.catatan.edit',
            'hris.catatan.delete',

            // Dokumen Pegawai
            'hris.dokumen_pegawai.view',
            'hris.dokumen_pegawai.create',
            'hris.dokumen_pegawai.edit',
            'hris.dokumen_pegawai.delete',
            'hris.dokumen_pegawai.verify',
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
            // HRIS Permissions
            'hris.struktur_organisasi.view', 'hris.struktur_organisasi.create', 'hris.struktur_organisasi.edit', 'hris.struktur_organisasi.clone',
            'hris.unit_organisasi.view', 'hris.unit_organisasi.create', 'hris.unit_organisasi.edit', 'hris.unit_organisasi.reorder',
            'hris.jabatan.view', 'hris.jabatan.create', 'hris.jabatan.edit',
            'hris.pegawai.view', 'hris.pegawai.create', 'hris.pegawai.edit', 'hris.pegawai.activate', 'hris.pegawai.assign_jabatan',
            'hris.pendidikan.view', 'hris.pendidikan.create', 'hris.pendidikan.edit', 'hris.pendidikan.delete',
            'hris.keluarga.view', 'hris.keluarga.create', 'hris.keluarga.edit', 'hris.keluarga.delete',
            'hris.ibadah.view', 'hris.ibadah.create', 'hris.ibadah.edit', 'hris.ibadah.delete',
            'hris.catatan.view', 'hris.catatan.create', 'hris.catatan.edit', 'hris.catatan.delete',
            'hris.dokumen_pegawai.view', 'hris.dokumen_pegawai.create', 'hris.dokumen_pegawai.edit', 'hris.dokumen_pegawai.delete', 'hris.dokumen_pegawai.verify',
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
