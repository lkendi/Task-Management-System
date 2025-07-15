<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Notifications\TaskAssigned;

class Task extends Model
{
    use HasFactory;

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'assigned_to',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted()
    {
        static::created(function ($task) {
            if ($task->assigned_to) {
                $user = \App\Models\User::find($task->assigned_to);
                if ($user) {
                    $user->notify(new TaskAssigned($task));
                }
            }
        });
        static::updated(function ($task) {
            if ($task->isDirty('assigned_to') && $task->assigned_to) {
                $user = \App\Models\User::find($task->assigned_to);
                if ($user) {
                    $user->notify(new TaskAssigned($task));
                }
            }
        });
    }
}
