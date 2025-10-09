<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AuditLog;
use App\Models\User;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first(); // lấy 1 user có sẵn

        if (!$user) {
            return;
        }

        AuditLog::create([
            'user_id'    => $user->id,
            'table_name' => 'universities',
            'action'     => 'create',
            'entity_id'  => 1,
            'logged_at'  => now()->subDays(1),
            'changes_json' => json_encode(['name' => 'ĐH Quốc Gia']),
        ]);

        AuditLog::create([
            'user_id'    => $user->id,
            'table_name' => 'faculties',
            'action'     => 'update',
            'entity_id'  => 2,
            'logged_at'  => now()->subHours(5),
            'changes_json' => json_encode(['name' => 'Khoa CNTT (đổi tên)']),
        ]);

        AuditLog::create([
            'user_id'    => $user->id,
            'table_name' => 'lectures',
            'action'     => 'delete',
            'entity_id'  => 3,
            'logged_at'  => now()->subMinutes(30),
            'changes_json' => json_encode(['id' => 3, 'name' => 'GV cũ']),
        ]);
    }
}
