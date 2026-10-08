<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Holds settings that belong to the installation rather than to a user,
     * starting with the application name and logo shown on the login screen
     * and in the sidebar.
     *
     * Key and value rather than a column per setting: these are read by name
     * from the views, there is only ever one row per key, and the next piece
     * of branding does not need a migration to store it.
     */
    public function up(): void
    {
        if (Schema::hasTable('app_settings')) {
            return;
        }

        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
