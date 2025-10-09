<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Department;
use App\Models\EducationProgram;

class HocPhanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $universityId = $user->university_id;

        $courses = Course::query()
            ->join('education_programs', 'courses.education_program_id', '=', 'education_programs.id')
            ->join('faculties', 'education_programs.faculty_id', '=', 'faculties.id')
            ->join('universities', 'faculties.university_id', '=', 'universities.id')
            ->where('universities.id', $universityId)
            ->select('courses.*')
            ->get();

        $programs = EducationProgram::query()
            ->join('faculties', 'education_programs.faculty_id', '=', 'faculties.id')
            ->join('universities', 'faculties.university_id', '=', 'universities.id')
            ->where('universities.id', $universityId)
            ->select('education_programs.*')
            ->get();

        return view('admin.hocphan.index', compact('courses', 'programs', 'universityId'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'course_code' => 'required|string|max:50',
            'course_name' => 'required|string|max:255',
            'credit' => 'required|numeric|min:0',
            'education_program_id' => 'required|exists:education_programs,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        $course = Course::updateOrCreate(
            ['id' => $request->id],
            [
                'course_code' => $request->course_code,
                'course_name' => $request->course_name,
                'credit' => $request->credit,
                'education_program_id' => $request->education_program_id,
                'department_id' => $request->department_id,
            ]
        );

        return response()->json([
            'success' => true,
            'course' => [
                'id' => $course->id,
                'course_code' => $course->course_code,
                'course_name' => $course->course_name,
                'credit' => $course->credit,
                'education_program_id' => $course->education_program_id,
                'department_id' => $course->department_id,
                'created_at' => $course->created_at->format('d-m-Y H:i'),
                'updated_at' => $course->updated_at->format('d-m-Y H:i'),
            ]
        ]);
    }

    public function destroy($id)
    {
        Course::where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    public function destroyMultiple(Request $request)
    {
        Course::whereIn('id', $request->ids)->delete();
        return response()->json(['success' => true]);
    }
}
