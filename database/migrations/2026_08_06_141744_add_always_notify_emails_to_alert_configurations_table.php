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
        Schema::table('alert_configurations', function (Blueprint $table) {
            $table->json('always_notify_emails')->nullable()->after('notify_via_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alert_configurations', function (Blueprint $table) {
            $table->dropColumn('always_notify_emails');
        });
    }
};
