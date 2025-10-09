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
        Schema::create('workloads', function (Blueprint $table) {
        $table->id();
        $table->foreignId('lecture_id')->constrained('lectures')->cascadeOnDelete();
        $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
        $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
        $table->float('teaching_hours')->default(0);
        $table->float('exam_proctoring_hours')->default(0);
        $table->float('other_duty_hours')->default(0);
        $table->float('standard_hours')->default(0);
        $table->longText('notes')->nullable();
        $table->timestamps();

        $table->unique(['lecture_id','academic_year_id','semester_id']);
    });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workloads');
    }
};
