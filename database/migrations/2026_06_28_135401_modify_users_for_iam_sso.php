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
        if (!Schema::hasColumn('users', 'iam_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('iam_id')->nullable()->unique()->after('id');
            });
        }
        
        if (!Schema::hasColumn('users', 'status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('status', 20)->default('active')->after('is_active');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });

        if (Schema::hasColumn('users', 'is_active')) {
            // Pindahkan data dari is_active ke status
            \Illuminate\Support\Facades\DB::statement("UPDATE users SET status = 'active' WHERE is_active = 1");
            \Illuminate\Support\Facades\DB::statement("UPDATE users SET status = 'inactive' WHERE is_active = 0");

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('status');
            });

            if (Schema::hasColumn('users', 'status')) {
                \Illuminate\Support\Facades\DB::statement("UPDATE users SET is_active = 1 WHERE status = 'active'");
                \Illuminate\Support\Facades\DB::statement("UPDATE users SET is_active = 0 WHERE status != 'active'");
            }
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'iam_id')) {
                $table->dropColumn('iam_id');
            }
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
