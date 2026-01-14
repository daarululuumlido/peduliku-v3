<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        Permission::create(['name' => 'orang.view']);
        Permission::create(['name' => 'orang.create']);
        Permission::create(['name' => 'orang.edit']);
        Permission::create(['name' => 'orang.delete']);
    }

    public function test_super_admin_has_all_permissions(): void
    {
        // Create Super Admin role
        $superAdminRole = Role::create(['name' => 'Super Admin']);

        // Create user with Super Admin role
        $user = User::factory()->create();
        $user->assignRole($superAdminRole);

        // Super Admin should pass all permission checks via Gate::before
        $this->assertTrue($user->can('orang.view'));
        $this->assertTrue($user->can('orang.create'));
        $this->assertTrue($user->can('orang.edit'));
        $this->assertTrue($user->can('orang.delete'));
        $this->assertTrue($user->can('any.random.permission')); // Even non-existent ones
    }

    public function test_role_can_have_specific_permissions(): void
    {
        // Create Staff HR role with specific permissions
        $staffRole = Role::create(['name' => 'Staff HR']);
        $staffRole->givePermissionTo(['orang.view', 'orang.create']);

        // Create user with Staff HR role
        $user = User::factory()->create();
        $user->assignRole($staffRole);

        // Should have assigned permissions
        $this->assertTrue($user->can('orang.view'));
        $this->assertTrue($user->can('orang.create'));

        // Should NOT have non-assigned permissions
        $this->assertFalse($user->can('orang.edit'));
        $this->assertFalse($user->can('orang.delete'));
    }

    public function test_user_can_have_multiple_roles(): void
    {
        $viewerRole = Role::create(['name' => 'Viewer']);
        $viewerRole->givePermissionTo(['orang.view']);

        $editorRole = Role::create(['name' => 'Editor']);
        $editorRole->givePermissionTo(['orang.edit']);

        $user = User::factory()->create();
        $user->assignRole([$viewerRole, $editorRole]);

        // Should have permissions from both roles
        $this->assertTrue($user->can('orang.view'));
        $this->assertTrue($user->can('orang.edit'));

        // Should NOT have non-assigned permissions
        $this->assertFalse($user->can('orang.create'));
        $this->assertFalse($user->can('orang.delete'));
    }

    public function test_user_can_be_assigned_direct_permission(): void
    {
        $user = User::factory()->create();

        // Assign permission directly (not via role)
        $user->givePermissionTo('orang.view');

        $this->assertTrue($user->can('orang.view'));
        $this->assertFalse($user->can('orang.create'));
    }

    public function test_role_can_be_removed_from_user(): void
    {
        $role = Role::create(['name' => 'Viewer']);
        $role->givePermissionTo(['orang.view']);

        $user = User::factory()->create();
        $user->assignRole($role);

        // Should have permission
        $this->assertTrue($user->can('orang.view'));

        // Remove role
        $user->removeRole($role);

        // Refresh user to clear cached permissions
        $user = $user->fresh();

        // Should no longer have permission
        $this->assertFalse($user->can('orang.view'));
    }

    public function test_user_has_role_check(): void
    {
        $adminRole = Role::create(['name' => 'Admin']);
        $userRole = Role::create(['name' => 'User']);

        $user = User::factory()->create();
        $user->assignRole($adminRole);

        $this->assertTrue($user->hasRole('Admin'));
        $this->assertFalse($user->hasRole('User'));
    }

    public function test_sync_roles(): void
    {
        $adminRole = Role::create(['name' => 'Admin']);
        $userRole = Role::create(['name' => 'User']);
        $guestRole = Role::create(['name' => 'Guest']);

        $user = User::factory()->create();
        $user->assignRole([$adminRole, $userRole]);

        // User has Admin and User roles
        $this->assertTrue($user->hasRole('Admin'));
        $this->assertTrue($user->hasRole('User'));
        $this->assertFalse($user->hasRole('Guest'));

        // Sync to only Guest role
        $user->syncRoles([$guestRole]);

        // Refresh user
        $user = $user->fresh();

        // User should only have Guest role now
        $this->assertFalse($user->hasRole('Admin'));
        $this->assertFalse($user->hasRole('User'));
        $this->assertTrue($user->hasRole('Guest'));
    }
}
