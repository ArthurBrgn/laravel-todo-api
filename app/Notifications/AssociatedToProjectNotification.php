<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Project;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class AssociatedToProjectNotification extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(private Project $project)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting("Bonjour {$notifiable->name}")
            ->subject("Projet {$this->project->name}")
            ->line("Vous avez maintenant accès au projet {$this->project->name}")
            ->salutation('À bientôt !');
    }
}
