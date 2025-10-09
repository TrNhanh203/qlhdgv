<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\University;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Lecture;
use App\Models\AuditLog;

class ReportController extends Controller
{
    public function byUniversity()
{
    $universities = University::withCount([
        'faculties',
        'faculties as departments_count' => function ($q) {
            $q->join('departments', 'faculties.id', '=', 'departments.faculty_id');
        },
        'faculties as lectures_count' => function ($q) {
            $q->join('departments', 'faculties.id', '=', 'departments.faculty_id')
              ->join('lectures', 'departments.id', '=', 'lectures.department_id');
        },
        'faculties as education_programs_count' => function ($q) {
            $q->join('education_programs', 'faculties.id', '=', 'education_programs.faculty_id');
        },
        'faculties as courses_count' => function ($q) {
            $q->join('departments', 'faculties.id', '=', 'departments.faculty_id')
              ->join('courses', 'departments.id', '=', 'courses.department_id');
        },
        'rooms',
        'academicYears',
        'semesters',
        'faculties as exams_count' => function ($q) {
            $q->join('departments', 'faculties.id', '=', 'departments.faculty_id')
              ->join('courses', 'departments.id', '=', 'courses.department_id')
              ->join('exams', 'courses.id', '=', 'exams.course_id');
        },
        'faculties as meetings_count' => function ($q) {
            $q->join('departments', 'faculties.id', '=', 'departments.faculty_id')
              ->join('meetings', 'departments.id', '=', 'meetings.department_id');
        },
    ])->get();

    return view('superadmin.reports.universities', compact('universities'));
}




    public function auditLogs()
    {
        $logs = AuditLog::with('user')
            ->orderBy('logged_at', 'desc')
            ->paginate(20);

        return view('superadmin.reports.audit', compact('logs'));
    }
}
