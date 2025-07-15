<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        $user2 = User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user'
        ]);


        $project1 = Project::create([
            'name' => 'Website Redesign',
            'description' => 'Complete redesign of the company website with modern UI/UX improvements and responsive design.',
            'created_by' => $user->id,
            'start_date' => now(),
            'end_date' => now()->addDays(30)
        ]);

        $project2 = Project::create([
            'name' => 'Mobile App Development',
            'description' => 'Development of a cross-platform mobile application for iOS and Android platforms.',
            'created_by' => $user->id,
            'start_date' => now(),
            'end_date' => now()->addDays(30)
        ]);

        Task::create([
            'title' => 'Design Homepage Layout',
            'description' => 'Create wireframes and mockups for the new homepage design',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => now()->addDays(7),
            'project_id' => $project1->id,
            'assigned_to' => $user->id,
            'created_by' => $user->id
        ]);

        Task::create([
            'title' => 'Implement Navigation Menu',
            'description' => 'Build responsive navigation menu with dropdown functionality',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => now()->addDays(10),
            'project_id' => $project1->id,
            'assigned_to' => null,
            'created_by' => $user->id
        ]);

        Task::create([
            'title' => 'Optimize Page Performance',
            'description' => 'Improve page load times and optimize images and scripts',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => now()->addDays(14),
            'project_id' => $project1->id,
            'assigned_to' => null,
            'created_by' => $user->id
        ]);

        Task::create([
            'title' => 'Setup Development Environment',
            'description' => 'Configure React Native development environment and project structure',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => now()->addDays(3),
            'project_id' => $project2->id,
            'assigned_to' => null,
            'created_by' => $user->id
        ]);

        Task::create([
            'title' => 'Design App UI/UX',
            'description' => 'Create user interface designs and user experience flows',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => now()->addDays(12),
            'project_id' => $project2->id,
            'assigned_to' => null,
            'created_by' => $user->id
        ]);

        Task::create([
            'title' => 'Implement Authentication',
            'description' => 'Build user authentication system with login and registration',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => now()->addDays(20),
            'project_id' => $project2->id,
            'assigned_to' => null,
            'created_by' => $user->id
        ]);

        Task::create([
            'title' => 'API Integration',
            'description' => 'Integrate backend APIs for data fetching and user management',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => now()->addDays(25),
            'project_id' => $project2->id,
            'assigned_to' => null,
            'created_by' => $user->id
        ]);
    }
}