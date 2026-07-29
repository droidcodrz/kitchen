<?php

namespace App\Console\Commands;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckDelayedProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:check-delayed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically mark projects as delayed if delivery date has passed';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today();

        // Find projects that should be marked as delayed
        $projects = Project::whereIn('status', ['confirmed', 'in_production'])
            ->whereNotNull('delivery_date')
            ->where('delivery_date', '<', $today)
            ->get();

        $count = 0;

        foreach ($projects as $project) {
            $project->update(['status' => 'delayed']);
            $count++;
            $this->info("Project #{$project->order_no} ({$project->name}) marked as delayed");
        }

        if ($count === 0) {
            $this->info('No projects to mark as delayed.');
        } else {
            $this->info("Total projects marked as delayed: {$count}");
        }

        return Command::SUCCESS;
    }
}
