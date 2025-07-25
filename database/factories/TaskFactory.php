<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => $this->faker->date(),
            'project_id' => Project::factory(),
            'assigned_to' => User::factory(),
            'created_by' => User::factory(),
        ];
    }
} 