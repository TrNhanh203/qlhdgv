<?php

namespace App\Http\Controllers\SuperAdmin;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\University;
use App\Models\User;
use App\Models\Lecture;
use App\Models\Role;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    
    public function dashboard()
    {
        $user = Auth::user();
        $university = null;
if ($user && $user->university_id) {
    $university = University::find($user->university_id);
}
        
        $stats = [
            'total_universities' => University::count(),
            'total_admins' => User::where('user_type', 'admin')->count(),
            'total_users' => User::count(),
            'system_uptime' => 99.5, 
        ];
$universityCodeShort = null;
        if ($university && $university->email) {
            if (preg_match('/@([^.]+)\./', $university->email, $matches)) {
                $universityCodeShort = strtoupper($matches[1]); 
            }
        }
        $roleDistribution = [
            'superadmin' => User::where('user_type', 'superadmin')->count(),
            'admin' => User::where('user_type', 'admin')->count(),
            'truongkhoa' => User::where('user_type', 'truongkhoa')->count(),
            'truongbomon' => User::where('user_type', 'truongbomon')->count(),
            'giangvien' => User::where('user_type', 'giangvien')->count(),
        ];

        $schoolsByRegion = University::selectRaw('
            CASE 
                WHEN address LIKE "%Hà Nội%" OR address LIKE "%Hải Phòng%" OR address LIKE "%Thái Nguyên%" THEN "Miền Bắc"
                WHEN address LIKE "%Đà Nẵng%" OR address LIKE "%Huế%" OR address LIKE "%Quảng Nam%" THEN "Miền Trung"
                ELSE "Miền Nam"
            END as region,
            COUNT(*) as count
        ')
        ->groupBy('region')
        ->pluck('count', 'region')
        ->toArray();

        $recentActivities = AuditLog::with('user')
            ->orderBy('logged_at', 'desc')
            ->limit(5)
            ->get();

        return view('superadmin.dashboard', compact(
            'user', 
            'university', 
            'universityCodeShort', 
            'stats', 
            'roleDistribution', 
            'schoolsByRegion', 
            'recentActivities'
        ));

    }
} 