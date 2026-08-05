<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const COLUMNS = 'id, order_no, name, slug, client_id, project_manager_id, status, proposal_signed_date, delivery_date, production_deadline, actual_delivery_date, description, notes, created_at, updated_at, deleted_at';

    /**
     * Run the migrations.
     *
     * Converts status from an enum/CHECK-constrained column to a plain
     * string, same as item_type on inventory_items, so new workflow stages
     * (Design/Inspection are being added right now) don't need an
     * enum-altering migration every time.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            $this->rebuildSqliteTable(fn (Blueprint $table) => $table->string('status', 50)->default('draft'));
            return;
        }

        DB::statement("ALTER TABLE projects MODIFY status VARCHAR(50) NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            $this->rebuildSqliteTable(fn (Blueprint $table) => $table->enum('status', ['draft', 'confirmed', 'in_production', 'delayed', 'finished', 'delivered'])->default('draft'));
            return;
        }

        DB::statement("ALTER TABLE projects MODIFY status ENUM('draft','confirmed','in_production','delayed','finished','delivered') NOT NULL DEFAULT 'draft'");
    }

    /**
     * SQLite enforces enum()'s CHECK constraint and has no ALTER COLUMN, so
     * the table has to be rebuilt to change it.
     */
    private function rebuildSqliteTable(\Closure $statusColumn): void
    {
        Schema::rename('projects', 'projects_old');

        // SQLite indexes live in a single global namespace (not per-table),
        // and renaming a table does not rename its indexes. The old table's
        // indexes would collide with the identically-named ones the new
        // table is about to create, so drop them first (safe: this table
        // and everything on it is dropped a few lines down anyway).
        foreach (DB::select("SELECT name FROM sqlite_master WHERE type = 'index' AND tbl_name = 'projects_old' AND sql IS NOT NULL") as $index) {
            DB::statement('DROP INDEX "' . $index->name . '"');
        }

        Schema::create('projects', function (Blueprint $table) use ($statusColumn) {
            $table->id();
            $table->string('order_no', 50)->unique();
            $table->string('name', 191);
            $table->string('slug', 191)->unique();
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->foreignId('project_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $statusColumn($table);
            $table->date('proposal_signed_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->date('production_deadline')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('order_no');
            $table->index('status');
            $table->index('client_id');
            $table->index('project_manager_id');
            $table->index('delivery_date');
            $table->index('production_deadline');
            $table->index('slug');
        });

        DB::statement('INSERT INTO projects (' . self::COLUMNS . ') SELECT ' . self::COLUMNS . ' FROM projects_old');

        Schema::drop('projects_old');
    }
};
