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
        Schema::create('exam_lectures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_proctoring_id')
                  ->constrained('exam_proctorings')
                  ->cascadeOnDelete();
            $table->foreignId('lecture_id')
                  ->constrained('lectures')
                  ->cascadeOnDelete();
            $table->string('assignment_type', 20);

            // chỉ lưu ngày, không lưu giờ
            $table->date('created_at')->nullable();
            $table->date('updated_at')->nullable();

            // Tên index ngắn gọn để tránh lỗi MySQL
            $table->unique(
                ['exam_proctoring_id', 'lecture_id', 'assignment_type'],
                'exam_lectures_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_lectures');
    }
};
