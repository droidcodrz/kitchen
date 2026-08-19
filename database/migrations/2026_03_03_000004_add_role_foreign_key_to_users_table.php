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
        // SQLite cannot add a foreign key to an existing table at all, and
        // re-adding one that already exists is an error on MySQL. The column
        // itself is created with the users table; this only adds the
        // constraint, so skipping it leaves the schema usable either way.
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        if (!Schema::hasColumn('users', 'role_id')) {
            return;
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('role_id')
                    ->references('id')
                    ->on('roles')
                    ->restrictOnDelete();
            });
        } catch (\Throwable $e) {
            // Constraint already present - nothing to do.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
        });
    }
};
