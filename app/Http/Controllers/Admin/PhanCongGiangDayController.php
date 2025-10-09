<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeachingDuty;
use App\Models\Course;
use App\Models\Lecture;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Room;
use Illuminate\Http\Request;

class PhanCongGiangDayController extends Controller
{
    public function index()
    {
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();
        
        $assignments = TeachingDuty::with([
            'lecture.department',
            'course.department',
            'room',
            'academicYear',
            'semester'
        ])
        ->where('academic_year_id', $currentYear->id ?? null)
        ->where('semester_id', $currentSemester->id ?? null)
        ->orderBy('start_time')
        ->paginate(20);
        
        $stats = [
            'total_assignments' => $assignments->total(),
            'pending_assignments' => TeachingDuty::where('status', 'pending')
                ->where('academic_year_id', $currentYear->id ?? null)
                ->where('semester_id', $currentSemester->id ?? null)
                ->count(),
            'approved_assignments' => TeachingDuty::where('status', 'approved')
                ->where('academic_year_id', $currentYear->id ?? null)
                ->where('semester_id', $currentSemester->id ?? null)
                ->count(),
            'completed_assignments' => TeachingDuty::where('status', 'completed')
                ->where('academic_year_id', $currentYear->id ?? null)
                ->where('semester_id', $currentSemester->id ?? null)
                ->count(),
            'total_hours' => TeachingDuty::where('academic_year_id', $currentYear->id ?? null)
                ->where('semester_id', $currentSemester->id ?? null)
                ->where('status', 'approved')
                ->sum('hours')
        ];
        
        return view('admin.phanconggiangday.index', compact('assignments', 'stats', 'currentYear', 'currentSemester'));
    }

    public function create()
    {
        $courses = Course::with('department')->get();
        $lecturers = Lecture::with('department')->where('status_id', 1)->get();
        $rooms = Room::where('status', 'active')->get();
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();
        
        return view('admin.phanconggiangday.create', compact('courses', 'lecturers', 'rooms', 'currentYear', 'currentSemester'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lecture_id' => 'required|exists:lectures,id',
            'course_id' => 'required|exists:courses,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'hours' => 'required|numeric|min:1',
            'room_id' => 'nullable|exists:rooms,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'notes' => 'nullable|string|max:500'
        ]);
        
        try {
            $conflict = TeachingDuty::where('lecture_id', $request->lecture_id)
                ->where(function($query) use ($request) {
                    $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                          ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                          ->orWhere(function($q) use ($request) {
                              $q->where('start_time', '<=', $request->start_time)
                                ->where('end_time', '>=', $request->end_time);
                          });
                })
                ->exists();
                
            if ($conflict) {
                return back()->with('error', 'Giảng viên đã có lịch dạy trong khoảng thời gian này!');
            }
            
            TeachingDuty::create([
                'lecture_id' => $request->lecture_id,
                'course_id' => $request->course_id,
                'academic_year_id' => $request->academic_year_id,
                'semester_id' => $request->semester_id,
                'hours' => $request->hours,
                'room_id' => $request->room_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'notes' => $request->notes,
                'status' => 'approved'
            ]);
            
            return redirect()->route('admin.phanconggiangday.index')->with('success', 'Tạo phân công giảng dạy thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $assignment = TeachingDuty::with([
            'lecture.department',
            'course.department',
            'room',
            'academicYear',
            'semester'
        ])->findOrFail($id);
        
        return view('admin.phanconggiangday.show', compact('assignment'));
    }

    public function edit($id)
    {
        $assignment = TeachingDuty::findOrFail($id);
        $courses = Course::with('department')->get();
        $lecturers = Lecture::with('department')->where('status_id', 1)->get();
        $rooms = Room::where('status', 'active')->get();
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();
        
        return view('admin.phanconggiangday.edit', compact('assignment', 'courses', 'lecturers', 'rooms', 'currentYear', 'currentSemester'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'lecture_id' => 'required|exists:lectures,id',
            'course_id' => 'required|exists:courses,id',
            'hours' => 'required|numeric|min:1',
            'room_id' => 'nullable|exists:rooms,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'notes' => 'nullable|string|max:500'
        ]);
        
        try {
            $assignment = TeachingDuty::findOrFail($id);
            
            $conflict = TeachingDuty::where('lecture_id', $request->lecture_id)
                ->where('id', '!=', $id)
                ->where(function($query) use ($request) {
                    $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                          ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                          ->orWhere(function($q) use ($request) {
                              $q->where('start_time', '<=', $request->start_time)
                                ->where('end_time', '>=', $request->end_time);
                          });
                })
                ->exists();
                
            if ($conflict) {
                return back()->with('error', 'Giảng viên đã có lịch dạy trong khoảng thời gian này!');
            }
            
            $assignment->update([
                'lecture_id' => $request->lecture_id,
                'course_id' => $request->course_id,
                'hours' => $request->hours,
                'room_id' => $request->room_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'notes' => $request->notes
            ]);
            
            return redirect()->route('admin.phanconggiangday.index')->with('success', 'Cập nhật phân công giảng dạy thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     */
    public function destroy($id)
    {
        try {
            $assignment = TeachingDuty::findOrFail($id);
            $assignment->delete();
            
            return redirect()->route('admin.phanconggiangday.index')->with('success', 'Xóa phân công giảng dạy thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    public function approve($id)
    {
        try {
            $assignment = TeachingDuty::findOrFail($id);
            $assignment->update(['status' => 'approved']);
            
            return back()->with('success', 'Duyệt phân công giảng dạy thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);
        
        try {
            $assignment = TeachingDuty::findOrFail($id);
            $assignment->update([
                'status' => 'rejected',
                'notes' => $assignment->notes . "\nLý do từ chối: " . $request->rejection_reason
            ]);
            
            return back()->with('success', 'Từ chối phân công giảng dạy thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    
    public function getLecturerWorkload($lecturerId, $semesterId)
    {
        $totalHours = TeachingDuty::where('lecture_id', $lecturerId)
            ->where('semester_id', $semesterId)
            ->where('status', 'approved')
            ->sum('hours');
            
        return response()->json(['total_hours' => $totalHours]);
    }
    
    public function statistics()
    {
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();
        
        $stats = [
            'total_assignments' => TeachingDuty::where('academic_year_id', $currentYear->id ?? null)
                ->where('semester_id', $currentSemester->id ?? null)
                ->count(),
            'assignments_by_status' => TeachingDuty::where('academic_year_id', $currentYear->id ?? null)
                ->where('semester_id', $currentSemester->id ?? null)
                ->groupBy('status')
                ->selectRaw('status, count(*) as count')
                ->get(),
            'total_hours_by_department' => TeachingDuty::where('academic_year_id', $currentYear->id ?? null)
                ->where('semester_id', $currentSemester->id ?? null)
                ->where('status', 'approved')
                ->join('lectures', 'teaching_duties.lecture_id', '=', 'lectures.id')
                ->join('departments', 'lectures.department_id', '=', 'departments.id')
                ->groupBy('departments.name')
                ->selectRaw('departments.name, sum(teaching_duties.hours) as total_hours')
                ->get(),
            'most_active_lecturers' => TeachingDuty::where('academic_year_id', $currentYear->id ?? null)
                ->where('semester_id', $currentSemester->id ?? null)
                ->where('status', 'approved')
                ->join('lectures', 'teaching_duties.lecture_id', '=', 'lectures.id')
                ->groupBy('lectures.id', 'lectures.full_name')
                ->selectRaw('lectures.id, lectures.full_name, sum(teaching_duties.hours) as total_hours')
                ->orderBy('total_hours', 'desc')
                ->limit(10)
                ->get()
        ];
        
        return response()->json($stats);
    }
}
