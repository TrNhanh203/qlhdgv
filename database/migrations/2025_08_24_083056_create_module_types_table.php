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
        Schema::create('module_types', function (Blueprint $table) {
        $table->id(); 
        $table->string('name', 100);
        $table->integer('menu_order')->nullable();
        $table->string('description', 300)->nullable();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_types');
    }
};
