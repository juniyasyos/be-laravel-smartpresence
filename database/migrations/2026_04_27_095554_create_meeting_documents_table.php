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
        Schema::create('meeting_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('meetings')->cascadeOnDelete();
            $table->string('type')->nullable();
            $table->string('file_name');
            $table->string('file_path');
            $table->integer('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('director_name')->nullable();
            $table->string('director_position')->nullable();
            $table->timestamp('director_signed_at')->nullable();
            $table->string('notulis_name')->nullable();
            $table->string('notulis_position')->nullable();
            $table->timestamp('notulis_signed_at')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_documents');
    }
};
