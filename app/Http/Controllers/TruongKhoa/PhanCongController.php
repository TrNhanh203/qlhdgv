<?php

namespace App\Http\Controllers\TruongKhoa;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lecture;
use App\Models\TeachingDuty;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhanCongController extends Controller
{
    public function phancong(){
        $currentYear = AcademicYear::orderBy('id', 'desc')->first();
        $currentSemester = Semester::orderBy('id', 'desc')->first();
        
        $courses = Course::with(['department', 'educationProgram'])
            ->where('semester_id', $currentSemester->id ?? null)
            ->get();
            
        $lecturers = Lecture::with(['department', 'teachingDuties' => function($query) use ($currentYear, $currentSemester) {
                $query->where('academic_year_id', $currentYear->id ?? null)
                      ->where('semester_id', $currentSemester->id ?? null);
            }])
            ->where('department_id', auth()->user()->department_id)
            ->where('status_id', 1) 
            ->get();
            
        $rooms = Room::where('status', 'active')->get();
        
        $currentAssignments = TeachingDuty::with(['lecture', 'course', 'room'])
            ->where('academic_year_id', $currentYear->id ?? null)
            ->where('semester_id', $currentSemester->id ?? null)
            ->get();
            
        return view('truongkhoa.phanconggiangday.phancong', compact(
            'courses', 'lecturers', 'rooms', 'currentAssignments', 'currentYear', 'currentSemester'
        ));
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
                'status' => 'pending' 
            ]);
            
            return back()->with('success', 'Phân công giảng dạy thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
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
                'notes' => $request->notes,
                'status' => 'pending' 
            ]);
            
            return back()->with('success', 'Cập nhật phân công giảng dạy thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    public function destroy($id)
    {
        try {
            $assignment = TeachingDuty::findOrFail($id);
            $assignment->delete();
            
            return back()->with('success', 'Xóa phân công giảng dạy thành công!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    public function getLecturerWorkload($lecturerId, $semesterId)
    {
        $totalHours = TeachingDuty::where('lecture_id', $lecturerId)
            ->where('semester_id', $semesterId)
            ->sum('hours');
            
        return response()->json(['total_hours' => $totalHours]);
    }
}
