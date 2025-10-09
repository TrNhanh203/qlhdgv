<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\University;
use App\Models\Role;
use App\Models\StatusCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SystemController extends Controller
{
    public function roles()
    {
        $roles = Role::all();
        return view('superadmin.settings.roles', compact('roles'));
    }

    public function storeRole(Request $request)
    {
        try {
            $data = $request->only(['role_name', 'description']);
            
            $role = Role::updateOrCreate(
                ['id' => $request->id ?: null],
                $data
            );

            return response()->json([
                'success' => true,
                'message' => 'Lưu thành công',
                'data'    => $role
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyMultipleRoles(Request $request)
    {
        Role::whereIn('id', $request->ids)->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa thành công!']);
    }

    public function backup()
    {
        return view('superadmin.settings.backup');
    }

    public function downloadBackup()
    {
        $db = env('DB_DATABASE');
        $filename = 'backup-' . now()->format('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        if (!File::exists(storage_path('app/backups'))) {
            File::makeDirectory(storage_path('app/backups'), 0755, true);
        }

        $sql = "-- Database backup: {$db}\n";
        $sql .= "-- Created at: " . now() . "\n\n";
        
        $tables = DB::select('SHOW TABLES');
        $keyName = "Tables_in_{$db}";

        foreach ($tables as $table) {
            $tableName = $table->$keyName;

            $createTable = DB::select("SHOW CREATE TABLE {$tableName}");
            $sql .= "\n-- ----------------------------\n";
            $sql .= "-- Table structure for {$tableName}\n";
            $sql .= "-- ----------------------------\n\n";
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                $sql .= "-- Dumping data for table {$tableName}\n";
                foreach ($rows as $row) {
                    $values = array_map(function ($value) {
                        if ($value === null) return 'NULL';
                        return "'" . str_replace("'", "''", $value) . "'";
                    }, (array) $row);
                    
                    $sql .= "INSERT INTO `{$tableName}` VALUES (" . implode(", ", $values) . ");\n";
                }
                $sql .= "\n";
            }
        }

        File::put($path, $sql);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function restoreBackup(Request $request)
    {
        if ($request->hasFile('sql_file')) {
            $file = $request->file('sql_file');
            $path = $file->storeAs('backups', $file->getClientOriginalName());

            $db = env('DB_DATABASE');
            $user = env('DB_USERNAME');
            $pass = env('DB_PASSWORD');
            $host = env('DB_HOST', '127.0.0.1');

            $fullPath = storage_path('app/' . $path);

            exec("mysql -h {$host} -u {$user} -p'{$pass}' {$db} < {$fullPath}");

            return back()->with('success', 'Đã phục hồi database thành công từ file: ' . $path);
        }

        return back()->with('error', 'Bạn chưa chọn file backup.');
    }
}
