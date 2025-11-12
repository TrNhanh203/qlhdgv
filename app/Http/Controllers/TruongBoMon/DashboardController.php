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
    // public function dashboard()
    // {
    //     $user = Auth::user();
    //     $lecture = $user->lecture;
    //     $departmentId = $lecture->department_id ?? null;
    //             $university = null;
    //     if ($user && $user->university_id) {
    //         $university = University::find($user->university_id);
    //     }
    //     $universityCodeShort = null;
    //     if ($university && $university->email) {
    //         if (preg_match('/@([^.]+)\./', $university->email, $matches)) {
    //             $universityCodeShort = strtoupper($matches[1]); // ví dụ contact@tdmu.edu.vn -> TDMU
    //         }
    //     }
    //     // Thống kê tổng quan cho bộ môn
    //     $stats = [
    //         'total_lecturers' => Lecture::where('department_id', $departmentId)->count(),
    //         'total_courses' => Course::whereHas('educationProgram', function($query) use ($departmentId) {
    //             $query->where('department_id', $departmentId);
    //         })->count(),
    //         'total_teaching_duties' => TeachingDuty::whereHas('lecture', function($query) use ($departmentId) {
    //             $query->where('department_id', $departmentId);
    //         })->count(),
    //         'total_meetings' => Meeting::where('department_id', $departmentId)->count(),
    //     ];

    //     // Thống kê giảng viên trong bộ môn
    //     $lecturerStats = Lecture::where('department_id', $departmentId)
    //     ->withCount(['teachingDuties', 'examProctorings'])
    //     ->get()
    //     ->map(function($lecturer) {
    //         return [
    //             'name' => $lecturer->full_name,
    //             'teaching_count' => $lecturer->teaching_duties_count,
    //             'exam_count' => $lecturer->exam_proctorings_count,
    //             'degree' => $lecturer->degree
    //         ];
    //     });
    //     // Thống kê giảng dạy theo tuần
    //     $weeklyStats = TeachingDuty::whereHas('lecture', function($query) use ($departmentId) {
    //         $query->where('department_id', $departmentId);
    //     })
    //     ->selectRaw('WEEK(created_at) as week, COUNT(*) as count')
    //     ->groupBy('week')
    //     ->orderBy('week', 'desc')
    //     ->limit(8)
    //     ->pluck('count', 'week')
    //     ->toArray();

    //     // Danh sách cuộc họp gần đây
    //     $recentMeetings = Meeting::where('department_id', $departmentId)
    //         ->orderBy('meeting_date', 'desc')
    //         ->limit(5)
    //         ->get();

    //     return view('truongbomon.dashboard', compact('stats', 'lecturerStats', 'weeklyStats', 'recentMeetings','user', 
    //         'university', 
    //         'universityCodeShort', ));
    // }


    public function dashboard()
    {
        $user = Auth::user();
        $lecture = $user->lecture;
        $departmentId = $lecture->department_id ?? null;

        // === Lấy thông tin trường đại học & rút ngắn code email ===
        $university = $user->university_id ? University::find($user->university_id) : null;
        $universityCodeShort = null;
        if ($university && $university->email && preg_match('/@([^.]+)\./', $university->email, $matches)) {
            $universityCodeShort = strtoupper($matches[1]); // contact@tdmu.edu.vn -> TDMU
        }

        // === 1️⃣ Thống kê tổng quan cho Bộ môn ===
        $stats = [
            'total_lecturers' => Lecture::where('department_id', $departmentId)->count(),

            // Học phần: đếm theo courses của bộ môn (vì course.department_id = dept.id)
            'total_courses' => DB::table('courses')
                ->where('department_id', $departmentId)
                ->count(),

            // Nhiệm vụ giảng dạy: lecture thuộc bộ môn này
            'total_teaching_duties' => DB::table('teaching_duties as td')
                ->join('lectures as l', 'td.lecture_id', '=', 'l.id')
                ->where('l.department_id', $departmentId)
                ->count(),

            // Cuộc họp nội bộ bộ môn
            'total_meetings' => DB::table('meetings')
                ->where('department_id', $departmentId)
                ->count(),
        ];

        // === 2️⃣ Thống kê giảng viên trong bộ môn ===
        $lecturerStats = DB::table('lectures as l')
            ->leftJoin('departments as d', 'l.department_id', '=', 'd.id')
            ->where('l.department_id', $departmentId)
            ->select(
                'l.full_name as name',
                'l.degree',
                DB::raw('(SELECT COUNT(*) FROM teaching_duties td WHERE td.lecture_id = l.id) as teaching_count'),
                DB::raw('(SELECT COUNT(*) FROM exam_proctorings ep WHERE ep.lecture_id = l.id) as exam_count')
            )
            ->orderBy('l.full_name')
            ->get();

        // === 3️⃣ Thống kê giảng dạy theo tuần (8 tuần gần nhất) ===
        $weeklyStats = DB::table('teaching_duties as td')
            ->join('lectures as l', 'td.lecture_id', '=', 'l.id')
            ->where('l.department_id', $departmentId)
            ->selectRaw('WEEK(td.created_at, 1) as week, COUNT(*) as count')
            ->groupBy('week')
            ->orderBy('week', 'desc')
            ->limit(8)
            ->pluck('count', 'week')
            ->toArray();

        // === 4️⃣ Danh sách cuộc họp gần đây ===
        $recentMeetings = DB::table('meetings')
            ->where('department_id', $departmentId)
            ->orderBy('meeting_date', 'desc')
            ->limit(5)
            ->get();

        // === 5️⃣ (Tùy chọn mở rộng) – Liệt kê học phần thuộc CTĐT có bộ môn này phụ trách ===
        $coursesInPrograms = DB::table('outline_program_courses as opc')
            ->join('courses as c', 'opc.course_id', '=', 'c.id')
            ->join('outline_program_versions as v', 'opc.program_version_id', '=', 'v.id')
            ->join('education_programs as ep', 'v.education_program_id', '=', 'ep.id')
            ->select('c.course_code', 'c.course_name', 'ep.program_name', 'v.version_code')
            ->where('c.department_id', $departmentId)
            ->limit(10)
            ->get();

        return view('truongbomon.dashboard', compact(
            'stats',
            'lecturerStats',
            'weeklyStats',
            'recentMeetings',
            'coursesInPrograms',
            'user',
            'university',
            'universityCodeShort'
        ));
    }
}
