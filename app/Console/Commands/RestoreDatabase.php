<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RestoreDatabase extends Command
{
    protected $signature = 'restore:database';
    protected $description = 'Khôi phục dữ liệu từ file JSON vào database theo đúng thứ tự bảng';

    public function handle()
    {
        $backupPath = storage_path('backups/');

        if (!File::exists($backupPath)) {
            $this->error("❌ Thư mục backup không tồn tại!");
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        $restoreOrder = [
            'universities',
                'faculties',
                'departments',
                'academic_years',
                'semesters',
                'roles',
                'lectures',
                'users',
                'lecture_roles',
                'role_permissions',
                'education_programs',
                'courses',
                'teaching_duties',
                'rooms',
                'exams',
                'exam_proctorings',
                'exam_lectures',
                'meetings',
                'meeting_participants',
                'workloads',
                'attachments',
                'module_types',
                'audit_logs',
                'sessions',
                'personal_access_tokens',
                'status_codes',
                'cache',
                'cache_locks',
                'migrations',
                'system_notifications'
        ];

        $dbName = env('DB_DATABASE');
        $dbTables = DB::select("SHOW TABLES");
        $allTables = [];
        foreach ($dbTables as $table) {
            $tableName = $table->{"Tables_in_$dbName"} ?? null;
            if ($tableName) {
                $allTables[] = $tableName;
            }
        }

        foreach ($restoreOrder as $tableName) {
            if (!in_array($tableName, $allTables)) {
                $this->warn("⚠ Bảng {$tableName} không tồn tại trong database, bỏ qua...");
                continue;
            }

            $filePath = $backupPath . $tableName . '.json';

            if (File::exists($filePath)) {
                $jsonData = File::get($filePath);
                $data = json_decode($jsonData, true);

                if (!empty($data)) {
                    DB::table($tableName)->truncate(); 
                    DB::table($tableName)->insert($data); 
                    $this->info("✅ Đã khôi phục dữ liệu cho bảng {$tableName}");
                } else {
                    $this->warn("⚠ Không có dữ liệu để khôi phục cho bảng {$tableName}");
                }
            } else {
                $this->warn("⚠ Không tìm thấy file backup cho bảng {$tableName}");
            }
        }

        
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $this->info("🎉 Khôi phục dữ liệu hoàn tất!");
    }
}
