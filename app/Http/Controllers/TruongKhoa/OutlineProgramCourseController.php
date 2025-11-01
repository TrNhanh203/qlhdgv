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
    public function overview($version_id)
    {
        // Thông tin phiên bản hiện tại
        $version = DB::table('outline_program_versions as v')
            ->join('education_programs as p', 'p.id', '=', 'v.education_program_id')
            ->select('v.id', 'v.version_code', 'p.program_name', 'p.program_code')
            ->where('v.id', $version_id)
            ->first();

        if (!$version) {
            abort(404, 'Không tìm thấy phiên bản CTĐT');
        }

        // Danh sách tất cả phiên bản để chọn
        $allVersions = DB::table('outline_program_versions as v')
            ->join('education_programs as p', 'p.id', '=', 'v.education_program_id')
            ->select('v.id', 'v.version_code', 'p.program_code')
            ->orderBy('p.program_code')
            ->get();

        // Dữ liệu học phần
        $courses = DB::table('outline_program_courses as opc')
            ->join('courses as c', 'c.id', '=', 'opc.course_id')
            ->where('opc.program_version_id', $version_id)
            ->select('opc.*', 'c.course_code', 'c.course_name')
            ->orderBy('opc.knowledge_type')
            ->orderBy('opc.semester_no')
            ->get();

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
            ->with('layout', 'layouts.apptruongkhoa')
            ->with('knowledgeTypeLabels', $this->knowledgeTypeLabels);
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
