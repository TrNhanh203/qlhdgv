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
        /*
        |--------------------------------------------------------------------------
        | 🧩 1️⃣ Bảng: outline_program_versions, outline_plos, outline_pis, outline_program_courses
        |--------------------------------------------------------------------------
        */
        Schema::create('outline_program_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('education_program_id')->constrained('education_programs');
            $table->string('version_code', 50);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->enum('status', ['draft', 'review', 'approved', 'archived'])->default('draft');
            $table->timestamps();
        });

        Schema::create('outline_plos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_version_id')->constrained('outline_program_versions');
            $table->string('code', 50)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('outline_pis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plo_id')->constrained('outline_plos');
            $table->string('code', 50)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('outline_program_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_version_id')->constrained('outline_program_versions');
            $table->foreignId('course_id')->constrained('courses');
            $table->integer('semester_no')->nullable();
            $table->boolean('is_compulsory')->default(true);
            $table->string('elective_group', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | 🧩 2️⃣ Bảng: outline_courses & outline_course_versions
        |--------------------------------------------------------------------------
        */
        Schema::create('outline_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses');
            $table->timestamps();
        });

        Schema::create('outline_course_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outline_course_id')->constrained('outline_courses');
            $table->integer('version_no')->default(1);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->enum('status', ['draft', 'review', 'approved', 'archived'])->default('draft');
            $table->text('change_log')->nullable();
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | 🧩 3️⃣ Bảng: outline_clos, outline_clo_plo_maps, outline_clo_pi_maps
        |--------------------------------------------------------------------------
        */
        Schema::create('outline_clos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_version_id')->constrained('outline_course_versions');
            $table->string('code', 50)->nullable();
            $table->text('description')->nullable();
            $table->string('bloom_level', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('outline_clo_plo_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clo_id')->constrained('outline_clos');
            $table->foreignId('plo_id')->constrained('outline_plos');
            $table->enum('level', ['I', 'R', 'T', 'A'])->default('I');
            $table->integer('weight')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('outline_clo_pi_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clo_id')->constrained('outline_clos');
            $table->foreignId('pi_id')->constrained('outline_pis');
            $table->enum('level', ['I', 'R', 'T', 'A'])->default('I');
            $table->integer('weight')->default(1);
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | 🧩 4️⃣ Bảng: outline_templates, outline_section_templates, outline_section_contents
        |--------------------------------------------------------------------------
        */
        Schema::create('outline_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained('faculties');
            $table->string('code', 50)->nullable();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('outline_section_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outline_template_id')->constrained('outline_templates');
            $table->string('code', 50)->nullable();
            $table->string('title', 255);
            $table->integer('order_no')->default(1);
            $table->boolean('is_reference')->default(false);
            $table->text('default_content')->nullable();
            $table->timestamps();
        });

        Schema::create('outline_section_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_version_id')->constrained('outline_course_versions');
            $table->foreignId('section_template_id')->constrained('outline_section_templates');
            $table->longText('content_html')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | 🧩 5️⃣ Bảng: outline_report_snapshots
        |--------------------------------------------------------------------------
        */
        Schema::create('outline_report_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_version_id')->constrained('outline_program_versions');
            $table->timestamp('generated_at')->useCurrent();
            $table->string('file_path', 255)->nullable();
            $table->string('hash', 255)->nullable();
        });

        /*
        |--------------------------------------------------------------------------
        | 🧩 6️⃣ Cập nhật bảng hiện có để hỗ trợ module outline
        |--------------------------------------------------------------------------
        */
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('has_outline')->default(false)->after('credit');
            $table->enum('outline_status', ['none', 'draft', 'review', 'approved'])->default('none')->after('has_outline');
            $table->unsignedBigInteger('last_outline_version_id')->nullable()->after('outline_status');
        });

        Schema::table('faculties', function (Blueprint $table) {
            $table->unsignedBigInteger('default_outline_template_id')->nullable();
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->unsignedBigInteger('outline_manager_id')->nullable();
        });

        Schema::table('education_programs', function (Blueprint $table) {
            $table->unsignedBigInteger('current_version_id')->nullable();
        });

        Schema::table('lectures', function (Blueprint $table) {
            $table->string('academic_rank', 100)->nullable();
            $table->string('signature_path', 255)->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_outline_reviewer')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa bảng theo thứ tự ngược để tránh lỗi FK
        Schema::dropIfExists('outline_report_snapshots');
        Schema::dropIfExists('outline_section_contents');
        Schema::dropIfExists('outline_section_templates');
        Schema::dropIfExists('outline_templates');
        Schema::dropIfExists('outline_clo_pi_maps');
        Schema::dropIfExists('outline_clo_plo_maps');
        Schema::dropIfExists('outline_clos');
        Schema::dropIfExists('outline_course_versions');
        Schema::dropIfExists('outline_courses');
        Schema::dropIfExists('outline_program_courses');
        Schema::dropIfExists('outline_pis');
        Schema::dropIfExists('outline_plos');
        Schema::dropIfExists('outline_program_versions');

        // Gỡ các cột thêm mới trong bảng cũ
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['has_outline', 'outline_status', 'last_outline_version_id']);
        });

        Schema::table('faculties', function (Blueprint $table) {
            $table->dropColumn('default_outline_template_id');
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('outline_manager_id');
        });

        Schema::table('education_programs', function (Blueprint $table) {
            $table->dropColumn('current_version_id');
        });

        Schema::table('lectures', function (Blueprint $table) {
            $table->dropColumn(['academic_rank', 'signature_path']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_outline_reviewer');
        });
    }
};
