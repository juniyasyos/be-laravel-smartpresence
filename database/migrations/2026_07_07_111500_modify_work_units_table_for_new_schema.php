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
        Schema::table('work_units', function (Blueprint $table) {
            // Rename work_unit to unit_name
            if (Schema::hasColumn('work_units', 'work_unit') && !Schema::hasColumn('work_units', 'unit_name')) {
                $table->renameColumn('work_unit', 'unit_name');
            }

            // Add slug
            if (!Schema::hasColumn('work_units', 'slug')) {
                $table->string('slug', 150)->nullable()->unique()->after('unit_name');
            }

            // Add description
            if (!Schema::hasColumn('work_units', 'description')) {
                $table->text('description')->nullable()->after('slug');
            }

            // Add deleted_at (soft deletes)
            if (!Schema::hasColumn('work_units', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_units', function (Blueprint $table) {
            // Revert rename
            if (Schema::hasColumn('work_units', 'unit_name') && !Schema::hasColumn('work_units', 'work_unit')) {
                $table->renameColumn('unit_name', 'work_unit');
            }

            // Drop slug
            if (Schema::hasColumn('work_units', 'slug')) {
                $table->dropColumn('slug');
            }

            // Drop description
            if (Schema::hasColumn('work_units', 'description')) {
                $table->dropColumn('description');
            }

            // Drop deleted_at
            if (Schema::hasColumn('work_units', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
