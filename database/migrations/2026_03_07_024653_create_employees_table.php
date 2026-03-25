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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('full_name',150);
            $table->string('nip',50)->unique();
            $table->foreignId('employee_type_id')->constrained('employee_types');
            $table->foreignId('work_unit_id')->constrained('work_units')->nullable();
            $table->foreignId('position_id')->constrained('positions')->nullable();
            $table->string('email',150)->nullable();
            $table->string('phone',20)->nullable();
            $table->string('signature_path',500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};