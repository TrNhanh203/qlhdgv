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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();
            
            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->cascadeOnDelete();
            
            $table->foreignId('semester_id')
                ->constrained('semesters')
                ->cascadeOnDelete();
            
            $table->string('exam_name', 250)->nullable();
            $table->string('exam_type', 50)->nullable();
            $table->string('exam_batch', 50)->nullable();
            $table->timestamp('exam_start')->useCurrent();
            $table->timestamp('exam_end')->useCurrent(); 
            $table->string('exam_form', 100)->nullable();
            
            $table->foreignId('room_id')
                ->nullable()
                ->constrained('rooms')
                ->nullOnDelete();
            
            $table->integer('expected_students')->nullable();
            $table->longText('notes')->nullable();

            // 🔹 thêm cột status_id, liên kết với bảng status_codes
            $table->foreignId('status_id')
                ->nullable()
                ->constrained('status_codes')
                ->nullOnDelete();

            $table->index('exam_start');
            $table->index(['academic_year_id', 'semester_id']);
            $table->index('room_id');
            $table->index('status_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
