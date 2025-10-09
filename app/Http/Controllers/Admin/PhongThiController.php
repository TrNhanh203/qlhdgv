<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhongThiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $rooms = Room::where('university_id', $user->university_id)->get();
        $exams = Exam::with('room','course')
            ->where('academic_year_id', session('academic_year_id'))
            ->where('semester_id', session('semester_id'))
            ->get();

        return view('admin.phongthi.index', compact('rooms','exams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'room_id' => 'required|exists:rooms,id',
            'exam_start' => 'required|date',
            'exam_end' => 'required|date|after:exam_start',
            'expected_students' => 'required|integer|min:1',
        ]);

        $conflict = Exam::where('room_id', $request->room_id)
            ->where(function($q) use ($request){
                $q->whereBetween('exam_start', [$request->exam_start,$request->exam_end])
                  ->orWhereBetween('exam_end', [$request->exam_start,$request->exam_end])
                  ->orWhere(function($q2) use ($request){
                      $q2->where('exam_start','<=',$request->exam_start)
                         ->where('exam_end','>=',$request->exam_end);
                  });
            })->exists();

        if($conflict){
            return response()->json([
                'success' => false,
                'errors' => ['room_id'=>['Phòng này đã được sử dụng trong thời gian này']]
            ]);
        }

        $exam = Exam::updateOrCreate(
            ['id'=>$request->id],
            [
                'course_id'=>$request->course_id,
                'academic_year_id'=>session('academic_year_id'),
                'semester_id'=>session('semester_id'),
                'room_id'=>$request->room_id,
                'exam_start'=>$request->exam_start,
                'exam_end'=>$request->exam_end,
                'expected_students'=>$request->expected_students,
                'exam_name'=>$request->exam_name,
                'exam_type'=>$request->exam_type,
            ]
        );

        return response()->json(['success'=>true,'exam'=>$exam]);
    }

    public function destroy($id)
    {
        Exam::findOrFail($id)->delete();
        return response()->json(['success'=>true]);
    }
}
