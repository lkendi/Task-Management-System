<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_users()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $response = $this->get('/users');
        $response->assertStatus(200);
    }

    public function test_admin_can_view_a_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $this->actingAs($admin);
        $response = $this->get('/users/' . $user->id);
        $response->assertStatus(200);
    }

    public function test_admin_can_create_a_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $response = $this->post('/users', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'role' => 'user',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'testuser@example.com']);
    }
} 