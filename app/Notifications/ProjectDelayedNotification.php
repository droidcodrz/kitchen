<?php

 

namespace App\Notifications;

 

use App\Models\Project;

use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Notifications\Notification;

 

class ProjectDelayedNotification extends Notification implements ShouldQueue

{

    use Queueable;

 

    public function __construct(

        protected Project $project,

        protected bool $viaEmail = false

    ) {}

 

    public function via(object $notifiable): array

    {
        if ($notifiable instanceof \Illuminate\Notifications\AnonymousNotifiable) {
    return ['mail'];
}

        return array_filter(['database', $this->viaEmail ? 'mail' : null]);

    }

 

    public function toMail(object $notifiable): MailMessage

    {

        return (new MailMessage)

            ->subject('Project Delayed: ' . $this->project->name)

            ->line($this->message())

            ->action('View Project', route('projects.show', $this->project));

    }

 

    public function toArray(object $notifiable): array

    {

        return [

            'type' => 'delayed',

            'title' => 'Project Delayed: ' . $this->project->name,

            'message' => $this->message(),

            'description' => $this->message(),

            'project_id' => $this->project->id,

            'url' => route('projects.show', $this->project),

        ];

    }

 

    private function message(): string

    {

        return sprintf(

            'Project #%s (%s) is now marked as delayed. Delivery date was %s.',

            $this->project->order_no,

            $this->project->name,

            $this->project->delivery_date?->format('M d, Y') ?? 'unset'

        );

    }

}