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
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id(); 
            $table->string('year_code', 20);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->foreignId('university_id')
                  ->constrained('universities')
                  ->onDelete('cascade');

            // chỉ lưu ngày, không lưu giờ
            $table->date('created_at')->nullable();
            $table->date('updated_at')->nullable();

            $table->unique(['year_code', 'university_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
