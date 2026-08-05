<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ProjectStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Project $project,
        protected string $fromStatus,
        protected string $toStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $message = sprintf(
            'Project #%s (%s) moved from %s to %s.',
            $this->project->order_no,
            $this->project->name,
            ucfirst(str_replace('_', ' ', $this->fromStatus)),
            ucfirst(str_replace('_', ' ', $this->toStatus))
        );

        return [
            'type' => 'status_change',
            'title' => 'Status Updated: ' . $this->project->name,
            'message' => $message,
            'description' => $message,
            'project_id' => $this->project->id,
            'url' => route('projects.show', $this->project),
        ];
    }
}
