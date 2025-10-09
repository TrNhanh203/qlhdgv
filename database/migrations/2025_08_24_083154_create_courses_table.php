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
        Schema::create('courses', function (Blueprint $table) {
            $table->id(); // AUTO_INCREMENT
            $table->string('course_code', 100)->unique();
            $table->string('course_name', 250);
            $table->unsignedBigInteger('course_group_id')->nullable();
            $table->string('course_group', 100)->nullable();
            $table->integer('credit')->nullable();
            $table->unsignedBigInteger('education_program_id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('semester_id')->nullable();

            // thêm 2 cột quản lý người tạo & người sửa
            $table->foreignId('created_by')->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->foreignId('updated_by')->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // chỉ lưu ngày, không lưu giờ
            $table->date('created_at')->nullable();
            $table->date('updated_at')->nullable();

            $table->foreign('education_program_id')->references('id')->on('education_programs')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('set null');

            $table->index('course_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
