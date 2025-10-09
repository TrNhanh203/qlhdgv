<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamProctoring;
use App\Models\Exam;
use App\Models\Lecture;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;

class GiangVienCoiThiController extends Controller
{
    public function index()
    {
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();
        
        $proctorings = ExamProctoring::with([
            'exam.course.department',
            'exam.room',
            'exam.academicYear',
            'exam.semester',
            'lecture.department'
        ])
        ->whereHas('exam', function($query) use ($currentYear, $currentSemester) {
            $query->where('academic_year_id', $currentYear->id ?? null)
                  ->where('semester_id', $currentSemester->id ?? null);
        })
        ->orderBy('exam.exam_start')
        ->paginate(20);
        
        $stats = [
            'total_proctorings' => $proctorings->total(),
            'main_proctors' => ExamProctoring::where('assignment_type', 'main')
                ->whereHas('exam', function($query) use ($currentYear, $currentSemester) {
                    $query->where('academic_year_id', $currentYear->id ?? null)
                          ->where('semester_id', $currentSemester->id ?? null);
                })
                ->count(),
            'assistant_proctors' => ExamProctoring::where('assignment_type', 'assistant')
                ->whereHas('exam', function($query) use ($currentYear, $currentSemester) {
                    $query->where('academic_year_id', $currentYear->id ?? null)
                          ->where('semester_id', $currentSemester->id ?? null);
                })
                ->count(),
        ];
        
        return view('admin.giangviencoithi.index', compact('proctorings', 'stats', 'currentYear', 'currentSemester'));
    }

    public function create()
    {
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();
        
        $exams = Exam::with(['course.department', 'room'])
            ->where('academic_year_id', $currentYear->id ?? null)
            ->where('semester_id', $currentSemester->id ?? null)
            ->where('status', 'approved')
            ->whereDoesntHave('proctorings')
            ->get();
            
        $lecturers = Lecture::with('department')
            ->where('status_id', 1)
            ->get();
            
        return view('admin.giangviencoithi.create', compact('exams', 'lecturers', 'currentYear', 'currentSemester'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'lecture_id' => 'required|exists:lectures,id',
            'assignment_type' => 'required|in:main,assistant',
            'proctor_order' => 'nullable|integer|min:1'
        ]);
        
        try {
            $exam = Exam::findOrFail($request->exam_id);
            
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
            
            return redirect()->route('admin.giangviencoithi.index')->with('success', 'Phân công coi thi thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $proctoring = ExamProctoring::with([
            'exam.course.department',
            'exam.room',
            'exam.academicYear',
            'exam.semester',
            'lecture.department'
        ])->findOrFail($id);
        
        return view('admin.giangviencoithi.show', compact('proctoring'));
    }

    public function edit($id)
    {
        $proctoring = ExamProctoring::with(['exam', 'lecture'])->findOrFail($id);
        
        $lecturers = Lecture::with('department')
            ->where('status_id', 1)
            ->get();
            
        return view('admin.giangviencoithi.edit', compact('proctoring', 'lecturers'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'lecture_id' => 'required|exists:lectures,id',
            'assignment_type' => 'required|in:main,assistant',
            'proctor_order' => 'nullable|integer|min:1'
        ]);
        
        try {
            $proctoring = ExamProctoring::findOrFail($id);
            $exam = $proctoring->exam;
            
            $conflict = ExamProctoring::where('lecture_id', $request->lecture_id)
                ->where('id', '!=', $id)
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
                $mainProctors = ExamProctoring::where('exam_id', $proctoring->exam_id)
                    ->where('assignment_type', 'main')
                    ->where('id', '!=', $id)
                    ->count();
                    
                if ($mainProctors >= 1) {
                    return back()->with('error', 'Kỳ thi này đã có giảng viên coi thi chính!');
                }
            }
            
            $proctoring->update([
                'lecture_id' => $request->lecture_id,
                'assignment_type' => $request->assignment_type,
                'proctor_order' => $request->proctor_order ?? 1
            ]);
            
            return redirect()->route('admin.giangviencoithi.index')->with('success', 'Cập nhật phân công coi thi thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $proctoring = ExamProctoring::findOrFail($id);
            $proctoring->delete();
            
            return redirect()->route('admin.giangviencoithi.index')->with('success', 'Xóa phân công coi thi thành công!');
            
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
    
    public function statistics()
    {
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();
        
        $stats = [
            'total_proctorings' => ExamProctoring::whereHas('exam', function($query) use ($currentYear, $currentSemester) {
                $query->where('academic_year_id', $currentYear->id ?? null)
                      ->where('semester_id', $currentSemester->id ?? null);
            })->count(),
            'proctorings_by_type' => ExamProctoring::whereHas('exam', function($query) use ($currentYear, $currentSemester) {
                $query->where('academic_year_id', $currentYear->id ?? null)
                      ->where('semester_id', $currentSemester->id ?? null);
            })
            ->groupBy('assignment_type')
            ->selectRaw('assignment_type, count(*) as count')
            ->get(),
            'most_active_proctors' => ExamProctoring::whereHas('exam', function($query) use ($currentYear, $currentSemester) {
                $query->where('academic_year_id', $currentYear->id ?? null)
                      ->where('semester_id', $currentSemester->id ?? null);
            })
            ->with('lecture')
            ->groupBy('lecture_id')
            ->selectRaw('lecture_id, count(*) as count')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
        ];
        
        return response()->json($stats);
    }
}
