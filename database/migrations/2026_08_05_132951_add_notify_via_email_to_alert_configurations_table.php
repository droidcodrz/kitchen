<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Guarded so the migration is safe to re-run against a database that
        // already carries this column - re-running otherwise aborts with a
        // duplicate-column error and blocks every later migration.
        if (Schema::hasColumn('alert_configurations', 'notify_via_email')) {
            return;
        }

        Schema::table('alert_configurations', function (Blueprint $table) {
            $table->boolean('notify_via_email')->default(true)->after('notify_roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alert_configurations', function (Blueprint $table) {
            $table->dropColumn('notify_via_email');
        });
    }
};
