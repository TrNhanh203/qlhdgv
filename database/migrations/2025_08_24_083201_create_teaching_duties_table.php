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
        Schema::create('teaching_duties', function (Blueprint $table) {
    $table->id();
    $table->string('duty_type', 20);
    $table->unsignedBigInteger('lecture_id');
    $table->unsignedBigInteger('course_id');
    $table->unsignedBigInteger('academic_year_id');
    $table->unsignedBigInteger('semester_id');
    $table->float('hours')->nullable();
    $table->string('class_group', 100)->nullable();
    $table->string('venue', 200)->nullable();
    $table->timestamp('duty_date')->nullable();
    $table->longText('notes')->nullable();
    $table->unsignedBigInteger('status_id')->nullable();
    
    // Không dùng ->after()
    $table->timestamp('start_time')->nullable();
    $table->timestamp('end_time')->nullable();

    $table->timestamps(); // tự tạo created_at và updated_at dạng timestamp
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_duties');
    }
};
