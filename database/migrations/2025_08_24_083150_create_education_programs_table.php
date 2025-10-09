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
        Schema::create('education_programs', function (Blueprint $table) {
            $table->id(); // INT AUTO_INCREMENT
            $table->string('program_code', 100)->unique();
            $table->string('program_name', 250);
            $table->unsignedBigInteger('faculty_id');
            $table->string('education_system_code', 100);
            $table->string('education_system_name', 250);

            // chỉ lưu ngày, không lưu giờ
            $table->date('created_at')->nullable();
            $table->date('updated_at')->nullable();

            $table->foreign('faculty_id')
                  ->references('id')->on('faculties')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_programs');
    }
};
