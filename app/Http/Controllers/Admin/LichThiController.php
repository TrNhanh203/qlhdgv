<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamProctoring;
use App\Models\Exam;
use App\Models\Lecture;
use App\Models\Room;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;

class LichThiController extends Controller
{
    public function index()
    {
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();

        $exam_proctorings = ExamProctoring::with(['exam.course.department', 'exam.room', 'exam.semester', 'lecture'])
            ->whereHas('exam', function($query) use ($currentYear, $currentSemester) {
                $query->where('academic_year_id', $currentYear->id ?? null)
                    ->where('semester_id', $currentSemester->id ?? null);
            })
            ->paginate(10);

        $exams = Exam::with(['course.department', 'room', 'academicYear', 'semester', 'proctorings.lecture'])
            ->where('academic_year_id', $currentYear->id ?? null)
            ->where('semester_id', $currentSemester->id ?? null)
            ->orderBy('exam_start')
            ->paginate(20);

        $pendingId  = 12;
        $approvedId = 11;

        $stats = [
            'total_exams' => $exams->total(),
            'pending_exams' => Exam::where('status_id', $pendingId)
                ->where('academic_year_id', $currentYear->id ?? null)
                ->where('semester_id', $currentSemester->id ?? null)
                ->count(),
            'approved_exams' => Exam::where('status_id', $approvedId)
                ->where('academic_year_id', $currentYear->id ?? null)
                ->where('semester_id', $currentSemester->id ?? null)
                ->count(),
            'exams_with_proctors' => Exam::where('status_id', $approvedId)
                ->where('academic_year_id', $currentYear->id ?? null)
                ->where('semester_id', $currentSemester->id ?? null)
                ->whereHas('proctorings')
                ->count(),
        ];

        return view('admin.lichthi.index', compact('exams', 'stats', 'currentYear', 'currentSemester', 'exam_proctorings'));
    }

    public function schedule()
    {
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();

        $approvedId = 11;

        $exams = Exam::with(['course.department', 'room', 'proctorings.lecture'])
            ->where('academic_year_id', $currentYear->id ?? null)
            ->where('semester_id', $currentSemester->id ?? null)
            ->where('status_id', $approvedId)
            ->orderBy('exam_start')
            ->get()
            ->groupBy(function ($exam) {
                return \Carbon\Carbon::parse($exam->exam_start)->format('Y-m-d');
            });

        return view('admin.lichthi.schedule', compact('exams', 'currentYear', 'currentSemester'));
    }

    public function roomSchedule()
    {
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();

        $approvedId = 11;

        $rooms = Room::where('status', 'active')->get();
        $roomSchedules = [];

        foreach ($rooms as $room) {
            $roomSchedules[$room->id] = Exam::with(['course.department', 'proctorings.lecture'])
                ->where('academic_year_id', $currentYear->id ?? null)
                ->where('semester_id', $currentSemester->id ?? null)
                ->where('status_id', $approvedId)
                ->where('room_id', $room->id)
                ->orderBy('exam_start')
                ->get();
        }

        return view('admin.lichthi.room-schedule', compact('rooms', 'roomSchedules', 'currentYear', 'currentSemester'));
    }

    public function export()
    {
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();

        $approvedId = 11;

        $exams = Exam::with(['course.department', 'room', 'proctorings.lecture'])
            ->where('academic_year_id', $currentYear->id ?? null)
            ->where('semester_id', $currentSemester->id ?? null)
            ->where('status_id', $approvedId)
            ->orderBy('exam_start')
            ->get();

        return response()->json(['message' => 'Export functionality will be implemented']);
    }
}
