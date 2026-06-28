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
        Schema::table('users', function (Blueprint $table) {
            $table->string('iam_id')->nullable()->unique()->after('id');
            $table->string('status', 20)->default('active')->after('is_active');
            $table->string('password')->nullable()->change();
        });

        // Pindahkan data dari is_active ke status
        \Illuminate\Support\Facades\DB::statement("UPDATE users SET status = 'active' WHERE is_active = 1");
        \Illuminate\Support\Facades\DB::statement("UPDATE users SET status = 'inactive' WHERE is_active = 0");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('status');
        });

        \Illuminate\Support\Facades\DB::statement("UPDATE users SET is_active = 1 WHERE status = 'active'");
        \Illuminate\Support\Facades\DB::statement("UPDATE users SET is_active = 0 WHERE status != 'active'");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['iam_id', 'status']);
            // Mengembalikan password menjadi NOT NULL (tergantung DBMS, ini bisa gagal jika ada data null, jadi dibiarkan saja)
        });
    }
};
