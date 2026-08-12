<?php

namespace Tests\Feature;

use App\Modules\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users_list(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        $response = $this->actingAs($admin)->get('/users');

        $response->assertStatus(200);
        $response->assertSee('Users Management');
    }

    public function test_standard_user_cannot_view_users_list(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        $response = $this->actingAs($user)->get('/users');

        $response->assertStatus(403);
    }

    public function test_admin_can_create_user(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'New Guy',
            'email' => 'newguy@example.com',
            'password' => 'password123',
            'role' => 'User',
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', ['email' => 'newguy@example.com']);
        
        $newGuy = User::where('email', 'newguy@example.com')->first();
        $this->assertTrue($newGuy->hasRole('User'));
    }

    public function test_standard_user_cannot_create_user(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        $response = $this->actingAs($user)->post('/users', [
            'name' => 'New Guy',
            'email' => 'newguy@example.com',
            'password' => 'password123',
            'role' => 'User',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', ['email' => 'newguy@example.com']);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        $response = $this->actingAs($admin)->delete("/users/{$admin->id}");

        $response->assertRedirect('/users');
        $response->assertSessionHas('error', 'You cannot delete your own account.');
        $this->assertDatabaseHas('users', ['email' => 'admin@example.com']);
    }

    public function test_admin_can_search_users_via_ajax(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        
        User::create([
            'name' => 'Unique SearchableName',
            'email' => 'search@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($admin)
            ->get('/users?search=Unique SearchableName', [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['html', 'pagination', 'total']);
        
        $data = $response->json();
        $this->assertStringContainsString('Unique SearchableName', $data['html']);
        $this->assertEquals(1, $data['total']);
    }

    public function test_admin_can_soft_delete_user(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        
        $userToDelete = User::create([
            'name' => 'To Delete',
            'email' => 'todelete@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($admin)->delete("/users/{$userToDelete->id}");

        $response->assertRedirect('/users');
        $this->assertSoftDeleted($userToDelete);
        
        $response = $this->actingAs($admin)->get('/users?search=To Delete');
        $response->assertSee('To Delete');
        $response->assertSee('DELETED');
    }

    public function test_admin_can_filter_users_by_status(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        
        $activeUser = User::create([
            'name' => 'Active UserFilterTest',
            'email' => 'activefilter@example.com',
            'password' => bcrypt('password123'),
        ]);

        $deletedUser = User::create([
            'name' => 'Deleted UserFilterTest',
            'email' => 'deletedfilter@example.com',
            'password' => bcrypt('password123'),
        ]);
        $deletedUser->delete();

        // 1. Filter by Active
        $response = $this->actingAs($admin)
            ->get('/users?status=active&search=UserFilterTest', [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertStringContainsString('Active UserFilterTest', $data['html']);
        $this->assertStringNotContainsString('Deleted UserFilterTest', $data['html']);

        // 2. Filter by Deleted
        $response = $this->actingAs($admin)
            ->get('/users?status=deleted&search=UserFilterTest', [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertStringNotContainsString('Active UserFilterTest', $data['html']);
        $this->assertStringContainsString('Deleted UserFilterTest', $data['html']);
    }

    public function test_admin_can_sort_users(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        // Create two test users with specific alphabetical names
        User::create(['name' => 'Alice UserSortTest', 'email' => 'alice@example.com', 'password' => bcrypt('password123')]);
        User::create(['name' => 'Zachary UserSortTest', 'email' => 'zachary@example.com', 'password' => bcrypt('password123')]);

        // 1. Sort by name asc
        $response = $this->actingAs($admin)->get('/users?search=UserSortTest&sort_by=name&sort_order=asc', [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);
        $response->assertStatus(200);
        $data = $response->json();
        // Alice should appear first in HTML
        $alicePos = strpos($data['html'], 'Alice UserSortTest');
        $zacharyPos = strpos($data['html'], 'Zachary UserSortTest');
        $this->assertTrue($alicePos < $zacharyPos);

        // 2. Sort by name desc
        $response = $this->actingAs($admin)->get('/users?search=UserSortTest&sort_by=name&sort_order=desc', [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);
        $response->assertStatus(200);
        $data = $response->json();
        // Zachary should appear first in HTML
        $alicePos = strpos($data['html'], 'Alice UserSortTest');
        $zacharyPos = strpos($data['html'], 'Zachary UserSortTest');
        $this->assertTrue($zacharyPos < $alicePos);
    }
}
