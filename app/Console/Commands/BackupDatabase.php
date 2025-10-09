<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Sao lưu dữ liệu từng bảng vào file JSON riêng';

    public function handle()
    {
        $backupPath = storage_path('backups/');

        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0777, true);
        }

        
        $tables = [
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
        $dbTables = DB::select('SHOW TABLES');
        $allTables = [];
        foreach ($dbTables as $table) {
            $tableName = $table->{"Tables_in_$dbName"} ?? null;
            if ($tableName) {
                $allTables[] = $tableName;
            }
        }

        foreach ($tables as $tableName) {
            if (!in_array($tableName, $allTables)) {
                $this->warn("⚠ Bảng {$tableName} không tồn tại, bỏ qua...");
                continue;
            }

            $data = DB::table($tableName)->get()->toArray();
            $filePath = $backupPath . "{$tableName}.json";

            File::put($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->info("✅ Đã sao lưu bảng {$tableName} vào {$filePath}");
        }

        $this->info("🎉 Sao lưu database hoàn tất!");
    }
}
