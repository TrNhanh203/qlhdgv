<?php

namespace App\Http\Controllers\TruongKhoa;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lecture;
use App\Models\Exam;
use App\Models\ExamProctoring;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Room;
use Illuminate\Http\Request;

class LichThiCoiThiController extends Controller
{
    public function lichthi(){
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();
        
        $exams = Exam::with(['course', 'room', 'academicYear', 'semester', 'proctorings.lecture'])
            ->where('academic_year_id', $currentYear->id ?? null)
            ->where('semester_id', $currentSemester->id ?? null)
            ->where('status', 'approved')
            ->whereHas('course', function($query) {
                $query->whereHas('department', function($q) {
                    $q->where('faculty_id', auth()->user()->faculty_id);
                });
            })
            ->orderBy('exam_start')
            ->get();
            
        return view('truongkhoa.lichthicoithi.lichthi', compact(
            'exams', 'currentYear', 'currentSemester'
        ));
    }
    
    public function coithi(){
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();
        
        $exams = Exam::with(['course', 'room', 'academicYear', 'semester', 'proctorings.lecture'])
            ->where('academic_year_id', $currentYear->id ?? null)
            ->where('semester_id', $currentSemester->id ?? null)
            ->where('status', 'approved')
            ->whereHas('course', function($query) {
                $query->whereHas('department', function($q) {
                    $q->where('faculty_id', auth()->user()->faculty_id);
                });
            })
            ->get();
            
        $lecturers = Lecture::with(['department'])
            ->whereHas('department', function($query) {
                $query->where('faculty_id', auth()->user()->faculty_id);
            })
            ->where('status_id', 1) 
            ->get();
            
        return view('truongkhoa.lichthicoithi.coithi', compact(
            'exams', 'lecturers', 'currentYear', 'currentSemester'
        ));
    }
    
    public function approveExam($id)
    {
        try {
            $exam = Exam::findOrFail($id);
            
            if ($exam->course->department->faculty_id != auth()->user()->faculty_id) {
                return back()->with('error', 'Bạn không có quyền duyệt kỳ thi này!');
            }
            
            $exam->update(['status' => 'approved']);
            
            return back()->with('success', 'Duyệt kỳ thi thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    public function rejectExam(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);
        
        try {
            $exam = Exam::findOrFail($id);
            
            if ($exam->course->department->faculty_id != auth()->user()->faculty_id) {
                return back()->with('error', 'Bạn không có quyền từ chối kỳ thi này!');
            }
            
            $exam->update([
                'status' => 'rejected',
                'notes' => $exam->notes . "\nLý do từ chối: " . $request->rejection_reason
            ]);
            
            return back()->with('success', 'Từ chối kỳ thi thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    public function assignProctoring(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'lecture_id' => 'required|exists:lectures,id',
            'assignment_type' => 'required|in:main,assistant',
            'proctor_order' => 'nullable|integer|min:1'
        ]);
        
        try {
            $exam = Exam::findOrFail($request->exam_id);
            
            
            if ($exam->course->department->faculty_id != auth()->user()->faculty_id) {
                return back()->with('error', 'Bạn không có quyền phân công coi thi cho kỳ thi này!');
            }
            
            $lecturer = Lecture::findOrFail($request->lecture_id);
            if ($lecturer->department->faculty_id != auth()->user()->faculty_id) {
                return back()->with('error', 'Giảng viên không thuộc khoa của bạn!');
            }
            
            $conflict = ExamProctoring::where('lecture_id', $request->lecture_id)
                ->whereHas('exam', function($query) use ($exam) {
                    $query->where(function($q) use ($exam) {
                        $q->whereBetween('exam_start', [$exam->exam_start, $exam->exam_end])
                          ->orWhereBetween('exam_end', [$exam->exam_start, $exam->exam_end])
                          ->orWhere(function($subQ) use ($exam) {
                              $subQ->where('exam_start', '<=', $exam->exam_start)
                                   ->where('exam_end', '>=', $exam->exam_end);
                          });
                    });
                })
                ->exists();
                
            if ($conflict) {
                return back()->with('error', 'Giảng viên đã có lịch coi thi trong khoảng thời gian này!');
            }
            
            if ($request->assignment_type == 'main') {
                $mainProctors = ExamProctoring::where('exam_id', $request->exam_id)
                    ->where('assignment_type', 'main')
                    ->count();
                    
                if ($mainProctors >= 1) {
                    return back()->with('error', 'Kỳ thi này đã có giảng viên coi thi chính!');
                }
            }
            
            ExamProctoring::create([
                'exam_id' => $request->exam_id,
                'lecture_id' => $request->lecture_id,
                'assignment_type' => $request->assignment_type,
                'proctor_order' => $request->proctor_order ?? 1,
                'status' => 'assigned'
            ]);
            
            return back()->with('success', 'Phân công coi thi thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    public function removeProctoring($id)
    {
        try {
            $proctoring = ExamProctoring::findOrFail($id);
            
            if ($proctoring->exam->course->department->faculty_id != auth()->user()->faculty_id) {
                return back()->with('error', 'Bạn không có quyền thực hiện thao tác này!');
            }
            
            $proctoring->delete();
            
            return back()->with('success', 'Hủy phân công coi thi thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    public function updateProctoring(Request $request, $id)
    {
        $request->validate([
            'assignment_type' => 'required|in:main,assistant',
            'proctor_order' => 'nullable|integer|min:1'
        ]);
        
        try {
            $proctoring = ExamProctoring::findOrFail($id);
            
            if ($proctoring->exam->course->department->faculty_id != auth()->user()->faculty_id) {
                return back()->with('error', 'Bạn không có quyền thực hiện thao tác này!');
            }
            
            if ($request->assignment_type == 'main') {
                $mainProctors = ExamProctoring::where('exam_id', $proctoring->exam_id)
                    ->where('assignment_type', 'main')
                    ->where('id', '!=', $id)
                    ->count();
                    
                if ($mainProctors >= 1) {
                    return back()->with('error', 'Kỳ thi này đã có giảng viên coi thi chính!');
                }
            }
            
            $proctoring->update([
                'assignment_type' => $request->assignment_type,
                'proctor_order' => $request->proctor_order ?? 1
            ]);
            
            return back()->with('success', 'Cập nhật phân công coi thi thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    public function getLecturerSchedule($lecturerId, $examId)
    {
        $exam = Exam::findOrFail($examId);
        
        $conflicts = ExamProctoring::where('lecture_id', $lecturerId)
            ->whereHas('exam', function($query) use ($exam) {
                $query->where(function($q) use ($exam) {
                    $q->whereBetween('exam_start', [$exam->exam_start, $exam->exam_end])
                      ->orWhereBetween('exam_end', [$exam->exam_start, $exam->exam_end])
                      ->orWhere(function($subQ) use ($exam) {
                          $subQ->where('exam_start', '<=', $exam->exam_start)
                               ->where('exam_end', '>=', $exam->exam_end);
                      });
                });
            })
            ->with('exam.course')
            ->get();
            
        return response()->json(['conflicts' => $conflicts]);
    }
}
