<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faculty;
use App\Models\Lecture;
use App\Models\Room;
use App\Models\Course;
use App\Models\University;
use App\Models\Exam;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\OutlineProgramCourse;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $university = $user->university_id ? University::find($user->university_id) : null;
        $universityLogo = $this->getUniversityLogoSafe($university);
        $universityDescription = $university?->description;
        $universityCodeShort = null;
        if ($university && $university->email) {
            if (preg_match('/@([^.]+)\./', $university->email, $matches)) {
                $universityCodeShort = strtoupper($matches[1]);
            }
        }

        $totalDepartments = $user->university_id
            ? Department::whereIn('faculty_id', Faculty::where('university_id', $user->university_id)->pluck('id'))->count()
            : Department::count();

        $totalCourses = $user->university_id
            ? Course::whereIn('department_id', Department::whereIn('faculty_id', Faculty::where('university_id', $user->university_id)->pluck('id'))->pluck('id'))->count()
            : Course::count();

        $stats = [
            'total_faculties' => $user->university_id ? Faculty::where('university_id', $user->university_id)->count() : Faculty::count(),
            'total_departments' => $totalDepartments,
            'total_lecturers' => $user->university_id ? Lecture::where('university_id', $user->university_id)->count() : Lecture::count(),
            'total_rooms' => $user->university_id ? Room::where('university_id', $user->university_id)->count() : Room::count(),
            'total_courses' => $totalCourses,
            'total_exams' => $this->getExamCount($user->university_id),
        ];

        $giangVienTheoKhoa = DB::table('lectures as l')
            ->join('departments as d', 'l.department_id', '=', 'd.id')
            ->join('faculties as f', 'd.faculty_id', '=', 'f.id')
            ->where('f.university_id', $user->university_id)
            ->select('f.faculty_name as ten_khoa', DB::raw('COUNT(l.id) as giangviens_count'))
            ->groupBy('f.faculty_name')
            ->get();

        $giangVienTheoBoMon = DB::table('lectures as l')
            ->join('departments as d', 'l.department_id', '=', 'd.id')
            ->join('faculties as f', 'd.faculty_id', '=', 'f.id')
            ->where('f.university_id', $user->university_id)
            ->select('d.department_name as bo_mon', DB::raw('COUNT(l.id) as total'))
            ->groupBy('d.department_name')
            ->get();

        $kyThiTheoNam = AcademicYear::select('academic_years.year_code as nam_hoc', DB::raw('COUNT(semesters.id) as total'))
            ->leftJoin('semesters', 'academic_years.id', '=', 'semesters.academic_year_id')
            ->where('academic_years.university_id', $user->university_id)
            ->groupBy('academic_years.id', 'academic_years.year_code')
            ->orderBy('academic_years.year_code')
            ->get();

        // $coursesByYearSemester = DB::table('courses as c')
        //     ->join('departments as d', 'c.department_id', '=', 'd.id')
        //     ->join('faculties as f', 'd.faculty_id', '=', 'f.id')
        //     ->join('semesters as s', 'c.semester_id', '=', 's.id')
        //     ->join('academic_years as ay', 's.academic_year_id', '=', 'ay.id')
        //     ->where('f.university_id', $user->university_id)
        //     ->select('ay.year_code','s.semester_name',DB::raw('COUNT(c.id) as total'))
        //     ->groupBy('ay.year_code', 's.semester_name')
        //     ->orderBy('ay.year_code')
        //     ->orderBy('s.semester_name')
        //     ->get();
        $coursesByYearSemester = DB::table('outline_program_courses as opc')
            ->join('outline_program_versions as v', 'opc.program_version_id', '=', 'v.id')
            ->join('education_programs as ep', 'v.education_program_id', '=', 'ep.id')
            ->join('faculties as f', 'ep.faculty_id', '=', 'f.id')
            ->leftJoin('semesters as s', 'opc.semester_id', '=', 's.id')
            ->leftJoin('academic_years as ay', 'opc.academic_year_id', '=', 'ay.id')
            ->where('f.university_id', $user->university_id)
            ->select('ay.year_code', 's.semester_name', DB::raw('COUNT(opc.id) as total'))
            ->groupBy('ay.year_code', 's.semester_name')
            ->orderBy('ay.year_code')->orderBy('s.semester_name')
            ->get();




        // $coursesBySemester = DB::table('courses as c')
        //     ->join('departments as d', 'c.department_id', '=', 'd.id')
        //     ->join('faculties as f', 'd.faculty_id', '=', 'f.id')
        //     ->join('semesters as s', 'c.semester_id', '=', 's.id')
        //     ->where('f.university_id', $user->university_id)
        //     ->select('s.semester_name', DB::raw('COUNT(c.id) as total'))
        //     ->groupBy('s.semester_name')
        //     ->get();

        $coursesBySemester = DB::table('outline_program_courses as opc')
            ->join('outline_program_versions as v', 'opc.program_version_id', '=', 'v.id')
            ->join('education_programs as ep', 'v.education_program_id', '=', 'ep.id')
            ->join('faculties as f', 'ep.faculty_id', '=', 'f.id')
            ->leftJoin('semesters as s', 'opc.semester_id', '=', 's.id')
            ->where('f.university_id', $user->university_id)
            ->select('s.semester_name', DB::raw('COUNT(opc.id) as total'))
            ->groupBy('s.semester_name')
            ->get();



        $lecturersAssignedByCourse = DB::table('teaching_duties as td')
            ->join('courses as c', 'td.course_id', '=', 'c.id')
            ->join('departments as d', 'c.department_id', '=', 'd.id')
            ->join('faculties as f', 'd.faculty_id', '=', 'f.id')
            ->where('f.university_id', $user->university_id)
            ->select('c.course_name', DB::raw('COUNT(td.lecture_id) as total'))
            ->groupBy('c.course_name')
            ->get();

        $lecturersByDept = DB::table('lectures as l')
            ->join('departments as d', 'l.department_id', '=', 'd.id')
            ->join('faculties as f', 'd.faculty_id', '=', 'f.id')
            ->where('f.university_id', $user->university_id)
            ->select('d.department_name as department', DB::raw('COUNT(l.id) as total'))
            ->groupBy('d.department_name')
            ->get();

        $departmentsByFaculty = DB::table('departments as d')
            ->join('faculties as f', 'd.faculty_id', '=', 'f.id')
            ->where('f.university_id', $user->university_id)
            ->select('f.faculty_name as faculty', DB::raw('COUNT(d.id) as total'))
            ->groupBy('f.faculty_name')
            ->get();

        // $semestersByYear = DB::table('academic_years as ay')
        // ->join('semesters as s', 's.academic_year_id', '=', 'ay.id')
        // ->leftJoin('courses as c', 'c.semester_id', '=', 's.id')
        // ->where('ay.university_id', $user->university_id)
        // ->select('ay.year_code','s.semester_name', DB::raw('COUNT(c.id) as total'))
        // ->groupBy('ay.year_code','s.semester_name')
        // ->orderBy('ay.year_code')
        // ->orderBy('s.semester_name')
        // ->get();
        $semestersByYear = DB::table('academic_years as ay')
            ->join('semesters as s', 's.academic_year_id', '=', 'ay.id')
            ->leftJoin('outline_program_courses as opc', 'opc.semester_id', '=', 's.id')
            ->leftJoin('outline_program_versions as v', 'opc.program_version_id', '=', 'v.id')
            ->leftJoin('education_programs as ep', 'v.education_program_id', '=', 'ep.id')
            ->leftJoin('faculties as f', 'ep.faculty_id', '=', 'f.id')
            ->where('ay.university_id', $user->university_id)   // có thể giữ thêm ->where('f.university_id', ...) nếu muốn
            ->select('ay.year_code', 's.semester_name', DB::raw('COUNT(opc.id) as total'))
            ->groupBy('ay.year_code', 's.semester_name')
            ->orderBy('ay.year_code')->orderBy('s.semester_name')
            ->get();


        // $semestersListByYear = DB::table('academic_years as ay')
        // ->leftJoin('semesters as s', 's.academic_year_id', '=', 'ay.id')
        // ->where('ay.university_id', $user->university_id)
        // ->select('ay.year_code','s.semester_name')
        // ->orderBy('ay.year_code')
        // ->orderBy('s.semester_name')
        // ->get();
        $semestersListByYear = DB::table('academic_years as ay')
            ->leftJoin('semesters as s', 's.academic_year_id', '=', 'ay.id')
            ->where('ay.university_id', $user->university_id) // nếu có cột này
            ->select('ay.year_code', 's.semester_name')
            ->orderBy('ay.year_code')
            ->orderBy('s.semester_name')
            ->get();



        return view('admin.dashboard', compact(
            'semestersByYear',
            'university',
            'user',
            'universityLogo',
            'universityDescription',
            'stats',
            'giangVienTheoKhoa',
            'giangVienTheoBoMon',
            'kyThiTheoNam',
            'coursesBySemester',
            'lecturersAssignedByCourse',
            'lecturersByDept',
            'departmentsByFaculty',
            'universityCodeShort',
            'coursesByYearSemester',
            'semestersListByYear'
        ));
    }


    private function getUniversityLogoSafe($university)
    {
        if (!$university || !$university->university_name) {
            return asset('logos/default.png');
        }

        if ($university->logo) {
            return asset($university->logo);
        }

        try {
            $apiKey = env('GOOGLE_API_KEY');
            $cseId = env('GOOGLE_CSE_ID');
            $query = urlencode($university->university_name . ' logo');
            $url = "https://www.googleapis.com/customsearch/v1?q={$query}&cx={$cseId}&key={$apiKey}&searchType=image&num=1";

            $response = file_get_contents($url);
            if ($response) {
                $data = json_decode($response, true);
                if (isset($data['items'][0]['link'])) {
                    return $data['items'][0]['link'];
                }
            }
        } catch (\Exception $e) {
        }

        return asset('logos/default.png');
    }

    public function update(Request $request, $id)
    {
        $university = University::findOrFail($id);
        $university->update($request->only([
            'university_name',
            'university_type',
            'address',
            'phone',
            'email',
            'website',
            'fanpage',
            'founded_date',
            'status_id',
            'description'
        ]));

        return response()->json(['success' => true]);
    }
    public function updateUniversity(Request $request, $id)
    {
        try {
            $university = University::findOrFail($id);

            $request->validate([
                'university_name' => 'required|string|max:255',
                'university_type' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:255',
                'website' => 'nullable|string|max:255',
                'fanpage' => 'nullable|string|max:255',
                'founded_date' => 'nullable|date',
                'status_id' => 'nullable|in:1,2,3,4',
                'description' => 'nullable|string|max:1000',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('logo')) {
                $file = $request->file('logo');

                $filename = time() . '_' . $file->getClientOriginalName();

                $file->move(public_path('logos'), $filename);

                if ($university->logo && file_exists(public_path($university->logo))) {
                    unlink(public_path($university->logo));
                }

                $university->logo = 'logos/' . $filename;
            }


            $fields = [
                'university_name',
                'university_type',
                'address',
                'phone',
                'email',
                'website',
                'fanpage',
                'founded_date',
                'status_id',
                'description'
            ];
            foreach ($fields as $field) {
                if ($request->has($field)) {
                    $university->$field = $request->$field;
                }
            }

            $university->save();

            return response()->json(['success' => true, 'message' => 'Cập nhật thành công!']);
        } catch (\Exception $e) {
            \Log::error('Update University Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra!'], 500);
        }
    }

    public function updateLogo(Request $request, $id)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:1000',
        ]);

        $university = University::findOrFail($id);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('logos'), $filename);


            if ($university->logo && file_exists(public_path($university->logo))) {
                unlink(public_path($university->logo));
            }

            $university->logo = 'logos/' . $filename;
        }

        $university->description = $request->description;
        $university->save();

        return redirect()->back()->with('success', 'Cập nhật thông tin trường thành công!');
    }


    public function deleteLogo($id)
    {
        $university = University::findOrFail($id);

        if ($university->logo && Storage::exists(str_replace('storage/', 'public/', $university->logo))) {
            Storage::delete(str_replace('storage/', 'public/', $university->logo));
        }

        $university->logo = null;
        $university->save();

        return redirect()->back()->with('success', 'Xóa logo thành công!');
    }

    private function getExamCount($universityId)
    {
        if (!$universityId) return Exam::count();

        if (Schema::hasColumn('exams', 'university_id')) {
            return Exam::where('university_id', $universityId)->count();
        }

        return DB::table('exams as e')
            ->join('courses as c', 'e.course_id', '=', 'c.id')
            ->join('departments as d', 'c.department_id', '=', 'd.id')
            ->join('faculties as f', 'd.faculty_id', '=', 'f.id')
            ->where('f.university_id', $universityId)
            ->count();
    }
}
