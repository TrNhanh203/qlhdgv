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

        Schema::create('users', function (Blueprint $table) {
            $table->id();  

            $table->foreignId('university_id')
                ->nullable()
                ->constrained('universities')
                ->nullOnDelete();

            $table->foreignId('lecture_id')
                ->nullable()
                ->constrained('lectures')
                ->nullOnDelete();
             $table->enum('role', ['superadmin', 'admin', 'truongkhoa', 'truongbomon', 'giangvien']);
            $table->string('name', 255)->nullable();
            $table->string('email', 255)->unique();
            $table->string('password_hash', 500);
            $table->string('user_type', 50);
            $table->unsignedBigInteger('status_id')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('avatar')->nullable();
            $table->rememberToken();
            $table->date('created_at')->nullable();
            $table->date('updated_at')->nullable();

        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
