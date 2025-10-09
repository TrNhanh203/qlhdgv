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
        Schema::create('attachments', function (Blueprint $table) {
        $table->id(); // tự động BIGINT UNSIGNED
        $table->foreignId('module_type_id')->constrained('module_types')->cascadeOnDelete();
        $table->unsignedBigInteger('entity_id');
        $table->string('file_name', 260);
        $table->string('file_path', 1000);
        $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
        $table->timestamp('uploaded_at')->useCurrent();

        $table->index(['module_type_id', 'entity_id']);
    });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
