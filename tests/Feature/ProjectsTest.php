<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_projects()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $response = $this->get('/projects');
        $response->assertStatus(200);
    }

    public function test_admin_can_view_a_project()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $project = Project::factory()->create();
        $this->actingAs($admin);
        $response = $this->get('/projects/' . $project->id);
        $response->assertStatus(200);
    }

    public function test_admin_can_create_a_project()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $response = $this->post('/projects', [
            'name' => 'Test Project',
            'created_by' => $admin->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('projects', ['name' => 'Test Project']);
    }
} 