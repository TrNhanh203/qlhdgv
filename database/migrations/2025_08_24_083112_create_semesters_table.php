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
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();  
            $table->string('semester_name', 50);
            $table->integer('order_number');

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->onDelete('cascade');

            // chỉ lưu ngày, không lưu giờ
            $table->date('created_at')->nullable();
            $table->date('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
