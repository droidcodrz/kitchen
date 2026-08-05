<?php

namespace App\Console\Commands;

use App\Models\AlertConfiguration;
use App\Models\Project;
use App\Notifications\ProjectDeadlineApproachingNotification;
use App\Services\AlertNotifier;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckProjectDeadlines extends Command
{
    protected $signature = 'projects:check-deadlines';

    protected $description = 'Notify relevant users about projects with an approaching delivery date or production deadline';

    public function handle(AlertNotifier $alertNotifier): int
    {
        $config = AlertConfiguration::where('alert_type', 'deadline_approaching')
            ->where('is_enabled', true)
            ->first();

        if (!$config) {
            $this->info('Deadline approaching alerts are disabled.');
            return Command::SUCCESS;
        }

        $days = (int) ($config->threshold_value ?? config('manufacturing.alerts.deadline_warning_days', 7));
        $windowEnd = Carbon::today()->addDays($days);

        $notified = 0;

        $notified += $this->checkDateField($alertNotifier, 'delivery_date', 'delivery date', $windowEnd);
        $notified += $this->checkDateField($alertNotifier, 'production_deadline', 'production deadline', $windowEnd);

        if ($notified === 0) {
            $this->info('No approaching deadlines to notify.');
        } else {
            $this->info("Sent {$notified} deadline notification(s).");
        }

        return Command::SUCCESS;
    }

    private function checkDateField(AlertNotifier $alertNotifier, string $field, string $label, Carbon $windowEnd): int
    {
        $projects = Project::whereNotNull($field)
            ->where($field, '>=', Carbon::today())
            ->where($field, '<=', $windowEnd)
            ->whereNotIn('status', ['delivered', 'finished', 'delayed'])
            ->get();

        $sent = 0;

        foreach ($projects as $project) {
            $daysRemaining = Carbon::today()->diffInDays($project->{$field}, false);

            $sentCount = $alertNotifier->notify(
                'deadline_approaching',
                fn (bool $viaEmail) => new ProjectDeadlineApproachingNotification($project, $label, (int) $daysRemaining, $viaEmail),
                fn ($user) => $user->unreadNotifications()
                    ->where('type', ProjectDeadlineApproachingNotification::class)
                    ->whereJsonContains('data->project_id', $project->id)
                    ->exists()
            );

            $sent += $sentCount;
        }

        return $sent;
    }
}
