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
        $user = auth()->user();
        if ($user->role === 'admin') {
            return redirect()->route('dashboard');
        } else {
            return redirect()->route('user.dashboard');
        }
    }
    return Inertia::render('Welcome');
})->name('home');

// Admin-only routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('dashboard', function () {
        $user = auth()->user();
        
        $taskStats = [
            'total' => Task::count(),
            'pending' => Task::where('status', 'pending')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
            'completed' => Task::where('status', 'completed')->count(),
            'overdue' => Task::where('due_date', '<', now())->whereNotIn('status', ['completed'])->count(),
        ];
        
        $userStats = [
            'total' => User::count(),
            'active' => User::where('email_verified_at', '!=', null)->count(),
        ];
        
        $recentTasks = Task::with(['assignedTo', 'createdBy', 'project'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'due_date' => $task->due_date?->format('Y-m-d'),
                    'project' => $task->project ? [
                        'id' => $task->project->id,
                        'name' => $task->project->name,
                    ] : null,
                    'assigned_to' => $task->assignedTo ? [
                        'id' => $task->assignedTo->id,
                        'name' => $task->assignedTo->name,
                    ] : null,
                    'created_by' => $task->createdBy ? [
                        'id' => $task->createdBy->id,
                        'name' => $task->createdBy->name,
                    ] : null,
                    'created_at' => $task->created_at?->toIso8601String(),
                    'is_overdue' => $task->due_date && $task->due_date < now() && $task->status !== 'completed',
                ];
            });
        
        $tasksByPriority = [
            'high' => Task::where('priority', 'high')->count(),
            'medium' => Task::where('priority', 'medium')->count(),
            'low' => Task::where('priority', 'low')->count(),
        ];
        
        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'tasks' => $taskStats,
                'users' => $userStats,
                'priority' => $tasksByPriority,
            ],
            'recentTasks' => $recentTasks,
        ]);
    })->name('dashboard');

    Route::resource('users', UserController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('tasks', TaskController::class);
    Route::get('users', function (Request $request) {
        $query = User::query();
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }
        
        if ($request->has('verified') && $request->verified !== '') {
            if ($request->verified === 'true') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }
        
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        
        $allowedSortFields = ['name', 'email', 'created_at', 'assigned_tasks_count', 'created_tasks_count'];
        if (in_array($sort, $allowedSortFields)) {
            if ($sort === 'assigned_tasks_count') {
                $query->withCount('assignedTasks')->orderBy('assigned_tasks_count', $direction);
            } elseif ($sort === 'created_tasks_count') {
                $query->withCount('createdTasks')->orderBy('created_tasks_count', $direction);
            } else {
                $query->orderBy($sort, $direction);
            }
        } else {
            $query->latest();
        }
        
        $users = $query
            ->paginate(15)
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at?->format('M j, Y'),
                    'created_at' => $user->created_at->format('M j, Y'),
                    'role' => $user->role,
                    'assigned_tasks_count' => $user->assignedTasks()->count(),
                    'created_tasks_count' => $user->createdTasks()->count(),
                ];
            });
        
        // Get unique roles from the users table
        $roles = User::select('role')->distinct()->pluck('role');
        
        return Inertia::render('Users', [
            'users' => $users,
            'filters' => [
                'search' => $request->search,
                'role' => $request->role,
                'verified' => $request->verified,
                'sort' => $request->sort,
                'direction' => $request->direction,
            ],
            'roles' => $roles,
        ]);
    })->name('users');
});

// Regular user dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('user-dashboard', function () {
        $user = auth()->user();
        $assignedTasks = Task::where('assigned_to', $user->id)->get();
        $completedCount = $assignedTasks->where('status', 'completed')->count();
        $pendingCount = $assignedTasks->where('status', 'pending')->count();
        $inProgressCount = $assignedTasks->where('status', 'in_progress')->count();
        return Inertia::render('user/Dashboard', [
            'assignedTasksCount' => $assignedTasks->count(),
            'completedCount' => $completedCount,
            'pendingCount' => $pendingCount,
            'inProgressCount' => $inProgressCount,
        ]);
    })->name('user.dashboard');

    // Regular user tasks page (only assigned tasks, view and update status only)
    Route::get('my-tasks', function () {
        $user = auth()->user();
        $tasks = Task::where('assigned_to', $user->id)->paginate(15);
        return Inertia::render('user/Tasks', [
            'tasks' => $tasks,
        ]);
    })->name('user.tasks');
});

Route::get('tasks', function (Request $request) {
    $query = Task::with(['assignedTo', 'createdBy']);
    
    if ($request->has('search') && $request->search) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }
    
    if ($request->has('status') && $request->status) {
        $query->where('status', $request->status);
    }
    
    if ($request->has('priority') && $request->priority) {
        $query->where('priority', $request->priority);
    }
    
    if ($request->has('assigned_to') && $request->assigned_to) {
        $query->where('assigned_to', $request->assigned_to);
    }
    
    if ($request->has('due_date') && $request->due_date) {
        switch ($request->due_date) {
            case 'overdue':
                $query->where('due_date', '<', now())->whereNotIn('status', ['completed']);
                break;
            case 'today':
                $query->whereDate('due_date', now());
                break;
            case 'week':
                $query->whereBetween('due_date', [now(), now()->addWeek()]);
                break;
            case 'month':
                $query->whereBetween('due_date', [now(), now()->addMonth()]);
                break;
        }
    }
    
    $sort = $request->get('sort', 'created_at');
    $direction = $request->get('direction', 'desc');
    
    $allowedSortFields = ['title', 'status', 'priority', 'due_date', 'created_at'];
    if (in_array($sort, $allowedSortFields)) {
        $query->orderBy($sort, $direction);
    } else {
        $query->latest();
    }
    
    $tasks = $query
        ->paginate(15)
        ->through(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'priority' => $task->priority,
                'due_date' => $task->due_date?->format('M j, Y'),
                'assigned_to' => $task->assignedTo?->name,
                'created_by' => $task->createdBy?->name,
                'created_at' => $task->created_at->format('M j, Y'),
                'is_overdue' => $task->due_date && $task->due_date < now() && $task->status !== 'completed',
            ];
        });
    
    $users = User::select('id', 'name')->get();
    
    return Inertia::render('admin/Tasks', [
        'tasks' => $tasks,
        'filters' => [
            'search' => $request->search,
            'status' => $request->status,
            'priority' => $request->priority,
            'assigned_to' => $request->assigned_to,
            'due_date' => $request->due_date,
            'sort' => $request->sort,
            'direction' => $request->direction,
        ],
        'users' => $users,
    ]);
})->middleware(['auth'])->name('tasks');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
