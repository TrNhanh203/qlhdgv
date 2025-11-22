<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class GiangVienOutlineController extends Controller
{

    public function cloneSelect($assignmentId)
    {
        $user = Auth::user();
        $lectureId = $user->lecture_id;

        // 1. Lấy assignment hiện tại của GV (đích clone đến) + thông tin CTĐT, khóa, năm học, học kỳ
        $assignment = DB::table('outline_course_assignments as a')
            ->join('outline_program_courses as opc', 'a.program_course_id', '=', 'opc.id')
            ->join('courses as c', 'opc.course_id', '=', 'c.id')
            ->join('outline_program_versions as opv', 'opc.program_version_id', '=', 'opv.id')
            ->join('education_programs as ep', 'opv.education_program_id', '=', 'ep.id')

            // ✅ Năm học + học kỳ lấy từ outline_program_courses, không phải từ courses
            ->leftJoin('semesters as s', 'opc.semester_id', '=', 's.id')
            ->leftJoin('academic_years as ay', 'opc.academic_year_id', '=', 'ay.id')

            ->where('a.id', $assignmentId)
            ->where('a.lecture_id', $lectureId)
            ->select(
                'a.id',
                'a.program_course_id',
                'a.outline_course_version_id',
                'a.role',

                'c.course_code',
                'c.course_name',

                'ep.program_code',
                'ep.program_name',
                'opv.version_code as program_version_code',

                // 👇 Thông tin năm học & học kỳ của assignment hiện tại
                'ay.year_code as academic_year_code',
                's.semester_name'
            )
            ->first();

        if (!$assignment) {
            abort(404, 'Không tìm thấy phân công đề cương phù hợp.');
        }

        // 2. Lấy các phiên bản đề cương cũ của cùng học phần + CTĐT + năm học + học kỳ
        $sourceVersions = DB::table('outline_course_versions as ocv')
            ->join('outline_program_courses as opc', 'ocv.program_course_id', '=', 'opc.id')
            ->join('outline_program_versions as opv', 'opc.program_version_id', '=', 'opv.id')
            ->join('education_programs as ep', 'opv.education_program_id', '=', 'ep.id')
            ->join('courses as c', 'opc.course_id', '=', 'c.id')

            // ✅ Cũng lấy năm học + học kỳ từ outline_program_courses
            ->leftJoin('semesters as s', 'opc.semester_id', '=', 's.id')
            ->leftJoin('academic_years as ay', 'opc.academic_year_id', '=', 'ay.id')

            ->where('c.course_code', $assignment->course_code)   // cùng mã học phần
            ->select(
                'ocv.id as version_id',
                'ocv.version_no',
                'ocv.status',

                'c.course_code',
                'c.course_name',

                'ep.program_code',
                'ep.program_name',
                'opv.version_code as program_version_code',

                // 👇 Thông tin năm học + học kỳ của từng version cũ
                'ay.year_code as academic_year_code',
                's.semester_name'
            )
            ->orderByDesc('ocv.created_at')
            ->get();

        return view('giangvien.outlines_clone_select', [
            'assignment'     => $assignment,
            'sourceVersions' => $sourceVersions,
        ]);
    }




    public function clonePreview($sourceVersionId)
    {
        // Lấy meta version nguồn
        $courseVersion = DB::table('outline_course_versions as ocv')
            ->join('outline_program_courses as opc', 'ocv.program_course_id', '=', 'opc.id')
            ->join('courses as c', 'opc.course_id', '=', 'c.id')
            ->join('outline_program_versions as opv', 'opc.program_version_id', '=', 'opv.id')
            ->join('education_programs as ep', 'opv.education_program_id', '=', 'ep.id')

            // Lấy năm học + học kỳ từ outline_program_courses
            ->leftJoin('semesters as s', 'opc.semester_id', '=', 's.id')
            ->leftJoin('academic_years as ay', 'opc.academic_year_id', '=', 'ay.id')

            ->where('ocv.id', $sourceVersionId)
            ->select(
                'ocv.id',
                'ocv.version_no',
                'ocv.status',

                'c.course_code',
                'c.course_name',

                'ep.program_code',
                'ep.program_name',
                'opv.version_code as program_version_code',

                'ay.year_code as academic_year_code',
                's.semester_name'
            )
            ->first();

        if (!$courseVersion) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phiên bản đề cương nguồn.'
            ], 404);
        }

        // Lấy danh sách mục nội dung
        $sections = DB::table('outline_section_contents as c')
            ->join('outline_section_templates as st', 'c.section_template_id', '=', 'st.id')
            ->where('c.course_version_id', $sourceVersionId)
            ->orderBy('st.order_no')
            ->select(
                'st.code',
                'st.title',
                'c.content_html'
            )
            ->get();

        // Render partial thành HTML
        $html = view('giangvien.partials.outline_preview', [
            'courseVersion' => $courseVersion,
            'sections'      => $sections,
        ])->render();

        return response()->json([
            'success' => true,
            'html'    => $html,
        ]);
    }




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

    public function clonePerform(Request $request, $assignmentId, $sourceVersionId)
    {
        $user      = Auth::user();
        $lectureId = $user->lecture_id;
        $userId    = $user->id;
        $now       = now();

        // 1. Lấy assignment đích (phân công hiện tại) + version đang soạn
        $assignment = DB::table('outline_course_assignments as a')
            ->join('outline_program_courses as opc', 'a.program_course_id', '=', 'opc.id')
            ->join('courses as c', 'opc.course_id', '=', 'c.id')
            ->join('outline_program_versions as opv', 'opc.program_version_id', '=', 'opv.id')
            ->join('education_programs as ep', 'opv.education_program_id', '=', 'ep.id')
            ->where('a.id', $assignmentId)
            ->where('a.lecture_id', $lectureId)
            ->select(
                'a.id',
                'a.program_course_id',
                'a.outline_course_version_id',
                'a.role',

                'c.course_code',
                'c.course_name',

                'ep.program_code',
                'ep.program_name',
                'opv.version_code as program_version_code'
            )
            ->first();

        if (!$assignment) {
            return back()->with('error', 'Không tìm thấy phân công đề cương phù hợp.');
        }

        if (!$assignment->outline_course_version_id) {
            return back()->with('error', 'Phân công này chưa có phiên bản đề cương. Vui lòng bấm "Tạo đề cương" trước khi nhân bản.');
        }

        $targetVersionId = $assignment->outline_course_version_id; // 🎯 bản đề cương đang soạn

        // 2. Lấy version nguồn + kiểm tra cùng học phần (an toàn thêm)
        $sourceVersion = DB::table('outline_course_versions as ocv')
            ->join('outline_program_courses as opc', 'ocv.program_course_id', '=', 'opc.id')
            ->join('courses as c', 'opc.course_id', '=', 'c.id')
            ->where('ocv.id', $sourceVersionId)
            ->select(
                'ocv.id',
                'ocv.program_course_id',
                'ocv.version_no',
                'c.course_code',
                'c.course_name'
            )
            ->first();

        if (!$sourceVersion) {
            return back()->with('error', 'Không tìm thấy phiên bản đề cương nguồn.');
        }

        // (có thể nới lỏng nếu bạn muốn cho copy chéo học phần, nhưng mặc định nên cùng mã)
        if ($sourceVersion->course_code !== $assignment->course_code) {
            return back()->with('error', 'Phiên bản nguồn không cùng mã học phần với phân công hiện tại.');
        }

        DB::beginTransaction();

        try {
            /*
         * 3. Xoá sạch nội dung hiện có của version đích
         *    Vì tiện ích clone là "soạn nhanh" → ghi đè toàn bộ
         */

            // 3.1. Xoá section contents
            DB::table('outline_section_contents')
                ->where('course_version_id', $targetVersionId)
                ->delete();

            // 3.2. Xoá CLO + mapping (nếu có)
            $oldTargetCloIds = DB::table('outline_clos')
                ->where('course_version_id', $targetVersionId)
                ->pluck('id')
                ->all();

            if (!empty($oldTargetCloIds)) {
                DB::table('outline_clo_pi_maps')
                    ->whereIn('clo_id', $oldTargetCloIds)
                    ->delete();

                DB::table('outline_clo_plo_maps')
                    ->whereIn('clo_id', $oldTargetCloIds)
                    ->delete();

                DB::table('outline_clos')
                    ->whereIn('id', $oldTargetCloIds)
                    ->delete();
            }

            /*
         * 4. Copy outline_section_contents từ sourceVersion sang targetVersion
         */
            $sourceSections = DB::table('outline_section_contents')
                ->where('course_version_id', $sourceVersionId)
                ->get();

            foreach ($sourceSections as $sec) {
                DB::table('outline_section_contents')->insert([
                    'course_version_id'   => $targetVersionId,
                    'section_template_id' => $sec->section_template_id,
                    'content_html'        => $sec->content_html,
                    'created_by'          => $userId, // hoặc $sec->created_by nếu muốn giữ nguyên
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ]);
            }

            /*
         * 5. Copy CLO + mapping sang version đích (nếu có dùng CLO)
         */

            // 5.1. Clone outline_clos từ sourceVersion sang targetVersion
            $sourceClos = DB::table('outline_clos')
                ->where('course_version_id', $sourceVersionId)
                ->get();

            $cloIdMap = [];

            foreach ($sourceClos as $clo) {
                $newCloId = DB::table('outline_clos')->insertGetId([
                    'course_version_id' => $targetVersionId,
                    'code'              => $clo->code,
                    'description'       => $clo->description,
                    'bloom_level'       => $clo->bloom_level,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ]);

                $cloIdMap[$clo->id] = $newCloId;
            }

            if (!empty($cloIdMap)) {
                $oldCloIds = array_keys($cloIdMap);

                // 5.2. Clone CLO–PI
                $sourceCloPiMaps = DB::table('outline_clo_pi_maps')
                    ->whereIn('clo_id', $oldCloIds)
                    ->get();

                foreach ($sourceCloPiMaps as $m) {
                    $newCloId = $cloIdMap[$m->clo_id] ?? null;
                    if (!$newCloId) continue;

                    DB::table('outline_clo_pi_maps')->insert([
                        'clo_id'     => $newCloId,
                        'pi_id'      => $m->pi_id,
                        'level'      => $m->level,
                        'weight'     => $m->weight,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                // 5.3. Clone CLO–PLO
                $sourceCloPloMaps = DB::table('outline_clo_plo_maps')
                    ->whereIn('clo_id', $oldCloIds)
                    ->get();

                foreach ($sourceCloPloMaps as $m) {
                    $newCloId = $cloIdMap[$m->clo_id] ?? null;
                    if (!$newCloId) continue;

                    DB::table('outline_clo_plo_maps')->insert([
                        'clo_id'     => $newCloId,
                        'plo_id'     => $m->plo_id,
                        'level'      => $m->level,
                        'weight'     => $m->weight,
                        'created_by' => $userId, // hoặc $m->created_by nếu muốn giữ nguyên
                        'created_at' => $now,
                    ]);
                }
            }

            /*
         * 6. Cập nhật meta version đích (status draft, updated_at)
         */
            DB::table('outline_course_versions')
                ->where('id', $targetVersionId)
                ->update([
                    'status'     => 'draft',
                    'updated_at' => $now,
                ]);

            DB::commit();

            return redirect()
                ->route('giangvien.outlines.edit', ['courseVersion' => $targetVersionId])
                ->with('success', 'Đã nhân bản nội dung đề cương (và CLO, nếu có) vào phiên bản hiện tại.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Lỗi khi nhân bản đề cương: ' . $e->getMessage());
        }
    }






    /**
     * Màn hình soạn đề cương cho 1 phiên bản học phần
     */
    public function edit($courseVersionId)
    {
        // Thông tin phiên bản đề cương + học phần
        // $courseVersion = DB::table('outline_course_versions as ocv')
        //     ->leftJoin('outline_program_courses as opc', 'ocv.program_course_id', '=', 'opc.id')
        //     ->leftJoin('courses', 'opc.course_id', '=', 'courses.id')
        //     ->select(
        //         'ocv.id',
        //         'ocv.version_no',
        //         'courses.course_code',
        //         'courses.course_name'
        //     )
        //     ->where('ocv.id', $courseVersionId)
        //     ->first();

        $courseVersion = DB::table('outline_course_versions as ocv')
            ->join('outline_program_courses as opc', 'ocv.program_course_id', '=', 'opc.id')
            ->join('courses as c', 'opc.course_id', '=', 'c.id')
            ->join('outline_program_versions as opv', 'opc.program_version_id', '=', 'opv.id')
            ->join('education_programs as ep', 'opv.education_program_id', '=', 'ep.id')
            // lấy năm học + học kỳ từ outline_program_courses
            ->leftJoin('semesters as s', 'opc.semester_id', '=', 's.id')
            ->leftJoin('academic_years as ay', 'opc.academic_year_id', '=', 'ay.id')
            ->select(
                'ocv.id',
                'ocv.version_no',

                'c.course_code',
                'c.course_name',

                'ep.program_code',
                'ep.program_name',
                'opv.version_code as program_version_code',

                'ay.year_code as academic_year_code',
                's.semester_name'
            )
            ->where('ocv.id', $courseVersionId)
            ->first();


        if (!$courseVersion) {
            abort(404, 'Không tìm thấy phiên bản đề cương.');
        }

        // Lấy assignment của GV hiện tại ứng với version này (nếu có)
        $assignment = DB::table('outline_course_assignments')
            ->where('outline_course_version_id', $courseVersionId)
            ->where('lecture_id', Auth::user()->lecture_id)
            ->first();

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
            'assignment'        => $assignment,
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
