<?php

namespace App\Http\Controllers\TruongKhoa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class OutlineTemplateController extends Controller
{
    public function index()
    {
        // 🔹 Lấy faculty_id của trưởng khoa hiện tại
        $facultyId = DB::table('lecture_roles')
            ->where('lecture_id', Auth::user()->lecture_id)
            ->whereNotNull('faculty_id')
            ->value('faculty_id');

        // 🔹 Lấy danh sách template thuộc khoa
        $items = DB::table('outline_templates')
            ->where('faculty_id', $facultyId)
            ->orderByDesc('id')
            ->get();

        // 🔹 Cột hiển thị trong bảng
        $columns = [
            ['label' => 'Tên mẫu đề cương', 'field' => 'name'],
            ['label' => 'Ngành áp dụng', 'field' => 'major_name', 'default' => '-'],
            // ['label' => 'Người tạo', 'field' => 'created_by', 'default' => 'Trưởng khoa'],
            [
                'label' => 'Ngày tạo',
                'field' => 'created_at',
                'default' => '-',
                'type' => 'date',
            ],
            [
                'label' => 'Trạng thái',
                'field' => 'is_default',
                'type' => 'badge',
                'options' => [
                    1 => ['text' => 'Mặc định', 'class' => 'bg-success text-white px-2 py-1 rounded'],
                    0 => ['text' => 'Tùy chọn', 'class' => 'bg-secondary text-white px-2 py-1 rounded'],
                ],
            ],
            [
                'label' => 'Thao tác',
                'type' => 'actions',
                'menu_items' => [
                    [
                        'text' => 'Chỉnh sửa mẫu đề cương',
                        'desc' => 'Mở trình biên tập mẫu đề cương này',
                        'route' => 'truongkhoa.outline-template.edit',
                        'param' => 'id',
                        'icon' => 'bi bi-pencil-square'
                    ],
                    // [
                    //     'text' => 'Đặt làm mặc định',
                    //     'desc' => 'Thiết lập mẫu này làm mặc định cho khoa',
                    //     'route' => 'truongkhoa.outline-template.setDefault',
                    //     'param' => 'id',
                    //     'icon' => 'bi bi-star'
                    // ],
                ],
            ],
        ];


        // 🔹 Các trường trong form thêm / sửa
        $fields = [
            ['name' => 'code', 'label' => 'Mã mẫu', 'type' => 'text', 'required' => true],
            ['name' => 'name', 'label' => 'Tên mẫu đề cương', 'type' => 'text', 'required' => true],
            ['name' => 'major_name', 'label' => 'Ngành áp dụng', 'type' => 'text'],
            ['name' => 'gov_header', 'label' => 'Cơ quan chủ quản', 'type' => 'text', 'default' => 'UBND TP. HỒ CHÍ MINH'],
            ['name' => 'university_name', 'label' => 'Tên trường', 'type' => 'text', 'default' => 'TRƯỜNG ĐH THỦ DẦU MỘT'],
            ['name' => 'national_header', 'label' => 'Quốc hiệu', 'type' => 'text', 'default' => 'CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM'],
            ['name' => 'national_motto', 'label' => 'Phương châm', 'type' => 'text', 'default' => 'Độc lập - Tự do - Hạnh phúc'],
        ];

        // 🔹 Các route CRUD
        $routes = [
            'store' => route('truongkhoa.outline-template.store'),
            'destroyMultiple' => route('truongkhoa.outline-template.destroyMultiple'),
        ];

        $customAddButton = [
            'label' => 'Soạn mẫu đề cương mới',
            'icon' => 'bi bi-file-earmark-plus',
            'route' => route('truongkhoa.outline-template.editor'),
            'confirm' => 'Bạn có muốn chuyển sang trang soạn thảo mẫu đề cương mới không?',
        ];


        return view('shared.crud-template', compact('items', 'columns', 'fields', 'routes'))
            ->with('title', 'Mẫu Đề cương Học phần')
            ->with('layout', 'layouts.apptruongkhoa')
            ->with('customAddButton', $customAddButton);
    }


    public function editor()
    {
        return view('truongkhoa.outline_template_editor')
            ->with('title', 'Soạn thảo Mẫu Đề cương')
            ->with('layout', 'layouts.apptruongkhoa');
    }



    public function edit($id)
    {
        // 🔹 Lấy template chính
        $template = DB::table('outline_templates')->where('id', $id)->first();

        if (!$template) {
            abort(404, 'Không tìm thấy mẫu đề cương');
        }

        // 🔹 Lấy các section kèm nội dung
        $sections = DB::table('outline_section_templates')
            ->where('outline_template_id', $id)
            ->orderBy('order_no')
            ->get();

        return view('truongkhoa.outline_template_editor', compact('template', 'sections'))
            ->with('title', 'Chỉnh sửa mẫu đề cương')
            ->with('layout', 'layouts.apptruongkhoa')
            ->with('isEdit', true);
    }



    /**
     * 🔹 Lưu mẫu đề cương kèm các section
     * Nhận payload dạng:
     * {
     *   "template_meta": {...},
     *   "sections": [{code,title,order_no,default_content}]
     * }
     */
    public function store(Request $r)
    {
        DB::beginTransaction();

        try {
            // Faculty hiện tại
            $facultyId = DB::table('lecture_roles')
                ->where('lecture_id', Auth::user()->lecture_id)
                ->whereNotNull('faculty_id')
                ->value('faculty_id');

            $meta = $r->input('template_meta', []);
            $sections = $r->input('sections', []);

            // ==== Validate thủ công ====
            if (empty($meta['code']) || empty($meta['name'])) {
                throw new \Exception('Thiếu Mã mẫu hoặc Tên mẫu.');
            }
            if (empty($sections)) {
                throw new \Exception('Mẫu đề cương phải có ít nhất một mục (section).');
            }

            // ==== Insert outline_templates ====
            $templateId = DB::table('outline_templates')->insertGetId([
                'faculty_id' => $facultyId,
                'code' => $meta['code'],
                'name' => $meta['name'],
                'description' => $meta['description'] ?? null,
                'is_default' => $meta['is_default'] ?? 0,
                'gov_header' => $meta['gov_header'] ?? 'UBND TP. HỒ CHÍ MINH',
                'university_name' => $meta['university_name'] ?? 'TRƯỜNG ĐH THỦ DẦU MỘT',
                'national_header' => $meta['national_header'] ?? 'CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM',
                'national_motto' => $meta['national_motto'] ?? 'Độc lập - Tự do - Hạnh phúc',
                'major_name' => $meta['major_name'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ==== Insert các section ====
            foreach ($sections as $s) {
                if (empty($s['code']) || empty($s['title'])) {
                    throw new \Exception('Mỗi section phải có code và title.');
                }

                DB::table('outline_section_templates')->insert([
                    'outline_template_id' => $templateId,
                    'code' => $s['code'],
                    'title' => $s['title'],
                    'order_no' => (int)($s['order_no'] ?? 1),
                    'default_content' => $s['default_content'] ?? '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã lưu mẫu đề cương thành công.',
                'id' => $templateId,
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroyMultiple(Request $r)
    {
        DB::table('outline_templates')
            ->whereIn('id', $r->ids ?? [])
            ->delete();

        return response()->json(['success' => true]);
    }
}
