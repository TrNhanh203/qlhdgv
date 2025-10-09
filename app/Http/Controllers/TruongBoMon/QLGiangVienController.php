<?php

namespace App\Http\Controllers\TruongBoMon;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lecture;
use App\Models\TeachingDuty;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Room;
use Illuminate\Http\Request;

class QLGiangVienController extends Controller
{
    public function dsgiangvien(){
        $lecturers = Lecture::with(['department', 'status'])
            ->where('department_id', auth()->user()->department_id)
            ->get();
            
        return view('truongbomon.quanlygiangvien.dsgiangvien', compact('lecturers'));
    }
    
    public function phanconggiangday(){
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();
        
        $pendingAssignments = TeachingDuty::with(['lecture', 'course', 'room', 'academicYear', 'semester'])
            ->where('status', 'pending')
            ->where('academic_year_id', $currentYear->id ?? null)
            ->where('semester_id', $currentSemester->id ?? null)
            ->whereHas('lecture', function($query) {
                $query->where('department_id', auth()->user()->department_id);
            })
            ->get();
            
        $approvedAssignments = TeachingDuty::with(['lecture', 'course', 'room', 'academicYear', 'semester'])
            ->where('status', 'approved')
            ->where('academic_year_id', $currentYear->id ?? null)
            ->where('semester_id', $currentSemester->id ?? null)
            ->whereHas('lecture', function($query) {
                $query->where('department_id', auth()->user()->department_id);
            })
            ->get();
            
        return view('truongbomon.quanlygiangvien.phanconggiangday', compact(
            'pendingAssignments', 'approvedAssignments', 'currentYear', 'currentSemester'
        ));
    }
    
    public function approveAssignment($id)
    {
        try {
            $assignment = TeachingDuty::findOrFail($id);
            
          
            if ($assignment->lecture->department_id != auth()->user()->department_id) {
                return back()->with('error', 'Bạn không có quyền duyệt phân công này!');
            }
            
            $assignment->update(['status' => 'approved']);
            
            return back()->with('success', 'Duyệt phân công giảng dạy thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    public function rejectAssignment(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);
        
        try {
            $assignment = TeachingDuty::findOrFail($id);
            
           
            if ($assignment->lecture->department_id != auth()->user()->department_id) {
                return back()->with('error', 'Bạn không có quyền từ chối phân công này!');
            }
            
            $assignment->update([
                'status' => 'rejected',
                'notes' => $assignment->notes . "\nLý do từ chối: " . $request->rejection_reason
            ]);
            
            return back()->with('success', 'Từ chối phân công giảng dạy thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    public function theodoitiendo(){
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();
        
        $lecturers = Lecture::with(['department'])
            ->where('department_id', auth()->user()->department_id)
            ->where('status_id', 1)
            ->get()
            ->map(function($lecturer) use ($currentYear, $currentSemester) {
                $totalHours = TeachingDuty::where('lecture_id', $lecturer->id)
                    ->where('academic_year_id', $currentYear->id ?? null)
                    ->where('semester_id', $currentSemester->id ?? null)
                    ->where('status', 'approved')
                    ->sum('hours');
                    
                $completedHours = TeachingDuty::where('lecture_id', $lecturer->id)
                    ->where('academic_year_id', $currentYear->id ?? null)
                    ->where('semester_id', $currentSemester->id ?? null)
                    ->where('status', 'completed')
                    ->sum('hours');
                    
                $lecturer->total_hours = $totalHours;
                $lecturer->completed_hours = $completedHours;
                $lecturer->progress_percentage = $totalHours > 0 ? round(($completedHours / $totalHours) * 100, 2) : 0;
                
                return $lecturer;
            });
            
        $stats = [
            'total_lecturers' => $lecturers->count(),
            'total_hours' => $lecturers->sum('total_hours'),
            'completed_hours' => $lecturers->sum('completed_hours'),
            'average_progress' => $lecturers->avg('progress_percentage')
        ];
            
        return view('truongbomon.quanlygiangvien.theodoitiendo', compact(
            'lecturers', 'stats', 'currentYear', 'currentSemester'
        ));
    }
    
    public function markCompleted($id)
    {
        try {
            $assignment = TeachingDuty::findOrFail($id);
            
            if ($assignment->lecture->department_id != auth()->user()->department_id) {
                return back()->with('error', 'Bạn không có quyền thực hiện thao tác này!');
            }
            
            $assignment->update(['status' => 'completed']);
            
            return back()->with('success', 'Đánh dấu hoàn thành phân công thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
