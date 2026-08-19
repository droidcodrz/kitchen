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
        // Safe to re-run: a database that already has this table
        // (records lost, restored dump, partial deploy) skips it
        // instead of aborting the whole run on "table exists".
        if (Schema::hasTable('alert_configurations')) {
            return;
        }

        Schema::create('alert_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('alert_type', 100)->unique();
            $table->decimal('threshold_value', 10, 2)->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->json('notify_roles')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alert_configurations');
    }
};
