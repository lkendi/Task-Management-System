<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Task;

class TaskAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('You have been assigned a new task')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new task has been assigned to you:')
            ->line('Title: ' . $this->task->title)
            ->line('Description: ' . ($this->task->description ?? ''))
            ->line('Due Date: ' . ($this->task->due_date ?? 'N/A'))
            ->action('View Task', url('/my-tasks'))
            ->line('Thank you for using our application!');
    }
} 