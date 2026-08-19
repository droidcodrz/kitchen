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
        if (Schema::hasTable('storage_locations')) {
            return;
        }

        Schema::create('storage_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('code', 50)->nullable()->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_locations');
    }
};
