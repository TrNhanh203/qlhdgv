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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->string('table_name', 128);
            $table->string('action', 20);
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->timestamp('logged_at')->useCurrent();
            $table->longText('changes_json')->nullable();
            
            // Nếu hay filter theo logged_at thì thêm index
            $table->index('logged_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
