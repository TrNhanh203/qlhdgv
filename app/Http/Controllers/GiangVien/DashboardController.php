<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeachingDuty;
use App\Models\ExamProctoring;
use App\Models\Meeting;
use App\Models\University;
use App\Models\Lecture;
use App\Models\Course;
use App\Models\Workload;
use App\Models\Exam;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $lectureId = $user->lecture_id;
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
            'total_teaching_duties' => TeachingDuty::where('lecture_id', $lectureId)->count(),
            'total_exam_proctorings' => ExamProctoring::where('lecture_id', $lectureId)->count(),
            'total_meetings' => Meeting::where('participants', 'LIKE', "%$lectureId%")->count(),
            'total_workload_hours' => Workload::where('lecture_id', $lectureId)
                ->selectRaw('COALESCE(SUM(teaching_hours),0) 
                        + COALESCE(SUM(exam_proctoring_hours),0) 
                        + COALESCE(SUM(other_duty_hours),0) as total')
                ->value('total'),
        ];

       $recentTeaching = TeachingDuty::where('lecture_id', $lectureId)
            ->orderBy('duty_date', 'desc')
            ->take(5)
            ->get();


        $upcomingExams = ExamProctoring::where('lecture_id', $lectureId)
            ->whereHas('exam', function($query) {
                $query->where('exam_start', '>=', now());
            })
            ->with(['exam', 'exam.room']) 
            ->orderBy(
                Exam::select('exam_start')
                    ->whereColumn('exams.id', 'exam_proctorings.exam_id')
            )
            ->limit(5)
            ->get();



        $monthlyStats = TeachingDuty::where('lecture_id', $lectureId)
            ->selectRaw('MONTH(duty_date) as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $upcomingMeetings = Meeting::where('participants', 'LIKE', "%$lectureId%")
            ->where('meeting_date', '>=', now())
            ->orderBy('meeting_date', 'asc')
            ->limit(3)
            ->get();

        return view('giangvien.dashboard', compact(
            'stats',
            'recentTeaching',
            'upcomingExams',
            'monthlyStats',
            'upcomingMeetings',
            'user', 
            'university', 
            'universityCodeShort',
));
    }
}

