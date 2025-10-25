<?php

namespace App\Http\Controllers\TruongKhoa;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProgramVersionController extends Controller
{
    public function index($program_id)
    {
        $program = DB::table('education_programs')->find($program_id);

        $items = DB::table('outline_program_versions')
            ->where('education_program_id', $program_id)
            ->orderByDesc('id')
            ->get();

        $columns = [
            ['label' => 'Mã phiên bản', 'field' => 'version_code'],
            ['label' => 'Hiệu lực từ', 'field' => 'effective_from'],
            ['label' => 'Hiệu lực đến', 'field' => 'effective_to'],
            ['label' => 'Trạng thái', 'field' => 'status'],
        ];

        $fields = [
            ['name' => 'version_code', 'label' => 'Mã phiên bản (vd: K2023)', 'type' => 'text', 'required' => true],
            ['name' => 'effective_from', 'label' => 'Ngày bắt đầu', 'type' => 'date'],
            ['name' => 'effective_to', 'label' => 'Ngày kết thúc', 'type' => 'date'],
            [
                'name' => 'status',
                'label' => 'Trạng thái',
                'type' => 'select',
                'options' => [
                    'draft' => 'Nháp',
                    'review' => 'Chờ duyệt',
                    'approved' => 'Đã duyệt',
                    'archived' => 'Lưu trữ'
                ]
            ],
        ];

        $routes = [
            'store' => route('truongkhoa.phienban.store', ['program_id' => $program_id]),
            'destroyMultiple' => route('truongkhoa.phienban.destroyMultiple', ['program_id' => $program_id]),
        ];

        return view('shared.crud-template', compact('items', 'columns', 'fields', 'routes'))
            ->with('title', 'Phiên bản CTĐT: ' . $program->program_name)
            ->with('parent_id', $program_id);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'version_code' => 'required|string|max:50',
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date',
            'status' => 'nullable|string|max:20',
        ]);

        // 🔹 Lấy ID cha từ route hoặc input ẩn
        $data['education_program_id'] = $r->parent_id ?: $r->route('program_id');
        $data['updated_at'] = now();

        if ($r->id) {
            DB::table('outline_program_versions')->where('id', $r->id)->update($data);
        } else {
            $data['created_at'] = now();
            DB::table('outline_program_versions')->insert($data);
        }

        return response()->json(['success' => true]);
    }

    public function destroyMultiple(Request $r)
    {
        DB::table('outline_program_versions')->whereIn('id', $r->ids ?? [])->delete();
        return response()->json(['success' => true]);
    }
}
