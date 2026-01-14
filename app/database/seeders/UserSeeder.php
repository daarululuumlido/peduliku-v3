<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin user
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@peduliku.local',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $superAdmin->assignRole('Super Admin');

        $this->command->info('Super Admin user created: admin@peduliku.local / password');
    }
}
