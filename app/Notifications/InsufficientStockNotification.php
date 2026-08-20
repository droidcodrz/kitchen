<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent when an action was blocked because stock could not cover it - starting
 * production, or adding materials to a project already in production. The
 * action did not happen, so this is a prompt to restock, not a record of
 * something that did.
 */
class InsufficientStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param array<int, array{name: string, sku: ?string, available: float, needed: float, unit: ?string}> $shortages
     */
    public function __construct(
        protected array $shortages,
        protected ?Project $project = null,
        protected ?string $action = null,
        protected bool $viaEmail = false
    ) {}

    public function via(object $notifiable): array
    {
        // An always_notify_emails address isn't a User, so it has nowhere to
        // store a database notification - mail only for those.
        if ($notifiable instanceof \Illuminate\Notifications\AnonymousNotifiable) {
            return ['mail'];
        }

        return array_filter(['database', $this->viaEmail ? 'mail' : null]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title())
            ->line($this->headline());

        foreach ($this->shortages as $s) {
            $mail->line(sprintf(
                '- %s%s: %s in stock, %s needed (short by %s%s)',
                $s['name'],
                !empty($s['sku']) ? ' (SKU: ' . $s['sku'] . ')' : '',
                $this->num($s['available']),
                $this->num($s['needed']),
                $this->num(max(0, $s['needed'] - $s['available'])),
                !empty($s['unit']) ? ' ' . $s['unit'] : ''
            ));
        }

        $mail->line('The action was not completed. Restock these items and try again.');

        if ($this->project) {
            $mail->action('View Project', route('projects.show', $this->project));
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'insufficient_stock',
            'title' => $this->title(),
            'message' => $this->headline(),
            'description' => $this->headline(),
            'shortages' => $this->shortages,
            'project_id' => $this->project?->id,
            'url' => $this->project ? route('projects.show', $this->project) : route('inventory.index'),
        ];
    }

    private function title(): string
    {
        return 'Insufficient Stock' . ($this->project ? ': ' . $this->project->name : '');
    }

    private function headline(): string
    {
        $what = $this->action ?: 'An action';
        $where = $this->project ? ' on project "' . $this->project->name . '"' : '';
        $items = count($this->shortages);

        return sprintf(
            '%s%s was blocked because %d inventory item%s did not have enough stock.',
            $what,
            $where,
            $items,
            $items === 1 ? '' : 's'
        );
    }

    private function num(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }
}
