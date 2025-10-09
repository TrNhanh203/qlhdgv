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
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->unsignedBigInteger('role_id');
            $table->string('module', 100);
            $table->string('action', 50);
            $table->boolean('is_allowed')->default(true);
            $table->string('notes', 300)->nullable();
            $table->timestamps();

            $table->unique(['role_id', 'module', 'action']);
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
