<?php

namespace App\Http\Controllers\TruongKhoa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OutlineProgramCourseController extends Controller
{

    protected array $knowledgeTypeLabels = [
        'kien_thuc_chung' => 'Kiến thức chung',
        'kien_thuc_khoa_hoc_co_ban' => 'Kiến thức khoa học cơ bản',
        'kien_thuc_bo_tro' => 'Kiến thức bổ trợ',
        'kien_thuc_co_so_nganh_lien_nganh' => 'Kiến thức cơ sở ngành / liên ngành',
        'kien_thuc_chuyen_nganh' => 'Kiến thức chuyên ngành',
        'hoc_phan_nghe_nghiep' => 'Học phần nghề nghiệp (trải nghiệm nghề nghiệp)',
        'hoc_phan_thuc_tap_tot_nghiep' => 'Học phần thực tập tốt nghiệp (tập sự nghề nghiệp)',
        'hoc_phan_tot_nghiep' => 'Học phần tốt nghiệp',
        'khoi_kien_thuc_dieu_kien_tot_nghiep' => 'Khối kiến thức điều kiện xét tốt nghiệp',
        'khoi_kien_thuc_ky_su_dac_thu' => 'Khối kiến thức học kỹ sư đặc thù',
        'do_an_thuc_tap' => 'Đồ án / Thực tập',
        'khac' => 'Khác',
    ];
    // === Hiển thị view khung CTĐT demo tĩnh ===
    // public function overview($version_id)
    // {
    //     // Thông tin phiên bản hiện tại
    //     $version = DB::table('outline_program_versions as v')
    //         ->join('education_programs as p', 'p.id', '=', 'v.education_program_id')
    //         ->select('v.id', 'v.version_code', 'p.program_name', 'p.program_code')
    //         ->where('v.id', $version_id)
    //         ->first();

    //     if (!$version) {
    //         abort(404, 'Không tìm thấy phiên bản CTĐT');
    //     }

    //     // Danh sách tất cả phiên bản để chọn
    //     $allVersions = DB::table('outline_program_versions as v')
    //         ->join('education_programs as p', 'p.id', '=', 'v.education_program_id')
    //         ->select('v.id', 'v.version_code', 'p.program_code')
    //         ->orderBy('p.program_code')
    //         ->get();

    //     // Dữ liệu học phần
    //     $courses = DB::table('outline_program_courses as opc')
    //         ->join('courses as c', 'c.id', '=', 'opc.course_id')
    //         ->where('opc.program_version_id', $version_id)
    //         ->select('opc.*', 'c.course_code', 'c.course_name')
    //         ->orderBy('opc.knowledge_type')
    //         ->orderBy('opc.semester_no')
    //         ->get();

    //     $groups = $courses->groupBy('knowledge_type');

    //     return view('truongkhoa.ctdt_khung_overview', [
    //         'layout' => 'layouts.apptruongkhoa',
    //         'version' => $version,
    //         'groups' => $groups,
    //         'allVersions' => $allVersions,
    //         'knowledgeTypeLabels' => $this->knowledgeTypeLabels,
    //     ]);
    // }
    public function overview($version_id)
    {
        // 1) Thông tin phiên bản hiện tại
        $version = DB::table('outline_program_versions as v')
            ->join('education_programs as p', 'p.id', '=', 'v.education_program_id')
            ->select('v.id', 'v.version_code', 'p.program_name', 'p.program_code')
            ->where('v.id', $version_id)
            ->first();

        abort_if(!$version, 404, 'Không tìm thấy phiên bản CTĐT');

        // 2) Danh sách version để chọn (giữ nguyên)
        $allVersions = DB::table('outline_program_versions as v')
            ->join('education_programs as p', 'p.id', '=', 'v.education_program_id')
            ->select('v.id', 'v.version_code', 'p.program_code')
            ->orderBy('p.program_code')
            ->get();

        // 3) Dữ liệu học phần + học kỳ thực + năm học
        $courses = DB::table('outline_program_courses as opc')
            ->join('courses as c', 'c.id', '=', 'opc.course_id')
            ->leftJoin('semesters as s', 's.id', '=', 'opc.semester_id')
            ->leftJoin('academic_years as y', 'y.id', '=', 's.academic_year_id')
            ->where('opc.program_version_id', $version_id)
            ->select(
                'opc.id',
                'opc.course_id',
                'opc.program_version_id',
                'opc.knowledge_type',
                'opc.is_compulsory',
                'opc.credit_theory',
                'opc.credit_practice',
                'opc.credit_total',
                'opc.course_group',
                'opc.note',
                'c.course_code',
                'c.course_name',
                'opc.semester_id',
                's.order_number as semester_order',
                's.semester_name',
                'y.year_code'
            )
            // Sắp theo năm học → thứ tự học kỳ → nhóm trong kỳ (nếu có)
            ->orderBy('y.year_code')
            // ->orderBy('s.order_number')
            // ->orderBy('opc.id') // hoặc 'opc.course_group' nếu muốn
            ->get();

        // 4) Gom theo loại kiến thức (giữ nguyên)
        $groups = $courses->groupBy('knowledge_type');

        return view('truongkhoa.ctdt_khung_overview', [
            'layout' => 'layouts.apptruongkhoa',
            'version' => $version,
            'groups' => $groups,
            'allVersions' => $allVersions,
            'knowledgeTypeLabels' => $this->knowledgeTypeLabels,
        ]);
    }




    // === Hiển thị view crud khung CTĐT theo version_id ===
    // public function index($version_id)
    // {
    //     $version = DB::table('outline_program_versions')
    //         ->join('education_programs', 'outline_program_versions.education_program_id', '=', 'education_programs.id')
    //         ->select(
    //             'outline_program_versions.*',
    //             'education_programs.program_name as program_name',
    //             'education_programs.program_code as program_code'
    //         )
    //         ->where('outline_program_versions.id', $version_id)
    //         ->first();

    //     $courses = DB::table('outline_program_courses as opc')
    //         ->join('courses as c', 'opc.course_id', '=', 'c.id')
    //         ->select(
    //             'opc.id',
    //             'opc.course_id',
    //             'opc.program_version_id',
    //             'opc.knowledge_type',
    //             'opc.semester_no',
    //             'opc.is_compulsory',
    //             'opc.credit_theory',
    //             'opc.credit_practice',
    //             'opc.credit_total',
    //             'opc.course_group',
    //             'opc.note',
    //             'c.course_code',
    //             'c.course_name'
    //         )
    //         ->where('opc.program_version_id', $version_id)
    //         ->orderBy('opc.semester_no')
    //         ->orderBy('opc.order_in_semester')
    //         ->get();

    //     // Dữ liệu cho dropdown thêm mới
    //     $courseOptions = DB::table('courses')
    //         ->select('id', 'course_name', 'course_code')
    //         ->orderBy('course_name')
    //         ->get();

    //     return view('truongkhoa.ctdt_khung_crud', compact('version', 'courses', 'courseOptions'))
    //         ->with('title', 'Khung CTĐT')
    //         ->with('layout', 'layouts.apptruongkhoa')
    //         ->with('knowledgeTypeLabels', $this->knowledgeTypeLabels);
    // }

    public function index($version_id)
    {
        // === Lấy thông tin phiên bản CTĐT kèm meta ===
        $version = DB::table('outline_program_versions as v')
            ->join('education_programs as ep', 'v.education_program_id', '=', 'ep.id')
            ->select(
                'v.*',
                'ep.program_name as program_name',
                'ep.program_code as program_code'
            )
            ->where('v.id', $version_id)
            ->first();

        if (!$version) abort(404, 'Không tìm thấy phiên bản CTĐT.');

        // === Lấy danh sách học phần trong CTĐT ===
        $courses = DB::table('outline_program_courses as opc')
            ->join('courses as c', 'opc.course_id', '=', 'c.id')
            ->leftJoin('semesters as s', 'opc.semester_id', '=', 's.id')
            ->leftJoin('academic_years as y', 's.academic_year_id', '=', 'y.id')
            ->select(
                'opc.id',
                'opc.course_id',
                'opc.program_version_id',
                'opc.knowledge_type',
                'opc.semester_id',
                's.semester_name',
                'y.year_code',
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
            ->orderBy('y.year_code')
            ->orderBy('s.order_number')
            ->orderBy('opc.order_in_semester')
            ->get();

        // === Dữ liệu cho dropdown học phần ===
        $courseOptions = DB::table('courses')
            ->select('id', 'course_name', 'course_code')
            ->orderBy('course_name')
            ->get();

        // === Lấy danh sách năm học trong khoảng hiệu lực của CTĐT ===
        $academicYears = DB::table('academic_years')
            ->where(function ($q) use ($version) {
                $q->whereBetween('start_date', [$version->effective_from, $version->effective_to])
                    ->orWhereBetween('end_date', [$version->effective_from, $version->effective_to])
                    ->orWhere(function ($q2) use ($version) {
                        $q2->where('start_date', '<=', $version->effective_from)
                            ->where('end_date', '>=', $version->effective_to);
                    });
            })
            ->orderBy('year_code')
            ->get();

        // === Lấy học kỳ thuộc các năm học đó ===
        $semesters = DB::table('semesters')
            ->whereIn('academic_year_id', $academicYears->pluck('id'))
            ->select('id', 'semester_name', 'academic_year_id', 'order_number')
            ->orderBy('academic_year_id')
            ->orderBy('order_number')
            ->get();

        // === Trả dữ liệu sang view ===
        return view('truongkhoa.ctdt_khung_crud', compact(
            'version',
            'courses',
            'courseOptions',
            'academicYears',
            'semesters'
        ))
            ->with('title', 'Khung CTĐT')
            ->with('layout', 'layouts.apptruongkhoa')
            ->with('knowledgeTypeLabels', $this->knowledgeTypeLabels);
    }


    // === Lưu hoặc cập nhật học phần trong khung ===
    // public function store(Request $r, $version_id)
    // public function store(Request $r, $version_id)
    // {
    //     try {
    //         $data = $r->validate([
    //             'course_id'        => 'required|integer',
    //             'knowledge_type'   => 'nullable|string',
    //             'is_compulsory'    => 'nullable|boolean',
    //             'semester_no'      => 'nullable|integer',
    //             'credit_theory'    => 'nullable|integer',
    //             'credit_practice'  => 'nullable|integer',
    //             'course_group'     => 'nullable|string',
    //             'note'             => 'nullable|string',
    //         ]);

    //         // 🔹 Bổ sung mặc định an toàn cho các cột không cho phép NULL
    //         $data['is_compulsory']   = $data['is_compulsory'] ?? 1;
    //         $data['semester_no']     = $data['semester_no'] ?? 1;
    //         $data['credit_theory']   = $data['credit_theory'] ?? 0;
    //         $data['credit_practice'] = $data['credit_practice'] ?? 0;
    //         // $data['credit_total']    = ($data['credit_theory'] ?? 0) + ($data['credit_practice'] ?? 0);

    //         // 🔹 Metadata & version
    //         $data['program_version_id'] = $version_id;
    //         $data['updated_by'] = Auth::id();
    //         $data['updated_at'] = now();

    //         if ($r->id) {
    //             // === Cập nhật học phần
    //             DB::table('outline_program_courses')->where('id', $r->id)->update($data);
    //         } else {
    //             // === Thêm mới học phần
    //             $data['created_by'] = Auth::id();
    //             $data['created_at'] = now();
    //             DB::table('outline_program_courses')->insert($data);
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Lưu thành công!',
    //         ]);
    //     } catch (\Throwable $e) {
    //         return response()->json([
    //             'success' => false,
    //             'error'   => $e->getMessage(),
    //             'file'    => $e->getFile(),
    //             'line'    => $e->getLine(),
    //         ]);
    //     }
    // }

    public function store(Request $r, $version_id)
    {
        try {
            $data = $r->validate([
                'course_id'        => 'required|integer',
                'knowledge_type'   => 'nullable|string',
                'is_compulsory'    => 'nullable|boolean',
                'semester_no'      => 'nullable|integer',
                'semester_id'      => 'nullable|integer|exists:semesters,id',
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

            // 🔹 Metadata & version
            $data['program_version_id'] = $version_id;
            $data['updated_by'] = Auth::id();
            $data['updated_at'] = now();

            // 🔹 Nếu có semester_id → tự động lấy year_code để tiện tracking (optional)
            if (!empty($data['semester_id'])) {
                $year = DB::table('semesters')
                    ->join('academic_years', 'semesters.academic_year_id', '=', 'academic_years.id')
                    ->where('semesters.id', $data['semester_id'])
                    ->select('academic_years.id as academic_year_id', 'academic_years.year_code')
                    ->first();
                if ($year) {
                    $data['academic_year_id'] = $year->academic_year_id;
                    $data['academic_year_code'] = $year->year_code ?? null; // nếu bạn muốn log thêm
                }
            }

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
