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
        Schema::create('universities', function (Blueprint $table) {
            $table->id(); 
            $table->string('university_name', 250);
            $table->string('university_type', 100)->nullable(); 
            $table->string('address', 500)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('website', 200)->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->string('logo', 500)->nullable(); // cột lưu logo
            $table->text('description')->nullable(); // cột mô tả
            $table->date('founded_date')->nullable(); // cột ngày thành lập
            $table->string('fanpage', 500)->nullable(); // cột link fanpage

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
        Schema::dropIfExists('universities');
    }
};
