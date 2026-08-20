<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AlertConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $alerts = [
            [
                'alert_type'      => 'low_stock',
                'threshold_value' => 10,
                'is_enabled'      => true,
                'notify_roles'    => json_encode(['admin', 'project-manager']),
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'alert_type'      => 'deadline_approaching',
                'threshold_value' => 7,
                'is_enabled'      => true,
                'notify_roles'    => json_encode(['admin', 'project-manager']),
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'alert_type'      => 'project_delayed',
                'threshold_value' => 0,
                'is_enabled'      => true,
                'notify_roles'    => json_encode(['admin', 'project-manager', 'team-lead']),
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'alert_type'      => 'insufficient_stock',
                'threshold_value' => 0,
                'is_enabled'      => true,
                'notify_roles'    => json_encode(['admin', 'project-manager']),
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'alert_type'      => 'new_inventory_addition',
                'threshold_value' => 0,
                'is_enabled'      => true,
                'notify_roles'    => json_encode(['admin']),
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
        ];

        foreach ($alerts as $alert) {
            DB::table('alert_configurations')->updateOrInsert(
                ['alert_type' => $alert['alert_type']],
                $alert
            );
        }
    }
}
