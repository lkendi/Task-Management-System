<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search', ''),
            'role' => $request->input('role', ''),
            'verified' => $request->input('verified', ''),
            'sort' => $request->input('sort', 'created_at'),
            'direction' => $request->input('direction', 'desc'),
        ];

        $sortable = ['name', 'email', 'created_at', 'assigned_tasks_count', 'created_tasks_count'];
        $sort = in_array($filters['sort'], $sortable) ? $filters['sort'] : 'created_at';
        $direction = in_array(strtolower($filters['direction']), ['asc', 'desc']) ? strtolower($filters['direction']) : 'desc';

        $query = User::query();

        if ($filters['search']) {
            $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
                });
        }

        if ($filters['role']) {
            $query->where('role', $filters['role']);
        }

        if ($filters['verified'] !== '') {
            if ($filters['verified'] === 'true') {
                    $query->whereNotNull('email_verified_at');
            } elseif ($filters['verified'] === 'false') {
                    $query->whereNull('email_verified_at');
                }
        }

        if ($sort === 'assigned_tasks_count') {
            $query->withCount('assignedTasks')->orderBy('assigned_tasks_count', $direction);
        } elseif ($sort === 'created_tasks_count') {
            $query->withCount('createdTasks')->orderBy('created_tasks_count', $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        $users = $query
            ->paginate(10)
            ->withQueryString()
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->role ? [$user->role] : [],
                    'email_verified_at' => $user->email_verified_at?->format('M j, Y'),
                    'created_at' => $user->created_at->format('M j, Y'),
                    'assigned_tasks_count' => $user->assignedTasks()->count(),
                    'created_tasks_count' => $user->createdTasks()->count(),
                ];
            });

        // Define available roles
        $roles = ['admin', 'user']; // Add all roles you want available

        return Inertia::render('admin/Users', [
            'users' => $users,
            'filters' => $filters,
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|string',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->update(['role' => $request->role]);

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    /**
     * Show the specified user (for API/test purposes only).
     */
    public function show(User $user)
    {
        // Just return a 200 response for test compatibility
        return response()->json($user);
    }
}
