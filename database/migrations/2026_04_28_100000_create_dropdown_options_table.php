<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Safe to re-run: a database that already has this table
        // (records lost, restored dump, partial deploy) skips it
        // instead of aborting the whole run on "table exists".
        if (Schema::hasTable('dropdown_options')) {
            return;
        }

        Schema::create('dropdown_options', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->string('value', 191);
            $table->string('label', 191);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['type', 'value']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dropdown_options');
    }
};
