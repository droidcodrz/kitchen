<?php

namespace App\Console\Commands;

use App\Services\ProjectService;
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
    protected $description = 'Automatically mark projects as delayed if delivery date has passed, and notify relevant users';

    /**
     * Execute the console command.
     */
    public function handle(ProjectService $projectService): int
    {
        $count = $projectService->checkAllDelayedProjects();

        if ($count === 0) {
            $this->info('No projects to mark as delayed.');
        } else {
            $this->info("Total projects marked as delayed: {$count}");
        }

        return Command::SUCCESS;
    }
}
