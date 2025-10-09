<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Room;
use App\Models\ExamProctoring;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();
        
        $exams = Exam::with(['course.department', 'room', 'academicYear', 'semester', 'proctorings.lecture'])
            ->where('academic_year_id', $currentYear->id ?? null)
            ->where('semester_id', $currentSemester->id ?? null)
            ->orderBy('exam_start')
            ->paginate(20);
            
        $stats = [
            'total_exams' => $exams->total(),
            'pending_exams' => Exam::where('status', 'pending')->count(),
            'approved_exams' => Exam::where('status', 'approved')->count(),
            'rejected_exams' => Exam::where('status', 'rejected')->count(),
        ];
            
        return view('admin.lichthi.index', compact('exams', 'stats', 'currentYear', 'currentSemester'));
    }

    public function create()
    {
        $courses = Course::with('department')->get();
        $rooms = Room::where('status', 'active')->get();
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();
        
        return view('admin.lichthi.create', compact('courses', 'rooms', 'currentYear', 'currentSemester'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'exam_name' => 'required|string|max:250',
            'exam_type' => 'required|string|max:50',
            'exam_batch' => 'nullable|string|max:50',
            'exam_start' => 'required|date',
            'exam_end' => 'required|date|after:exam_start',
            'exam_form' => 'required|string|max:100',
            'room_id' => 'nullable|exists:rooms,id',
            'expected_students' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        try {
            if ($request->room_id) {
                $roomConflict = Exam::where('room_id', $request->room_id)
                    ->where(function($query) use ($request) {
                        $query->whereBetween('exam_start', [$request->exam_start, $request->exam_end])
                              ->orWhereBetween('exam_end', [$request->exam_start, $request->exam_end])
                              ->orWhere(function($q) use ($request) {
                                  $q->where('exam_start', '<=', $request->exam_start)
                                    ->where('exam_end', '>=', $request->exam_end);
                              });
                    })
                    ->exists();
                    
                if ($roomConflict) {
                    return back()->with('error', 'Phòng thi đã được sử dụng trong khoảng thời gian này!');
                }
            }
            
            Exam::create([
                'course_id' => $request->course_id,
                'academic_year_id' => $request->academic_year_id,
                'semester_id' => $request->semester_id,
                'exam_name' => $request->exam_name,
                'exam_type' => $request->exam_type,
                'exam_batch' => $request->exam_batch,
                'exam_start' => $request->exam_start,
                'exam_end' => $request->exam_end,
                'exam_form' => $request->exam_form,
                'room_id' => $request->room_id,
                'expected_students' => $request->expected_students,
                'notes' => $request->notes,
                'status' => 'approved'
            ]);
            
            return redirect()->route('admin.lichthi.index')->with('success', 'Tạo kỳ thi thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $exam = Exam::with(['course.department', 'room', 'academicYear', 'semester', 'proctorings.lecture'])
            ->findOrFail($id);
            
        return view('admin.lichthi.show', compact('exam'));
    }

    public function edit($id)
    {
        $exam = Exam::findOrFail($id);
        $courses = Course::with('department')->get();
        $rooms = Room::where('status', 'active')->get();
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();
        
        return view('admin.lichthi.edit', compact('exam', 'courses', 'rooms', 'currentYear', 'currentSemester'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'exam_name' => 'required|string|max:250',
            'exam_type' => 'required|string|max:50',
            'exam_batch' => 'nullable|string|max:50',
            'exam_start' => 'required|date',
            'exam_end' => 'required|date|after:exam_start',
            'exam_form' => 'required|string|max:100',
            'room_id' => 'nullable|exists:rooms,id',
            'expected_students' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        try {
            $exam = Exam::findOrFail($id);
            
            if ($request->room_id) {
                $roomConflict = Exam::where('room_id', $request->room_id)
                    ->where('id', '!=', $id)
                    ->where(function($query) use ($request) {
                        $query->whereBetween('exam_start', [$request->exam_start, $request->exam_end])
                              ->orWhereBetween('exam_end', [$request->exam_start, $request->exam_end])
                              ->orWhere(function($q) use ($request) {
                                  $q->where('exam_start', '<=', $request->exam_start)
                                    ->where('exam_end', '>=', $request->exam_end);
                              });
                    })
                    ->exists();
                    
                if ($roomConflict) {
                    return back()->with('error', 'Phòng thi đã được sử dụng trong khoảng thời gian này!');
                }
            }
            
            $exam->update([
                'course_id' => $request->course_id,
                'exam_name' => $request->exam_name,
                'exam_type' => $request->exam_type,
                'exam_batch' => $request->exam_batch,
                'exam_start' => $request->exam_start,
                'exam_end' => $request->exam_end,
                'exam_form' => $request->exam_form,
                'room_id' => $request->room_id,
                'expected_students' => $request->expected_students,
                'notes' => $request->notes
            ]);
            
            return redirect()->route('admin.lichthi.index')->with('success', 'Cập nhật kỳ thi thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $exam = Exam::findOrFail($id);
            
            $hasProctorings = ExamProctoring::where('exam_id', $id)->exists();
            if ($hasProctorings) {
                return back()->with('error', 'Không thể xóa kỳ thi đã có phân công coi thi!');
            }
            
            $exam->delete();
            
            return redirect()->route('admin.lichthi.index')->with('success', 'Xóa kỳ thi thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    public function approve($id)
    {
        try {
            $exam = Exam::findOrFail($id);
            $exam->update(['status' => 'approved']);
            
            return back()->with('success', 'Duyệt kỳ thi thành công!');
            
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
            $exam = Exam::findOrFail($id);
            $exam->update([
                'status' => 'rejected',
                'notes' => $exam->notes . "\nLý do từ chối: " . $request->rejection_reason
            ]);
            
            return back()->with('success', 'Từ chối kỳ thi thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    public function statistics()
    {
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();
        
        $stats = [
            'total_exams' => Exam::where('academic_year_id', $currentYear->id ?? null)
                ->where('semester_id', $currentSemester->id ?? null)
                ->count(),
            'pending_exams' => Exam::where('status', 'pending')
                ->where('academic_year_id', $currentYear->id ?? null)
                ->where('semester_id', $currentSemester->id ?? null)
                ->count(),
            'approved_exams' => Exam::where('status', 'approved')
                ->where('academic_year_id', $currentYear->id ?? null)
                ->where('semester_id', $currentSemester->id ?? null)
                ->count(),
            'exams_by_type' => Exam::where('academic_year_id', $currentYear->id ?? null)
                ->where('semester_id', $currentSemester->id ?? null)
                ->groupBy('exam_type')
                ->selectRaw('exam_type, count(*) as count')
                ->get(),
            'exams_by_form' => Exam::where('academic_year_id', $currentYear->id ?? null)
                ->where('semester_id', $currentSemester->id ?? null)
                ->groupBy('exam_form')
                ->selectRaw('exam_form, count(*) as count')
                ->get()
        ];
        
        return response()->json($stats);
    }
}
