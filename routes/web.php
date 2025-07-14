<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    $user = auth()->user();
    
    // Get task statistics
    $taskStats = [
        'total' => Task::count(),
        'pending' => Task::where('status', 'pending')->count(),
        'in_progress' => Task::where('status', 'in_progress')->count(),
        'completed' => Task::where('status', 'completed')->count(),
        'overdue' => Task::where('due_date', '<', now())->whereNotIn('status', ['completed'])->count(),
    ];
    
    // Get user statistics
    $userStats = [
        'total' => User::count(),
        'active' => User::where('email_verified_at', '!=', null)->count(),
    ];
    
    // Get recent tasks
    $recentTasks = Task::with(['assignedTo', 'createdBy'])
        ->latest()
        ->take(5)
        ->get()
        ->map(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'status' => $task->status,
                'priority' => $task->priority,
                'due_date' => $task->due_date?->format('M j, Y'),
                'assigned_to' => $task->assignedTo?->name,
                'created_by' => $task->createdBy?->name,
            ];
        });
    
    // Get tasks by priority
    $tasksByPriority = [
        'high' => Task::where('priority', 'high')->count(),
        'medium' => Task::where('priority', 'medium')->count(),
        'low' => Task::where('priority', 'low')->count(),
    ];
    
    return Inertia::render('Dashboard', [
        'stats' => [
            'tasks' => $taskStats,
            'users' => $userStats,
            'priority' => $tasksByPriority,
        ],
        'recentTasks' => $recentTasks,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('users', function (Request $request) {
    $query = User::with(['roles', 'permissions']);
    
    // Search functionality
    if ($request->has('search') && $request->search) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }
    
    // Filter by role
    if ($request->has('role') && $request->role) {
        $query->whereHas('roles', function ($q) use ($request) {
            $q->where('name', $request->role);
        });
    }
    
    // Filter by verification status
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
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->permissions->pluck('name'),
                'assigned_tasks_count' => $user->assignedTasks()->count(),
                'created_tasks_count' => $user->createdTasks()->count(),
            ];
        });
    
    // Get available roles for filter
    $roles = \Spatie\Permission\Models\Role::pluck('name');
    
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
})->middleware(['auth', 'verified'])->name('users');

Route::get('tasks', function (Request $request) {
    $query = Task::with(['assignedTo', 'createdBy']);
    
    // Search functionality
    if ($request->has('search') && $request->search) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }
    
    // Filter by status
    if ($request->has('status') && $request->status) {
        $query->where('status', $request->status);
    }
    
    // Filter by priority
    if ($request->has('priority') && $request->priority) {
        $query->where('priority', $request->priority);
    }
    
    // Filter by assigned user
    if ($request->has('assigned_to') && $request->assigned_to) {
        $query->where('assigned_to', $request->assigned_to);
    }
    
    // Filter by due date
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
    
    // Sorting
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
    
    // Get available users for assignment filter
    $users = User::select('id', 'name')->get();
    
    return Inertia::render('Tasks', [
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
})->middleware(['auth', 'verified'])->name('tasks');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
