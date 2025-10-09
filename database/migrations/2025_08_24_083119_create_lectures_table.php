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
        Schema::create('lectures', function (Blueprint $table) {
            $table->id();  
            $table->string('lecturer_code', 100)->nullable();
            $table->string('full_name', 250);
            $table->string('degree', 150)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('phone', 50)->nullable();

            $table->foreignId('department_id')
                  ->constrained('departments')
                  ->onDelete('cascade');

            $table->foreignId('university_id')
                  ->constrained('universities')
                  ->onDelete('cascade');

            $table->foreignId('status_id')
                  ->nullable()
                  ->constrained('status_codes')
                  ->onDelete('set null');

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
        Schema::dropIfExists('lectures');
    }
};
