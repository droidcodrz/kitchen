<?php

 

use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Schema;

 

return new class extends Migration

{

    /**

     * Run the migrations.

     *

     * The earlier change_status_to_string_on_projects_table migration

     * rebuilt "projects" on SQLite by renaming it to "projects_old" and

     * creating a fresh "projects" table. SQLite auto-updates a child

     * table's FOREIGN KEY clause when the table it points at is renamed,

     * so project_product/project_team/project_user ended up with their

     * project_id foreign key permanently pointing at "projects_old",

     * which no longer exists - breaking every insert into them. Rebuild

     * those three pivot tables the same way, pointed at "projects".

     */

    public function up(): void

    {

        if (Schema::getConnection()->getDriverName() !== 'sqlite') {

            return;

        }

 

        $this->rebuild('project_product', function (Blueprint $table) {

            $table->id();

            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();

            $table->unsignedInteger('quantity')->default(1);

            $table->decimal('unit_price_at_time', 12, 2)->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['project_id', 'product_id']);

        });

 

        $this->rebuild('project_team', function (Blueprint $table) {

            $table->id();

            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();

            $table->timestamp('assigned_at')->nullable();

            $table->timestamps();

            $table->unique(['project_id', 'team_id']);

        });

 

        $this->rebuild('project_user', function (Blueprint $table) {

            $table->id();

            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->timestamp('assigned_at')->nullable();

            $table->timestamps();

            $table->unique(['project_id', 'user_id']);

        });

    }

 

    /**

     * Reverse the migrations.

     *

     * Not reversible to the old (broken) state on purpose - there is

     * nothing worth going back to.

     */

    public function down(): void

    {

        //

    }

 

    private function rebuild(string $table, \Closure $definition): void

    {

        $columns = implode(', ', array_map(

            fn ($col) => '"' . $col . '"',

            Schema::getColumnListing($table)

        ));

 

        Schema::rename($table, $table . '_broken');

 

        foreach (DB::select("SELECT name FROM sqlite_master WHERE type = 'index' AND tbl_name = ? AND sql IS NOT NULL", [$table . '_broken']) as $index) {

            DB::statement('DROP INDEX "' . $index->name . '"');

        }

 

        Schema::create($table, $definition);

 

        DB::statement("INSERT INTO \"{$table}\" ({$columns}) SELECT {$columns} FROM \"{$table}_broken\"");

 

        Schema::drop($table . '_broken');

    }

};