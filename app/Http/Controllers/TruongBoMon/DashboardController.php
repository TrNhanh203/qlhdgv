<?php

namespace App\Http\Controllers\TruongBoMon;

use App\Http\Controllers\Controller;
use App\Models\University;
use App\Models\Lecture;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\TeachingDuty;
use App\Models\Exam;
use App\Models\Meeting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $lecture = $user->lecture;
        $departmentId = $lecture->department_id ?? null;
                $university = null;
        if ($user && $user->university_id) {
            $university = University::find($user->university_id);
        }
        $universityCodeShort = null;
        if ($university && $university->email) {
            if (preg_match('/@([^.]+)\./', $university->email, $matches)) {
                $universityCodeShort = strtoupper($matches[1]); // ví dụ contact@tdmu.edu.vn -> TDMU
            }
        }
        // Thống kê tổng quan cho bộ môn
        $stats = [
            'total_lecturers' => Lecture::where('department_id', $departmentId)->count(),
            'total_courses' => Course::whereHas('educationProgram', function($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->count(),
            'total_teaching_duties' => TeachingDuty::whereHas('lecture', function($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->count(),
            'total_meetings' => Meeting::where('department_id', $departmentId)->count(),
        ];

        // Thống kê giảng viên trong bộ môn
        $lecturerStats = Lecture::where('department_id', $departmentId)
        ->withCount(['teachingDuties', 'examProctorings'])
        ->get()
        ->map(function($lecturer) {
            return [
                'name' => $lecturer->full_name,
                'teaching_count' => $lecturer->teaching_duties_count,
                'exam_count' => $lecturer->exam_proctorings_count,
                'degree' => $lecturer->degree
            ];
        });
        // Thống kê giảng dạy theo tuần
        $weeklyStats = TeachingDuty::whereHas('lecture', function($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
        ->selectRaw('WEEK(created_at) as week, COUNT(*) as count')
        ->groupBy('week')
        ->orderBy('week', 'desc')
        ->limit(8)
        ->pluck('count', 'week')
        ->toArray();

        // Danh sách cuộc họp gần đây
        $recentMeetings = Meeting::where('department_id', $departmentId)
            ->orderBy('meeting_date', 'desc')
            ->limit(5)
            ->get();

        return view('truongbomon.dashboard', compact('stats', 'lecturerStats', 'weeklyStats', 'recentMeetings','user', 
            'university', 
            'universityCodeShort', ));
    }
} 