<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('system_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title', 300);
            $table->longText('message')->nullable();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->boolean('is_global')->default(true);
            $table->foreignId('university_id')->nullable()
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->timestamp('start_at')->useCurrent();
            $table->timestamp('end_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

        Schema::create('notification_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')
                ->constrained('system_notifications')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->timestamp('read_at')->useCurrent();
            $table->unique(['notification_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_reads');
        Schema::dropIfExists('system_notifications');
    }
};
