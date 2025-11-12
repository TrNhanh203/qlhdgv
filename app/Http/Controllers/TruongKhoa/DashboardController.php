<?php

namespace App\Http\Controllers\TruongKhoa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Lecture;
use App\Models\Course;
use App\Models\TeachingDuty;
use App\Models\Exam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\University;


class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $lecture = $user->lecture;
        $facultyId = $lecture->department->faculty_id ?? null;
        $university = null;
        if ($user && $user->university_id) {
            $university = University::find($user->university_id);
        }
        $universityCodeShort = null;
        if ($university && $university->email) {
            if (preg_match('/@([^.]+)\./', $university->email, $matches)) {
                $universityCodeShort = strtoupper($matches[1]);
            }
        }
        $stats = [
            'total_departments' => Department::where('faculty_id', $facultyId)->count(),
            'total_lecturers' => Lecture::whereHas('department', function ($query) use ($facultyId) {
                $query->where('faculty_id', $facultyId);
            })->count(),
            'total_courses' => Course::whereHas('department', function ($q) use ($facultyId) {
                $q->where('faculty_id', $facultyId);
            })->count(),

            'total_teaching_duties' => TeachingDuty::whereHas('lecture', function ($query) use ($facultyId) {
                $query->whereHas('department', function ($q) use ($facultyId) {
                    $q->where('faculty_id', $facultyId);
                });
            })->count(),
        ];
        $lecturers = Lecture::whereHas('department', function ($q) use ($facultyId) {
            $q->where('faculty_id', $facultyId);
        })
            ->with('department')
            ->paginate(10)
            ->through(function ($lec) {
                return [
                    'name' => $lec->fullname ?? $lec->full_name,
                    'department' => $lec->department->department_name ?? 'Chưa có',
                    'degree' => $lec->degree ?? 'Chưa rõ',
                    'course_count' => $lec->teachingDuties()->count(),
                ];
            });

        $departmentStats = Department::where('faculty_id', $facultyId)
            ->withCount('lectures')
            ->paginate(10)
            ->map(function ($department) {
                return [
                    'name' => $department->department_name,
                    'lecturer_count' => $department->lectures_count,
                    'course_count' => Course::where('department_id', $department->id)->count()

                ];
            });
        $lecturerStats = Department::where('faculty_id', $facultyId)
            ->withCount('lectures')
            ->paginate(10)
            ->map(function ($dept) {
                return [
                    'department' => $dept->department_name,
                    'lecturer_count' => $dept->lectures_count
                ];
            });
        $teachingStats = TeachingDuty::whereHas('lecture', function ($query) use ($facultyId) {
            $query->whereHas('department', function ($q) use ($facultyId) {
                $q->where('faculty_id', $facultyId);
            });
        })
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        return view('truongkhoa.dashboard', compact(
            'stats',
            'departmentStats',
            'teachingStats',
            'user',
            'university',
            'lecturers',
            'lecturerStats',

            'universityCodeShort',
        ));
    }
}
