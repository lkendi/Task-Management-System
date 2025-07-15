<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search', ''),
            'status' => $request->input('status', ''),
            'priority' => $request->input('priority', ''),
            'assigned_to' => $request->input('assigned_to', ''),
            'due_date' => $request->input('due_date', ''),
            'sort' => $request->input('sort', 'created_at'),
            'project' => $request->input('project', ''),
            'direction' => $request->input('direction', 'desc'),
        ];

        $sortable = ['title', 'status', 'priority', 'due_date', 'created_at'];
        $sort = in_array($filters['sort'], $sortable) ? $filters['sort'] : 'created_at';
        $direction = in_array(strtolower($filters['direction']), ['asc', 'desc']) ? strtolower($filters['direction']) : 'desc';

        $tasks = Task::with(['assignedTo', 'createdBy', 'project'])
            ->when($filters['search'], function ($query, $search) {
                $query->where('title', 'like', "%$search%");
            })
            ->when($filters['status'], function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filters['priority'], function ($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when($filters['assigned_to'], function ($query, $assignedTo) {
                $query->where('assigned_to', $assignedTo);
            })
            ->when($filters['due_date'], function ($query, $dueDate) {
                if ($dueDate === 'overdue') {
                    $query->where('due_date', '<', now());
                } elseif ($dueDate === 'today') {
                    $query->whereDate('due_date', now());
                } elseif ($dueDate === 'week') {
                    $query->whereBetween('due_date', [now(), now()->addWeek()]);
                } elseif ($dueDate === 'month') {
                    $query->whereBetween('due_date', [now(), now()->addMonth()]);
                }
            })
            ->when($filters['project'], function ($query, $project) {
                $query->where('project_id', $project);
            })
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        $statuses = ['pending', 'in_progress', 'completed'];
        $priorities = ['high', 'medium', 'low'];
        $users = User::select('id', 'name')->get();
        $projects = Project::select('id', 'name')->get();

        return Inertia::render('admin/Tasks', [
            'tasks' => $tasks,
            'filters' => $filters,
            'statuses' => $statuses,
            'priorities' => $priorities,
            'users' => $users,
            'projects' => $projects,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $projects = Project::all();
        
        return Inertia::render('admin/Tasks/Create', [
            'users' => $users,
            'projects' => $projects,
            'statuses' => ['pending', 'in_progress', 'completed'],
            'priorities' => ['high', 'medium', 'low'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'priority' => 'required|in:high,medium,low',
            'due_date' => 'nullable|date',
            'project_id' => 'nullable|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $validated['created_by'] = auth()->id();
        
        Task::create($validated);
        
        return redirect()->route('tasks.index')->with('success', 'Task created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::with(['assignedTo', 'createdBy', 'project'])->findOrFail($id);
        
        return Inertia::render('admin/Tasks/Show', [
            'task' => $task,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $task = Task::with(['assignedTo', 'createdBy', 'project'])->findOrFail($id);
        $users = User::all();
        $projects = Project::all();
        
        return Inertia::render('admin/Tasks/Edit', [
            'task' => $task,
            'users' => $users,
            'projects' => $projects,
            'statuses' => ['pending', 'in_progress', 'completed'],
            'priorities' => ['high', 'medium', 'low'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:pending,in_progress,completed',
            'priority' => 'required|in:high,medium,low',
            'due_date' => 'nullable|date',
            'project_id' => 'nullable|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $task->update($validated);
        
        return redirect()->route('tasks.index')->with('success', 'Task updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully');
    }
}
