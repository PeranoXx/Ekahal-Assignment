<?php

namespace Tests\Feature;

use App\Modules\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_signin_page_renders_successfully(): void
    {
        $response = $this->get('/signin');
        $response->assertStatus(200);
        $response->assertSee('Sign in to your workspace');
    }

    public function test_signup_page_renders_successfully(): void
    {
        $response = $this->get('/signup');
        $response->assertStatus(200);
        $response->assertSee('Create a new workspace');
    }

    public function test_user_can_signup_successfully(): void
    {
        $response = $this->post('/signup', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
        
        $this->assertAuthenticated();
    }

    public function test_user_can_signin_successfully(): void
    {
        // Create user using Eloquent
        $user = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/signin', [
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_signin_with_incorrect_password(): void
    {
        $user = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/signin', [
            'email' => 'jane@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
