<?php

namespace App\Http\Controllers\TruongKhoa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OutlineProgramCourseController extends Controller
{
    // === Hiển thị khung CTĐT theo version_id ===
    public function index($version_id)
    {
        $version = DB::table('outline_program_versions')
            ->join('education_programs', 'outline_program_versions.education_program_id', '=', 'education_programs.id')
            ->select(
                'outline_program_versions.*',
                'education_programs.program_name as program_name',
                'education_programs.program_code as program_code'
            )
            ->where('outline_program_versions.id', $version_id)
            ->first();

        $courses = DB::table('outline_program_courses as opc')
            ->join('courses as c', 'opc.course_id', '=', 'c.id')
            ->select(
                'opc.id',
                'opc.course_id',
                'opc.program_version_id',
                'opc.knowledge_type',
                'opc.semester_no',
                'opc.is_compulsory',
                'opc.credit_theory',
                'opc.credit_practice',
                'opc.credit_total',
                'opc.course_group',
                'opc.note',
                'c.course_code',
                'c.course_name'
            )
            ->where('opc.program_version_id', $version_id)
            ->orderBy('opc.semester_no')
            ->orderBy('opc.order_in_semester')
            ->get();

        // Dữ liệu cho dropdown thêm mới
        $courseOptions = DB::table('courses')
            ->select('id', 'course_name', 'course_code')
            ->orderBy('course_name')
            ->get();

        return view('truongkhoa.ctdt_khung_crud', compact('version', 'courses', 'courseOptions'))
            ->with('title', 'Khung CTĐT')
            ->with('layout', 'layouts.apptruongkhoa');
    }

    // === Lưu hoặc cập nhật học phần trong khung ===
    // public function store(Request $r, $version_id)
    public function store(Request $r, $version_id)
    {
        try {
            $data = $r->validate([
                'course_id'        => 'required|integer',
                'knowledge_type'   => 'nullable|string',
                'is_compulsory'    => 'nullable|boolean',
                'semester_no'      => 'nullable|integer',
                'credit_theory'    => 'nullable|integer',
                'credit_practice'  => 'nullable|integer',
                'course_group'     => 'nullable|string',
                'note'             => 'nullable|string',
            ]);

            // 🔹 Bổ sung mặc định an toàn cho các cột không cho phép NULL
            $data['is_compulsory']   = $data['is_compulsory'] ?? 1;
            $data['semester_no']     = $data['semester_no'] ?? 1;
            $data['credit_theory']   = $data['credit_theory'] ?? 0;
            $data['credit_practice'] = $data['credit_practice'] ?? 0;
            // $data['credit_total']    = ($data['credit_theory'] ?? 0) + ($data['credit_practice'] ?? 0);

            // 🔹 Metadata & version
            $data['program_version_id'] = $version_id;
            $data['updated_by'] = Auth::id();
            $data['updated_at'] = now();

            if ($r->id) {
                // === Cập nhật học phần
                DB::table('outline_program_courses')->where('id', $r->id)->update($data);
            } else {
                // === Thêm mới học phần
                $data['created_by'] = Auth::id();
                $data['created_at'] = now();
                DB::table('outline_program_courses')->insert($data);
            }

            return response()->json([
                'success' => true,
                'message' => 'Lưu thành công!',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
        }
    }



    // === Xóa nhiều học phần ===
    public function destroyMultiple(Request $r, $version_id)
    {
        DB::table('outline_program_courses')
            ->where('program_version_id', $version_id)
            ->whereIn('id', $r->ids ?? [])
            ->delete();

        return response()->json(['success' => true]);
    }
}
