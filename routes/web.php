<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'admin'
            ? redirect()->route('dashboard')
            : redirect()->route('user.dashboard');
    }
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('dashboard', function () {
        $taskStats = [
            'total'       => Task::count(),
            'pending'     => Task::where('status', 'pending')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
            'completed'   => Task::where('status', 'completed')->count(),
            'overdue'     => Task::where('due_date', '<', now())->whereNotIn('status', ['completed'])->count(),
        ];
        $recentTasks = Task::with(['assignedTo', 'createdBy', 'project'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($task) {
                return [
                    'id'         => $task->id,
                    'title'      => $task->title,
                    'description'=> $task->description,
                    'status'     => $task->status,
                    'priority'   => $task->priority,
                    'due_date'   => $task->due_date?->format('Y-m-d'),
                    'project'    => $task->project ? ['id' => $task->project->id, 'name' => $task->project->name] : null,
                    'assigned_to'=> $task->assignedTo ? ['id' => $task->assignedTo->id, 'name' => $task->assignedTo->name] : null,
                    'created_by' => $task->createdBy ? ['id' => $task->createdBy->id, 'name' => $task->createdBy->name] : null,
                    'created_at' => $task->created_at?->toIso8601String(),
                    'is_overdue' => $task->due_date && $task->due_date < now() && $task->status !== 'completed',
                ];
            });
        $tasksByPriority = [
            'high'   => Task::where('priority', 'high')->count(),
            'medium' => Task::where('priority', 'medium')->count(),
            'low'    => Task::where('priority', 'low')->count(),
        ];
        return Inertia::render('admin/Dashboard', [
            'stats' => ['tasks' => $taskStats, 'priority' => $tasksByPriority],
            'recentTasks' => $recentTasks,
            'users' => User::select('id', 'name')->get()->toArray(),
        ]);
    })->name('dashboard');

    Route::resource('users', UserController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('tasks', TaskController::class);
});

Route::middleware(['auth'])->group(function () {
    Route::get('user-dashboard', function () {
        $user = auth()->user();
        $assignedTasks = Task::where('assigned_to', $user->id)->get();
        $taskStats = [
            'total'       => $assignedTasks->count(),
            'pending'     => $assignedTasks->where('status', 'pending')->count(),
            'in_progress' => $assignedTasks->where('status', 'in_progress')->count(),
            'completed'   => $assignedTasks->where('status', 'completed')->count(),
            'overdue'     => $assignedTasks->filter(fn($t) => $t->due_date && $t->due_date < now() && $t->status !== 'completed')->count(),
        ];
        $recentTasks = $assignedTasks->sortByDesc('created_at')->take(5)->map(function ($task) {
            return [
                'id'         => $task->id,
                'title'      => $task->title,
                'description'=> $task->description,
                'status'     => $task->status,
                'priority'   => $task->priority,
                'due_date'   => $task->due_date?->format('Y-m-d'),
                'project'    => $task->project ? ['id' => $task->project->id, 'name' => $task->project->name] : null,
                'assigned_to'=> $task->assignedTo ? ['id' => $task->assignedTo->id, 'name' => $task->assignedTo->name] : null,
                'created_by' => $task->createdBy ? ['id' => $task->createdBy->id, 'name' => $task->createdBy->name] : null,
                'created_at' => $task->created_at?->toIso8601String(),
                'is_overdue' => $task->due_date && $task->due_date < now() && $task->status !== 'completed',
            ];
        });
        return Inertia::render('user/Dashboard', [
            'stats' => ['tasks' => $taskStats],
            'recentTasks' => $recentTasks,
        ]);
    })->name('user.dashboard');

    Route::get('my-tasks', function () {
        $user = auth()->user();
        $tasks = Task::where('assigned_to', $user->id)->paginate(15)->through(fn($task) => [
            'id'         => $task->id,
            'title'      => $task->title,
            'description'=> $task->description,
            'status'     => $task->status,
            'priority'   => $task->priority,
            'due_date'   => $task->due_date?->format('M j, Y'),
            'assigned_to'=> $task->assignedTo?->name,
            'created_by' => $task->createdBy?->name,
            'created_at' => $task->created_at->format('M j, Y'),
            'is_overdue' => $task->due_date && $task->due_date < now() && $task->status !== 'completed',
        ]);
        return Inertia::render('user/Tasks', ['tasks' => $tasks]);
    })->name('user.tasks');
});

Route::middleware(['auth'])->patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
