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
        Schema::create('exam_proctorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')
                  ->constrained('exams')
                  ->cascadeOnDelete();

            $table->foreignId('lecture_id')
                  ->constrained('lectures')
                  ->cascadeOnDelete();

            $table->string('assignment_type', 20);
            $table->integer('proctor_order')->nullable();

            $table->foreignId('status_id')->nullable()
                  ->constrained('status_codes')
                  ->nullOnDelete();

            // chỉ lưu ngày, không lưu giờ
            $table->date('created_at')->nullable();
            $table->date('updated_at')->nullable();

            $table->index('exam_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_proctorings');
    }
};
