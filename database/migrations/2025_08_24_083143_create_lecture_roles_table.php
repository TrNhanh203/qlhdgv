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
        Schema::create('lecture_roles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('lecture_id')->constrained('lectures')->cascadeOnDelete();
        $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
        $table->unsignedBigInteger('faculty_id')->nullable();
        $table->unsignedBigInteger('department_id')->nullable();
        $table->unsignedBigInteger('status_id')->nullable();
        $table->timestamp('start_date')->useCurrent();
        $table->timestamp('end_date')->nullable();

        $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('cascade');
        $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');

        $table->unique(['lecture_id','role_id','faculty_id','department_id']);

        $table->timestamps(); 
    });







    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecture_roles');
    }
};
