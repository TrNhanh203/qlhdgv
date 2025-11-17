<?php

namespace App\Http\Controllers\TruongBoMon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TruongBoMonOutlineAssignmentController extends Controller
{
    /**
     * Danh sách các phiên bản đề cương thuộc bộ môn để phân công.
     */
    public function index(\Illuminate\Http\Request $request)
    {


        // Lấy program_course_id cho học phần trong CTĐT
        $programCourse = null;
        $initialAssignments = 0;

        if (!empty($selectedProgramVersionId) && !empty($selectedCourseId)) {
            $programCourse = DB::table('outline_program_courses')
                ->where('program_version_id', $selectedProgramVersionId)
                ->where('course_id', $selectedCourseId)
                ->first();

            if ($programCourse) {
                $initialAssignments = DB::table('outline_course_assignments')
                    ->where('program_course_id', $programCourse->id)
                    ->whereNull('outline_course_version_id')
                    ->count();
            }
        }


        $user = \Illuminate\Support\Facades\Auth::user();
        $lectureId = $user->lecture_id ?? null;

        if (!$lectureId) {
            abort(403, 'Tài khoản hiện tại không gắn với giảng viên.');
        }

        // 📌 Bộ môn mà TBM đang phụ trách
        $departmentId = \Illuminate\Support\Facades\DB::table('lecture_roles')
            ->where('lecture_id', $lectureId)
            ->whereNotNull('department_id')
            ->orderByDesc('start_date')
            ->value('department_id');

        if (!$departmentId) {
            abort(403, 'Không xác định được bộ môn của bạn.');
        }

        // 📌 Danh sách các KHÓA CTĐT (outline_program_versions)
        // chỉ lấy những khóa có ít nhất 1 học phần thuộc bộ môn này
        $programVersions = \Illuminate\Support\Facades\DB::table('outline_program_versions as opv')
            ->join('education_programs as ep', 'opv.education_program_id', '=', 'ep.id')
            ->join('outline_program_courses as opc', 'opv.id', '=', 'opc.program_version_id')
            ->join('courses as c', 'opc.course_id', '=', 'c.id')
            ->where('c.department_id', $departmentId)
            ->select(
                'opv.id',
                'opv.version_code',
                'ep.program_code',
                'ep.program_name'
            )
            ->distinct()
            ->orderBy('ep.program_code')
            ->orderBy('opv.version_code')
            ->get();

        $selectedProgramVersionId = $request->query('program_version_id');
        $selectedCourseId         = $request->query('course_id');

        // 📌 Danh sách học phần thuộc KHÓA CTĐT được chọn (nếu có)
        $coursesInProgram = collect();
        if (!empty($selectedProgramVersionId)) {
            $coursesInProgram = \Illuminate\Support\Facades\DB::table('outline_program_courses as opc')
                ->join('courses as c', 'opc.course_id', '=', 'c.id')
                ->where('opc.program_version_id', $selectedProgramVersionId)
                ->where('c.department_id', $departmentId)
                ->select('c.id', 'c.course_code', 'c.course_name')
                ->orderBy('c.course_code')
                ->orderBy('c.course_name')
                ->get();
        }

        // 📌 Danh sách các phiên bản đề cương (outline_course_versions)
        $outlineVersions = collect();

        if (!empty($selectedProgramVersionId)) {
            $query = \Illuminate\Support\Facades\DB::table('outline_course_versions as ocv')
                ->join('outline_program_courses as opc', 'ocv.program_course_id', '=', 'opc.id')
                ->join('outline_program_versions as opv', 'opc.program_version_id', '=', 'opv.id')
                ->join('education_programs as ep', 'opv.education_program_id', '=', 'ep.id')
                ->join('courses as c', 'opc.course_id', '=', 'c.id')
                ->leftJoin('semesters as s', 'opc.semester_id', '=', 's.id')
                ->leftJoin('academic_years as ay', 'opc.academic_year_id', '=', 'ay.id')
                ->where('c.department_id', $departmentId)
                ->where('opv.id', $selectedProgramVersionId);

            if (!empty($selectedCourseId)) {
                $query->where('c.id', $selectedCourseId);
            }

            $outlineVersions = $query
                ->select(
                    'ocv.id',
                    'ocv.version_no',
                    'ocv.status',
                    'c.id as course_id',
                    'c.course_code',
                    'c.course_name',
                    'ep.program_code',
                    'ep.program_name',
                    'opv.version_code as program_version_code',
                    's.semester_name',
                    'ay.year_code'
                )
                ->orderBy('c.course_code')
                ->orderByDesc('ocv.version_no')
                ->get();
        }

        // 📌 Đếm số phân công / đề cương
        $assignmentCounts = collect();
        if ($outlineVersions->isNotEmpty()) {
            $assignmentCounts = \Illuminate\Support\Facades\DB::table('outline_course_assignments')
                ->select('outline_course_version_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'))
                ->whereIn('outline_course_version_id', $outlineVersions->pluck('id'))
                ->groupBy('outline_course_version_id')
                ->pluck('total', 'outline_course_version_id');
        }

        return view('truongbomon.outline_assign_index', [
            'programVersions'        => $programVersions,
            'selectedProgramVersion' => $selectedProgramVersionId,
            'coursesInProgram'       => $coursesInProgram,
            'selectedCourseId'       => $selectedCourseId,
            'outlineVersions'        => $outlineVersions,
            'assignmentCounts'       => $assignmentCounts,

            'programCourse'          => $programCourse,
            'initialAssignments'     => $initialAssignments,
        ]);
    }



    /**
     * Màn hình phân công chi tiết cho 1 phiên bản đề cương.
     */
    public function edit($outlineCourseVersionId)
    {
        $user = Auth::user();
        $lectureId = $user->lecture_id ?? null;

        if (!$lectureId) {
            abort(403, 'Tài khoản hiện tại không gắn với giảng viên.');
        }

        $departmentId = DB::table('lecture_roles')
            ->where('lecture_id', $lectureId)
            ->whereNotNull('department_id')
            ->orderByDesc('start_date')
            ->value('department_id');

        if (!$departmentId) {
            abort(403, 'Không xác định được bộ môn của bạn.');
        }

        $outline = DB::table('outline_course_versions as ocv')
            ->join('outline_program_courses as opc', 'ocv.program_course_id', '=', 'opc.id')
            ->join('outline_program_versions as opv', 'opc.program_version_id', '=', 'opv.id')
            ->join('education_programs as ep', 'opv.education_program_id', '=', 'ep.id')
            ->join('courses as c', 'opc.course_id', '=', 'c.id')
            ->leftJoin('semesters as s', 'opc.semester_id', '=', 's.id')
            ->leftJoin('academic_years as ay', 'opc.academic_year_id', '=', 'ay.id')
            ->where('ocv.id', $outlineCourseVersionId)
            ->where('c.department_id', $departmentId)
            ->select(
                'ocv.id',
                'ocv.version_no',
                'ocv.status',
                'c.course_code',
                'c.course_name',
                'ep.program_code',
                'ep.program_name',
                'opv.version_code as program_version_code',
                's.semester_name',
                'ay.year_code'
            )
            ->first();

        if (!$outline) {
            abort(404, 'Không tìm thấy đề cương hoặc không thuộc bộ môn của bạn.');
        }

        // Giảng viên thuộc bộ môn
        $lecturers = DB::table('lectures')
            ->where('department_id', $departmentId)
            ->orderBy('full_name')
            ->get();

        // Phân công hiện tại
        $existingAssignments = DB::table('outline_course_assignments')
            ->where('outline_course_version_id', $outlineCourseVersionId)
            ->get()
            ->keyBy('lecture_id');

        return view('truongbomon.outline_assign_edit', [
            'outline'             => $outline,
            'lecturers'           => $lecturers,
            'existingAssignments' => $existingAssignments,
        ]);
    }

    /**
     * Lưu phân công cho 1 phiên bản đề cương (AJAX JSON).
     */
    public function save(Request $request, $outlineCourseVersionId)
    {
        $user = Auth::user();
        $lectureId = $user->lecture_id ?? null;

        if (!$lectureId) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản hiện tại không gắn với giảng viên.',
            ], 403);
        }

        $departmentId = DB::table('lecture_roles')
            ->where('lecture_id', $lectureId)
            ->whereNotNull('department_id')
            ->orderByDesc('start_date')
            ->value('department_id');

        if (!$departmentId) {
            return response()->json([
                'success' => false,
                'message' => 'Không xác định được bộ môn của bạn.',
            ], 403);
        }

        // Check đề cương có thuộc bộ môn không
        $exists = DB::table('outline_course_versions as ocv')
            ->join('outline_program_courses as opc', 'ocv.program_course_id', '=', 'opc.id')
            ->join('courses as c', 'opc.course_id', '=', 'c.id')
            ->where('ocv.id', $outlineCourseVersionId)
            ->where('c.department_id', $departmentId)
            ->exists();

        if (!$exists) {
            return response()->json([
                'success' => false,
                'message' => 'Đề cương không thuộc bộ môn của bạn.',
            ], 403);
        }

        $assignments = $request->input('assignments', []);

        if (!is_array($assignments) || empty($assignments)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có dữ liệu phân công.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Xoá tất cả phân công cũ của đề cương này
            DB::table('outline_course_assignments')
                ->where('outline_course_version_id', $outlineCourseVersionId)
                ->delete();

            foreach ($assignments as $item) {
                $lecId   = $item['lecture_id'] ?? null;
                $role    = $item['role'] ?? null;
                $dueDate = $item['due_date'] ?? null;
                $note    = $item['note'] ?? null;

                if (!$lecId || !$role) {
                    throw new \Exception('Thiếu giảng viên hoặc vai trò trong dữ liệu phân công.');
                }

                // Đảm bảo giảng viên thuộc bộ môn của TBM
                $validLecture = DB::table('lectures')
                    ->where('id', $lecId)
                    ->where('department_id', $departmentId)
                    ->exists();

                if (!$validLecture) {
                    throw new \Exception('Giảng viên ID ' . $lecId . ' không thuộc bộ môn của bạn.');
                }

                DB::table('outline_course_assignments')->insert([
                    'outline_course_version_id' => $outlineCourseVersionId,
                    'lecture_id'                => $lecId,
                    'assigned_by'               => $user->id,
                    'role'                      => $role,
                    'status'                    => 'assigned',
                    'note'                      => $note,
                    'due_date'                  => $dueDate ?: null,
                    'assigned_at'               => now(),
                    'created_at'                => now(),
                    'updated_at'                => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã lưu phân công soạn đề cương.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function assignNew(Request $request)
    {
        $user = Auth::user();
        $lectureId = $user->lecture_id ?? null;

        if (!$lectureId) {
            abort(403, 'Tài khoản hiện tại không gắn với giảng viên.');
        }

        $departmentId = DB::table('lecture_roles')
            ->where('lecture_id', $lectureId)
            ->whereNotNull('department_id')
            ->orderByDesc('start_date')
            ->value('department_id');

        if (!$departmentId) {
            abort(403, 'Không xác định được bộ môn của bạn.');
        }

        $programVersionId = $request->query('program_version_id');
        $courseId         = $request->query('course_id');

        if (!$programVersionId || !$courseId) {
            abort(400, 'Thiếu tham số khóa CTĐT hoặc học phần.');
        }

        // Tìm dòng trong khung CTĐT
        $programCourse = DB::table('outline_program_courses as opc')
            ->join('outline_program_versions as opv', 'opc.program_version_id', '=', 'opv.id')
            ->join('education_programs as ep', 'opv.education_program_id', '=', 'ep.id')
            ->join('courses as c', 'opc.course_id', '=', 'c.id')
            ->leftJoin('semesters as s', 'opc.semester_id', '=', 's.id')
            ->leftJoin('academic_years as ay', 'opc.academic_year_id', '=', 'ay.id')
            ->where('opc.program_version_id', $programVersionId)
            ->where('opc.course_id', $courseId)
            ->where('c.department_id', $departmentId)
            ->select(
                'opc.id as program_course_id',
                'c.course_code',
                'c.course_name',
                'ep.program_code',
                'ep.program_name',
                'opv.version_code as program_version_code',
                's.semester_name',
                'ay.year_code'
            )
            ->first();

        if (!$programCourse) {
            abort(404, 'Không tìm thấy học phần trong CTĐT hoặc không thuộc bộ môn của bạn.');
        }

        // Giảng viên thuộc bộ môn
        $lecturers = DB::table('lectures')
            ->where('department_id', $departmentId)
            ->orderBy('full_name')
            ->get();

        // Các phân công "soạn mới" trước đó (chưa gắn version)
        $existingAssignments = DB::table('outline_course_assignments')
            ->where('program_course_id', $programCourse->program_course_id)
            ->whereNull('outline_course_version_id')
            ->get()
            ->keyBy('lecture_id');

        return view('truongbomon.outline_assign_new', [
            'info'               => $programCourse,  // thông tin học phần + CTĐT
            'lecturers'          => $lecturers,
            'existingAssignments' => $existingAssignments,
        ]);
    }


    public function saveNew(Request $request, $programCourseId)
    {
        $user = Auth::user();
        $lectureId = $user->lecture_id ?? null;

        if (!$lectureId) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản hiện tại không gắn với giảng viên.',
            ], 403);
        }

        $departmentId = DB::table('lecture_roles')
            ->where('lecture_id', $lectureId)
            ->whereNotNull('department_id')
            ->orderByDesc('start_date')
            ->value('department_id');

        if (!$departmentId) {
            return response()->json([
                'success' => false,
                'message' => 'Không xác định được bộ môn của bạn.',
            ], 403);
        }

        // Kiểm tra program_course có thuộc bộ môn không
        $pc = DB::table('outline_program_courses as opc')
            ->join('courses as c', 'opc.course_id', '=', 'c.id')
            ->where('opc.id', $programCourseId)
            ->where('c.department_id', $departmentId)
            ->select('opc.id')
            ->first();

        if (!$pc) {
            return response()->json([
                'success' => false,
                'message' => 'Học phần không thuộc bộ môn của bạn.',
            ], 403);
        }

        $assignments = $request->input('assignments', []);

        if (!is_array($assignments) || empty($assignments)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có dữ liệu phân công.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Xoá tất cả phân công "soạn mới" cũ của học phần này (chưa gắn version)
            DB::table('outline_course_assignments')
                ->where('program_course_id', $programCourseId)
                ->whereNull('outline_course_version_id')
                ->delete();

            foreach ($assignments as $item) {
                $lecId   = $item['lecture_id'] ?? null;
                $role    = $item['role'] ?? null;
                $dueDate = $item['due_date'] ?? null;
                $note    = $item['note'] ?? null;

                if (!$lecId || !$role) {
                    throw new \Exception('Thiếu giảng viên hoặc vai trò trong dữ liệu phân công.');
                }

                // Đảm bảo giảng viên thuộc bộ môn
                $validLecture = DB::table('lectures')
                    ->where('id', $lecId)
                    ->where('department_id', $departmentId)
                    ->exists();

                if (!$validLecture) {
                    throw new \Exception('Giảng viên ID ' . $lecId . ' không thuộc bộ môn của bạn.');
                }

                DB::table('outline_course_assignments')->insert([
                    'program_course_id'         => $programCourseId,
                    'outline_course_version_id' => null, // 🔑 soạn mới, chưa có version
                    'lecture_id'                => $lecId,
                    'assigned_by'               => $user->id,
                    'role'                      => $role,
                    'status'                    => 'assigned',
                    'note'                      => $note,
                    'due_date'                  => $dueDate ?: null,
                    'assigned_at'               => now(),
                    'created_at'                => now(),
                    'updated_at'                => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã lưu phân công soạn mới cho học phần.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
