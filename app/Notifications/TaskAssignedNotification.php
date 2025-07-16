<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class TaskAssignedNotification extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(private Task $task)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $notifiable): MailMessage
    {
        $subject = "{$this->task->project->name} - Tâche {$this->task->number}";

        return (new MailMessage)
            ->greeting("Bonjour {$notifiable->name}")
            ->subject($subject)
            ->line("La tâche \"{$this->task->name}\" vous a été assignée")
            ->salutation('À bientôt !');
    }
}
