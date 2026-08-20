<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Registers the alert type used when an action is blocked by short stock.
     * Seeded here as well as in the seeder so existing installations pick it
     * up on deploy without being re-seeded.
     */
    public function up(): void
    {
        if (!Schema::hasTable('alert_configurations')) {
            return;
        }

        $exists = DB::table('alert_configurations')
            ->where('alert_type', 'insufficient_stock')
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('alert_configurations')->insert([
            'alert_type' => 'insufficient_stock',
            'threshold_value' => 0,
            'is_enabled' => true,
            'notify_roles' => json_encode(['admin', 'project-manager']),
            'notify_via_email' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (Schema::hasTable('alert_configurations')) {
            DB::table('alert_configurations')->where('alert_type', 'insufficient_stock')->delete();
        }
    }
};
