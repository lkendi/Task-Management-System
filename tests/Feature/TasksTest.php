<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TasksTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_tasks()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $response = $this->get('/tasks');
        $response->assertStatus(200);
    }

    public function test_admin_can_view_a_task()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $task = Task::factory()->create();
        $this->actingAs($admin);
        $response = $this->get('/tasks/' . $task->id);
        $response->assertStatus(200);
    }

    public function test_admin_can_create_a_task()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $project = \App\Models\Project::factory()->create();
        $assignee = User::factory()->create();
        $response = $this->post('/tasks', [
            'title' => 'Test Task',
            'status' => 'pending',
            'priority' => 'medium',
            'created_by' => $admin->id,
            'project_id' => $project->id,
            'assigned_to' => $assignee->id,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', ['title' => 'Test Task']);
    }
} 