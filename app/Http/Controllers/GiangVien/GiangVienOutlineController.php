<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class GiangVienOutlineController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $lectureId = $user->lecture_id;

        if (!$lectureId) abort(403, "Không tìm thấy thông tin giảng viên.");

        // Lấy tất cả phân công soạn đề cương
        $assignments = DB::table('outline_course_assignments as a')
            ->join('outline_program_courses as opc', 'a.program_course_id', '=', 'opc.id')
            ->join('courses as c', 'opc.course_id', '=', 'c.id')
            ->join('outline_program_versions as opv', 'opc.program_version_id', '=', 'opv.id')
            ->join('education_programs as ep', 'opv.education_program_id', '=', 'ep.id')
            ->leftJoin('outline_course_versions as ocv', 'a.outline_course_version_id', '=', 'ocv.id')
            ->where('a.lecture_id', $lectureId)
            ->select(
                'a.id as assignment_id',
                'a.role',
                'a.due_date',
                'a.status',

                'c.course_code',
                'c.course_name',

                'ep.program_code',
                'ep.program_name',
                'opv.version_code as program_version_code',

                'ocv.id as version_id',
                'ocv.version_no',
                'ocv.status as version_status'
            )
            ->orderBy('c.course_code')
            ->get();

        return view('giangvien.decuong_index', [
            'assignments' => $assignments
        ]);
    }

    public function createVersion($assignmentId)
    {
        $user = Auth::user();
        $lectureId = $user->lecture_id;

        $assignment = DB::table('outline_course_assignments')
            ->where('id', $assignmentId)
            ->where('lecture_id', $lectureId)
            ->whereNull('outline_course_version_id')
            ->first();

        if (!$assignment) {
            return back()->with('error', 'Không thể tạo phiên bản đề cương.');
        }

        DB::beginTransaction();

        try {
            // Tạo version mới (V1)
            $versionId = DB::table('outline_course_versions')->insertGetId([
                'program_course_id' => $assignment->program_course_id,
                'version_no'        => 1,
                'status'            => 'draft',
                'created_at'        => now(),
                'updated_at'        => now()
            ]);

            // Gán tất cả assignment cùng môn vào version này
            DB::table('outline_course_assignments')
                ->where('program_course_id', $assignment->program_course_id)
                ->whereNull('outline_course_version_id')
                ->update([
                    'outline_course_version_id' => $versionId,
                    'updated_at' => now()
                ]);

            DB::commit();

            return redirect()
                ->route('giangvien.outlines.edit', ['courseVersion' => $versionId])
                ->with('success', 'Đã tạo phiên bản đề cương.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }




    /**
     * Màn hình soạn đề cương cho 1 phiên bản học phần
     */
    public function edit($courseVersionId)
    {
        // Thông tin phiên bản đề cương + học phần
        $courseVersion = DB::table('outline_course_versions as ocv')
            ->leftJoin('outline_program_courses as opc', 'ocv.program_course_id', '=', 'opc.id')
            ->leftJoin('courses', 'opc.course_id', '=', 'courses.id')
            ->select(
                'ocv.id',
                'ocv.version_no',
                'courses.course_code',
                'courses.course_name'
            )
            ->where('ocv.id', $courseVersionId)
            ->first();

        if (!$courseVersion) {
            abort(404, 'Không tìm thấy phiên bản đề cương.');
        }

        // Khoa của giảng viên hiện tại
        $facultyId = DB::table('lectures as l')
            ->join('departments as d', 'd.id', '=', 'l.department_id')
            ->join('faculties as f', 'f.id', '=', 'd.faculty_id')
            ->where('l.id', Auth::user()->lecture_id)
            ->value('f.id');


        // Danh sách mẫu đề cương trong khoa
        $templates = DB::table('outline_templates')
            ->where('faculty_id', $facultyId)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        // Kiểm tra xem courseVersion này đã có nội dung section chưa
        $existingSectionRows = DB::table('outline_section_contents as c')
            ->join('outline_section_templates as st', 'c.section_template_id', '=', 'st.id')
            ->join('outline_templates as t', 'st.outline_template_id', '=', 't.id')
            ->where('c.course_version_id', $courseVersionId)
            ->orderBy('st.order_no')
            ->select(
                'c.section_template_id',
                'st.code',
                'st.title',
                'st.order_no',
                'c.content_html',
                't.id as template_id',
                't.gov_header',
                't.university_name',
                't.national_header',
                't.national_motto',
                't.major_name'
            )
            ->get();

        $sections = [];
        $currentTemplateId = null;
        $templateMeta = null;

        if ($existingSectionRows->count() > 0) {
            $first = $existingSectionRows->first();
            $currentTemplateId = $first->template_id;

            $templateMeta = [
                'id'              => $first->template_id,
                'gov_header'      => $first->gov_header,
                'university_name' => $first->university_name,
                'national_header' => $first->national_header,
                'national_motto'  => $first->national_motto,
                'major_name'      => $first->major_name,
            ];

            foreach ($existingSectionRows as $row) {
                $sections[] = [
                    'section_template_id' => $row->section_template_id,
                    'code'                => $row->code,
                    'title'               => $row->title,
                    'order_no'            => $row->order_no,
                    'content_html'        => $row->content_html,
                ];
            }
        }

        return view('giangvien.outline_editor', [
            'courseVersion'     => $courseVersion,
            'templates'         => $templates,
            'currentTemplateId' => $currentTemplateId,
            'templateMeta'      => $templateMeta,
            'sections'          => $sections,
            // nếu bạn có layout riêng cho giảng viên thì sửa lại ở đây
            'layout'            => 'layouts.appGV',
        ]);
    }

    /**
     * Load metadata + section của 1 template để fill lên editor khi GV chọn mẫu
     */
    public function loadTemplate(Request $request, $courseVersionId)
    {
        $templateId = $request->input('template_id');
        if (!$templateId) {
            return response()->json([
                'success' => false,
                'message' => 'Thiếu template_id.',
            ], 422);
        }

        // Khoa của giảng viên hiện tại
        $facultyId = DB::table('lectures as l')
            ->join('departments as d', 'd.id', '=', 'l.department_id')
            ->join('faculties as f', 'f.id', '=', 'd.faculty_id')
            ->where('l.id', Auth::user()->lecture_id)
            ->value('f.id');


        $template = DB::table('outline_templates')
            ->where('id', $templateId)
            ->when($facultyId, function ($q) use ($facultyId) {
                $q->where('faculty_id', $facultyId);
            })
            ->first();

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy mẫu đề cương phù hợp.',
            ], 404);
        }

        $sections = DB::table('outline_section_templates')
            ->where('outline_template_id', $templateId)
            ->orderBy('order_no')
            ->get()
            ->map(function ($row) {
                return [
                    'section_template_id' => $row->id,
                    'code'                => $row->code,
                    'title'               => $row->title,
                    'order_no'            => $row->order_no,
                    'content_html'        => $row->default_content ?? '',
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'success'  => true,
            'template' => [
                'id'              => $template->id,
                'code'            => $template->code,
                'name'            => $template->name,
                'gov_header'      => $template->gov_header,
                'university_name' => $template->university_name,
                'national_header' => $template->national_header,
                'national_motto'  => $template->national_motto,
                'major_name'      => $template->major_name,
            ],
            'sections' => $sections,
        ]);
    }

    /**
     * Lưu nội dung đề cương (outline_section_contents)
     */
    public function save(Request $request, $courseVersionId)
    {
        $templateId = $request->input('template_id');
        $sections   = $request->input('sections', []);

        if (empty($templateId)) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn mẫu đề cương trước khi lưu.',
            ], 422);
        }

        if (!is_array($sections) || empty($sections)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có nội dung section nào để lưu.',
            ], 422);
        }

        // (tuỳ bạn) kiểm tra courseVersion tồn tại
        $courseVersion = DB::table('outline_course_versions')
            ->where('id', $courseVersionId)
            ->first();

        if (!$courseVersion) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phiên bản đề cương.',
            ], 404);
        }

        DB::beginTransaction();

        try {
            $now    = now();
            $userId = Auth::id();

            // Xoá toàn bộ nội dung cũ
            DB::table('outline_section_contents')
                ->where('course_version_id', $courseVersionId)
                ->delete();

            // Insert nội dung mới
            foreach ($sections as $s) {
                if (empty($s['section_template_id'])) {
                    throw new \Exception('Thiếu section_template_id cho 1 mục nội dung.');
                }

                DB::table('outline_section_contents')->insert([
                    'course_version_id'   => $courseVersionId,
                    'section_template_id' => $s['section_template_id'],
                    'content_html'        => $s['content_html'] ?? '',
                    'created_by'          => $userId,
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ]);
            }

            // Cập nhật trạng thái đề cương → draft (tuỳ bạn)
            DB::table('outline_course_versions')
                ->where('id', $courseVersionId)
                ->update([
                    'status'     => 'draft',
                    'updated_at' => $now,
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã lưu đề cương.',
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
