<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Make user foreign keys nullable with SET NULL on delete.
     * This ensures related data (meetings, assignments, documents)
     * remains intact when a user is deleted.
     */
    public function up(): void
    {
        // meetings.created_by → nullable, SET NULL
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });
        Schema::table('meetings', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->change();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        // meeting_assignments: drop FK on meeting_id first (it depends on the unique index),
        // then drop the unique index, then re-add FK on meeting_id
        Schema::table('meeting_assignments', function (Blueprint $table) {
            $table->dropForeign(['meeting_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['assigned_by']);
        });
        Schema::table('meeting_assignments', function (Blueprint $table) {
            $table->dropUnique(['meeting_id', 'user_id']);
        });
        Schema::table('meeting_assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->unsignedBigInteger('assigned_by')->nullable()->change();
            $table->foreign('meeting_id')->references('id')->on('meetings');
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('assigned_by')->references('id')->on('users')->nullOnDelete();
        });

        // attendances.verified_by → already nullable, just update FK to SET NULL
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
        });
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('verified_by')->references('id')->on('users')->nullOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });
        Schema::table('meetings', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable(false)->change();
            $table->foreign('created_by')->references('id')->on('users');
        });

        Schema::table('meeting_assignments', function (Blueprint $table) {
            $table->dropForeign(['meeting_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['assigned_by']);
        });
        Schema::table('meeting_assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->unsignedBigInteger('assigned_by')->nullable(false)->change();
            $table->foreign('meeting_id')->references('id')->on('meetings');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('assigned_by')->references('id')->on('users');
            $table->unique(['meeting_id', 'user_id']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
        });
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('verified_by')->references('id')->on('users');
        });
    }
};
