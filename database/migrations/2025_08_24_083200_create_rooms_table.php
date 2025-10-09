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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id(); 
            $table->string('name', 100);
            $table->string('type', 50)->nullable(); 
            $table->string('category', 20)->default('hoc'); 
            $table->integer('capacity')->nullable();
            $table->string('location', 200)->nullable();
            $table->unsignedBigInteger('university_id');
            $table->unsignedBigInteger('status_id')->nullable();

            // chỉ lưu ngày, không lưu giờ
            $table->date('created_at')->nullable();
            $table->date('updated_at')->nullable();

            $table->foreign('university_id')->references('id')->on('universities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
