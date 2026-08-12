<?php

namespace Tests\Feature;

use App\Modules\Users\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_guest_is_redirected_to_signin(): void
    {
        $response = $this->get('/settings');
        $response->assertRedirect('/signin');
    }

    public function test_standard_user_cannot_access_role_permissions(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        $response = $this->actingAs($user)->get('/settings');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_role_permissions(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        $response = $this->actingAs($admin)->get('/settings');
        $response->assertStatus(200);
        $response->assertSee('Role Permissions');
        $response->assertSee('Select Role to Edit');
    }

    public function test_admin_can_create_new_role(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        $response = $this->actingAs($admin)->post('/settings/roles', [
            'name' => 'Supervisor'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', [
            'name' => 'Supervisor',
            'guard_name' => 'web'
        ]);
    }

    public function test_admin_can_create_new_permission_group_and_auto_assigns_to_admin(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        $response = $this->actingAs($admin)->post('/settings/permissions', [
            'resource_name' => 'Order'
        ]);

        $response->assertRedirect();
        
        $actions = ['view', 'create', 'update', 'delete'];
        foreach ($actions as $action) {
            $this->assertDatabaseHas('permissions', [
                'name' => 'order-' . $action,
                'guard_name' => 'web'
            ]);
        }

        // Verify that Admin role was auto-assigned these permissions
        $adminRole = Role::where('name', 'Admin')->first();
        foreach ($actions as $action) {
            $this->assertTrue($adminRole->hasPermissionTo('order-' . $action));
        }
    }

    public function test_admin_can_update_role_permissions(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $userRole = Role::where('name', 'User')->first();

        // Initially User role has no permissions in seeder
        $this->assertEquals(0, $userRole->permissions()->count());

        $response = $this->actingAs($admin)->post('/settings/update', [
            'role_id' => $userRole->id,
            'permissions' => [
                'product-view',
                'product-create'
            ]
        ]);

        $response->assertRedirect();
        
        // Refresh role and assert permissions
        $userRole = $userRole->fresh();
        $this->assertTrue($userRole->hasPermissionTo('product-view'));
        $this->assertTrue($userRole->hasPermissionTo('product-create'));
        $this->assertFalse($userRole->hasPermissionTo('product-update'));
    }

    public function test_admin_cannot_delete_admin_role(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $adminRole = Role::where('name', 'Admin')->first();

        $response = $this->actingAs($admin)->delete('/settings/roles/' . $adminRole->id);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'The default Admin role cannot be deleted.');
        $this->assertDatabaseHas('roles', ['id' => $adminRole->id]);
    }

    public function test_admin_cannot_delete_role_assigned_to_users(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $userRole = Role::where('name', 'User')->first();

        // The user 'test@example.com' has the 'User' role assigned in DatabaseSeeder
        $response = $this->actingAs($admin)->delete('/settings/roles/' . $userRole->id);

        $response->assertRedirect();
        $response->assertSessionHas('error', "Cannot delete role 'User' because it is assigned to users.");
        $this->assertDatabaseHas('roles', ['id' => $userRole->id]);
    }

    public function test_admin_can_delete_unassigned_role(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        
        // Create an unassigned role
        $role = Role::create(['name' => 'Supervisor', 'guard_name' => 'web']);

        $response = $this->actingAs($admin)->delete('/settings/roles/' . $role->id);

        $response->assertRedirect();
        $response->assertSessionHas('success', "Role 'Supervisor' deleted successfully.");
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }
}
