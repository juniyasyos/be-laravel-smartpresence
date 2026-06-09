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
        Schema::table('meetings', function (Blueprint $table) {
            $table->index('status');
            $table->index('start_time');
        });
        
        Schema::table('employees', function (Blueprint $table) {
            $table->index('nip');
            $table->index('full_name');
            $table->index('is_active');
        });

        Schema::table('meetings', function (Blueprint $table) {
            $table->index('end_time');
            $table->index('room_id');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->index(['meeting_id', 'employee_id']);
            $table->index('status');
        });

        Schema::table('meeting_participants', function (Blueprint $table) {
            $table->index(['meeting_id', 'employee_id']);
        });
        
        Schema::table('meeting_rooms', function (Blueprint $table) {
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['start_time']);
        });
        
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex(['nip']);
            $table->dropIndex(['full_name']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('meetings', function (Blueprint $table) {
            $table->dropIndex(['end_time']);
            $table->dropIndex(['room_id']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['meeting_id', 'employee_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('meeting_participants', function (Blueprint $table) {
            $table->dropIndex(['meeting_id', 'employee_id']);
        });
        
        Schema::table('meeting_rooms', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });
    }
};
