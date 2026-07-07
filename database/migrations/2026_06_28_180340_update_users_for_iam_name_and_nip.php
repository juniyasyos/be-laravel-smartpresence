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
        if (Schema::hasColumn('users', 'username') && !Schema::hasColumn('users', 'name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('username', 'name');
            });
        }

        if (!Schema::hasColumn('users', 'nip')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('nip', 50)->nullable()->unique()->after('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Jika kolom name ada dan username belum ada, rename name -> username
        if (
            Schema::hasColumn('users', 'name') &&
            ! Schema::hasColumn('users', 'username')
        ) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('name', 'username');
            });
        }

        // Jika kolom nip ada, hapus
        if (Schema::hasColumn('users', 'nip')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('nip');
            });
        }
    }
};