<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GiangVienCloController extends Controller
{
    // ====== HÀM CHECK QUYỀN DÙNG CHUNG ======
    protected function ensureAssignment($courseVersionId)
    {
        $user = Auth::user();
        $lectureId = $user->lecture_id ?? null;

        if (!$lectureId) {
            abort(403, 'Tài khoản hiện tại không gắn với giảng viên.');
        }

        $hasAssignment = DB::table('outline_course_versions as ocv')
            ->join('outline_program_courses as opc', 'ocv.program_course_id', '=', 'opc.id')
            ->join('outline_course_assignments as oca', 'oca.program_course_id', '=', 'opc.id')
            ->where('ocv.id', $courseVersionId)
            ->where('oca.lecture_id', $lectureId)
            ->exists();

        if (!$hasAssignment) {
            abort(403, 'Bạn không được phân công soạn đề cương này.');
        }
    }

    /**
     * Trang tiện ích xây dựng CLO cho 1 phiên bản đề cương.
     */
    public function index($courseVersionId)
    {
        $user = Auth::user();
        $lectureId = $user->lecture_id ?? null;

        if (!$lectureId) {
            abort(403, 'Tài khoản hiện tại không gắn với giảng viên.');
        }

        // ✅ Kiểm tra giảng viên có được phân công soạn đề cương này không
        $hasAssignment = DB::table('outline_course_versions as ocv')
            ->join('outline_program_courses as opc', 'ocv.program_course_id', '=', 'opc.id')
            ->join('outline_course_assignments as oca', 'oca.program_course_id', '=', 'opc.id')
            ->where('ocv.id', $courseVersionId)
            ->where('oca.lecture_id', $lectureId)
            ->exists();

        if (!$hasAssignment) {
            abort(403, 'Bạn không được phân công soạn đề cương này.');
        }

        // 📌 Thông tin phiên bản đề cương + học phần + CTĐT
        $courseVersion = DB::table('outline_course_versions as ocv')
            ->join('outline_program_courses as opc', 'ocv.program_course_id', '=', 'opc.id')
            ->join('courses as c', 'opc.course_id', '=', 'c.id')
            ->join('outline_program_versions as opv', 'opc.program_version_id', '=', 'opv.id')
            ->join('education_programs as ep', 'opv.education_program_id', '=', 'ep.id')
            ->select(
                'ocv.id',
                'ocv.version_no',
                'c.course_code',
                'c.course_name',
                'ep.program_code',
                'ep.program_name',
                'opv.version_code as program_version_code'
            )
            ->where('ocv.id', $courseVersionId)
            ->first();

        if (!$courseVersion) {
            abort(404, 'Không tìm thấy phiên bản đề cương.');
        }


        // 🔹 Lấy danh sách section (đã có trong đề cương) để render CLO vào
        $sections = DB::table('outline_section_contents as c')
            ->join('outline_section_templates as st', 'c.section_template_id', '=', 'st.id')
            ->where('c.course_version_id', $courseVersionId)
            ->orderBy('st.order_no')
            ->select(
                'st.id as section_template_id',
                'st.code',
                'st.title'
            )
            ->get();

        // 📌 Danh sách CLO hiện có (nếu đã từng soạn)
        $clos = DB::table('outline_clos')
            ->where('course_version_id', $courseVersionId)
            ->orderBy('code') // CLO1, CLO2...
            ->get();

        return view('giangvien.decuong_clo_builder', [
            'courseVersion' => $courseVersion,
            'clos'          => $clos,
            'sections'      => $sections,
        ]);
    }

    /**
     * Lấy chi tiết 1 CLO (AJAX) để fill modal sửa.
     */
    public function show($courseVersionId, $cloId)
    {
        $this->ensureAssignment($courseVersionId);

        $clo = DB::table('outline_clos')
            ->where('course_version_id', $courseVersionId)
            ->where('id', $cloId)
            ->first();

        if (!$clo) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy CLO.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $clo,
        ]);
    }

    /**
     * Tạo mới CLO (AJAX).
     */
    public function store(Request $request, $courseVersionId)
    {
        $this->ensureAssignment($courseVersionId);

        $data = $request->validate([
            'code'        => 'required|string|max:50',
            'description' => 'required|string',
            'bloom_level' => 'nullable|string|max:50',
        ], [
            'code.required'        => 'Vui lòng nhập mã CLO.',
            'description.required' => 'Vui lòng nhập mô tả CLO.',
        ]);

        // Check trùng code trong cùng course_version
        $exists = DB::table('outline_clos')
            ->where('course_version_id', $courseVersionId)
            ->where('code', $data['code'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Mã CLO đã tồn tại trong học phần này.',
            ], 422);
        }

        $id = DB::table('outline_clos')->insertGetId([
            'course_version_id' => $courseVersionId,
            'code'              => $data['code'],
            'description'       => $data['description'],
            'bloom_level'       => $data['bloom_level'] ?? null,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        $clo = DB::table('outline_clos')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm CLO.',
            'data'    => $clo,
        ]);
    }

    /**
     * Cập nhật CLO (AJAX).
     */
    public function update(Request $request, $courseVersionId, $cloId)
    {
        $this->ensureAssignment($courseVersionId);

        $clo = DB::table('outline_clos')
            ->where('course_version_id', $courseVersionId)
            ->where('id', $cloId)
            ->first();

        if (!$clo) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy CLO.',
            ], 404);
        }

        $data = $request->validate([
            'code'        => 'required|string|max:50',
            'description' => 'required|string',
            'bloom_level' => 'nullable|string|max:50',
        ], [
            'code.required'        => 'Vui lòng nhập mã CLO.',
            'description.required' => 'Vui lòng nhập mô tả CLO.',
        ]);

        // Check trùng code (bỏ qua chính nó)
        $exists = DB::table('outline_clos')
            ->where('course_version_id', $courseVersionId)
            ->where('code', $data['code'])
            ->where('id', '<>', $cloId)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Mã CLO đã tồn tại trong học phần này.',
            ], 422);
        }

        DB::table('outline_clos')
            ->where('id', $cloId)
            ->update([
                'code'        => $data['code'],
                'description' => $data['description'],
                'bloom_level' => $data['bloom_level'] ?? null,
                'updated_at'  => now(),
            ]);

        $updated = DB::table('outline_clos')->where('id', $cloId)->first();

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật CLO.',
            'data'    => $updated,
        ]);
    }

    /**
     * Xóa CLO (AJAX).
     */
    public function destroy($courseVersionId, $cloId)
    {
        $this->ensureAssignment($courseVersionId);

        $clo = DB::table('outline_clos')
            ->where('course_version_id', $courseVersionId)
            ->where('id', $cloId)
            ->first();

        if (!$clo) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy CLO.',
            ], 404);
        }

        DB::table('outline_clos')
            ->where('id', $cloId)
            ->delete();

        // TODO: nếu muốn, có thể xóa luôn mapping CLO-PLO/PI ở đây

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa CLO.',
        ]);
    }

    public function preview(Request $request, $courseVersionId)
    {
        $this->ensureAssignment($courseVersionId);

        $clos = DB::table('outline_clos')
            ->where('course_version_id', $courseVersionId)
            ->orderBy('code')
            ->get();

        if ($clos->isEmpty()) {
            return "(Chưa có CLO nào để xem preview)";
        }

        return view('giangvien.render_templates.clo_html', [
            'clos' => $clos
        ]);
    }


    // public function renderToSection(Request $request, $courseVersionId)
    // {
    //     $this->ensureAssignment($courseVersionId);

    //     $request->validate([
    //         'section_template_id' => 'required|integer',
    //     ]);

    //     $sectionId = $request->section_template_id;

    //     $clos = DB::table('outline_clos')
    //         ->where('course_version_id', $courseVersionId)
    //         ->orderBy('code')
    //         ->get();

    //     if ($clos->isEmpty()) {
    //         return response()->json(['success' => false, 'message' => 'Chưa có CLO nào.'], 422);
    //     }

    //     $html = view('giangvien.render_templates.clo_html', [
    //         'clos' => $clos,
    //     ])->render();

    //     DB::table('outline_section_contents')
    //         ->updateOrInsert(
    //             [
    //                 'course_version_id' => $courseVersionId,
    //                 'section_template_id' => $sectionId
    //             ],
    //             [
    //                 'content_html' => $html,
    //                 'created_by' => Auth::id(),
    //                 'updated_at' => now(),
    //                 'created_at' => now(),
    //             ]
    //         );

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Đã render và ghi vào đề cương.',
    //     ]);
    // }

    public function renderToSection(Request $request, $courseVersionId)
    {
        $this->ensureAssignment($courseVersionId);

        $request->validate([
            'section_template_id' => 'required|integer',
            'mode' => 'nullable|string|in:replace,prepend,append',
        ]);

        $sectionId = $request->section_template_id;
        $mode = $request->mode ?? 'replace';

        // Lấy CLOs
        $clos = DB::table('outline_clos')
            ->where('course_version_id', $courseVersionId)
            ->orderBy('code')
            ->get();

        if ($clos->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Chưa có CLO nào.'], 422);
        }

        // Render HTML mới
        $newHtml = view('giangvien.render_templates.clo_html', [
            'clos' => $clos,
        ])->render();

        // Lấy nội dung cũ
        $existing = DB::table('outline_section_contents')
            ->where('course_version_id', $courseVersionId)
            ->where('section_template_id', $sectionId)
            ->value('content_html');

        // Merge theo mode
        $finalHtml = $newHtml;

        if ($mode === 'prepend' && $existing) {
            $finalHtml = $newHtml . "\n\n" . $existing;
        }

        if ($mode === 'append' && $existing) {
            $finalHtml = $existing . "\n\n" . $newHtml;
        }

        // Lưu lại
        DB::table('outline_section_contents')
            ->updateOrInsert(
                [
                    'course_version_id' => $courseVersionId,
                    'section_template_id' => $sectionId
                ],
                [
                    'content_html' => $finalHtml,
                    'created_by' => Auth::id(),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

        return response()->json([
            'success' => true,
            'message' => 'Đã ghi CLO vào đề cương.',
        ]);
    }
}
