<?php

namespace App\Http\Controllers\TruongBoMon;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Exam;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Room;
use Illuminate\Http\Request;

class DeXuatThiController extends Controller
{
    public function dexuatlichthi(){
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();
        
        $courses = Course::with(['department', 'educationProgram'])
            ->where('semester_id', $currentSemester->id ?? null)
            ->where('department_id', auth()->user()->department_id)
            ->get();
            
        $exams = Exam::with(['course', 'room', 'academicYear', 'semester'])
            ->where('academic_year_id', $currentYear->id ?? null)
            ->where('semester_id', $currentSemester->id ?? null)
            ->whereHas('course', function($query) {
                $query->where('department_id', auth()->user()->department_id);
            })
            ->get();
            
        $rooms = Room::where('status', 'active')->get();
            
        return view('truongbomon.dexuathi.dexuatlichthi', compact(
            'courses', 'exams', 'rooms', 'currentYear', 'currentSemester'
        ));
    }
    
    public function storeExam(Request $request)
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
            $course = Course::findOrFail($request->course_id);
            if ($course->department_id != auth()->user()->department_id) {
                return back()->with('error', 'Bạn không có quyền đề xuất kỳ thi cho học phần này!');
            }
            
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
                'status' => 'pending' 
            ]);
            
            return back()->with('success', 'Đề xuất kỳ thi thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    public function updateExam(Request $request, $id)
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
            
            if ($exam->course->department_id != auth()->user()->department_id) {
                return back()->with('error', 'Bạn không có quyền cập nhật kỳ thi này!');
            }
            
            
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
                'notes' => $request->notes,
                'status' => 'pending' 
            ]);
            
            return back()->with('success', 'Cập nhật đề xuất kỳ thi thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    public function destroyExam($id)
    {
        try {
            $exam = Exam::findOrFail($id);
            
            if ($exam->course->department_id != auth()->user()->department_id) {
                return back()->with('error', 'Bạn không có quyền xóa kỳ thi này!');
            }
            
            if ($exam->status == 'approved') {
                return back()->with('error', 'Không thể xóa kỳ thi đã được duyệt!');
            }
            
            $exam->delete();
            
            return back()->with('success', 'Xóa đề xuất kỳ thi thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    public function dexuatdethi(){
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();
        
        $approvedExams = Exam::with(['course', 'room', 'academicYear', 'semester'])
            ->where('academic_year_id', $currentYear->id ?? null)
            ->where('semester_id', $currentSemester->id ?? null)
            ->where('status', 'approved')
            ->whereHas('course', function($query) {
                $query->where('department_id', auth()->user()->department_id);
            })
            ->get();
            
        return view('truongbomon.dexuathi.dexuatdethi', compact(
            'approvedExams', 'currentYear', 'currentSemester'
        ));
    }
    
    public function submitExamPaper(Request $request, $examId)
    {
        $request->validate([
            'exam_paper_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'notes' => 'nullable|string|max:500'
        ]);
        
        try {
            $exam = Exam::findOrFail($examId);
            
        
            if ($exam->course->department_id != auth()->user()->department_id) {
                return back()->with('error', 'Bạn không có quyền nộp đề thi cho kỳ thi này!');
            }
            
            $file = $request->file('exam_paper_file');
            $fileName = 'exam_paper_' . $examId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('exam_papers', $fileName, 'public');
            
            $exam->update([
                'exam_paper_path' => $filePath,
                'exam_paper_submitted_at' => now(),
                'notes' => $exam->notes . "\nĐề thi: " . $request->notes
            ]);
            
            return back()->with('success', 'Nộp đề thi thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
