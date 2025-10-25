<?php

namespace App\Http\Controllers\TruongKhoa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EducationProgramController extends Controller
{
    public function index()
    {
        // $facultyId = Auth::user()->faculty_id;
        $facultyId = DB::table('lecture_roles')
            ->where('lecture_id', Auth::user()->lecture_id)
            ->whereNotNull('faculty_id')
            ->value('faculty_id');

        $items = DB::table('education_programs')
            ->where('faculty_id', $facultyId)
            ->get();

        //cấu hình cột hiển thị trong bảng
        $columns = [
            ['label' => 'Mã CTĐT', 'field' => 'program_code'],
            ['label' => 'Tên CTĐT', 'field' => 'program_name', 'link_to_child' => true],
            ['label' => 'Mã hệ', 'field' => 'education_system_code'],
            ['label' => 'Tên hệ', 'field' => 'education_system_name'],
        ];

        //cấu hình các trường trong form thêm/sửa
        $fields = [
            ['name' => 'program_code', 'label' => 'Mã CTĐT', 'type' => 'text', 'required' => true],
            ['name' => 'program_name', 'label' => 'Tên CTĐT', 'type' => 'text', 'required' => true],
            ['name' => 'education_system_code', 'label' => 'Mã hệ đào tạo', 'type' => 'text', 'required' => true],
            ['name' => 'education_system_name', 'label' => 'Tên hệ đào tạo', 'type' => 'text', 'required' => true],
        ];

        //Route pv ajax
        $routes = [
            'store' => route('truongkhoa.chuongtrinhdaotao.store'),
            'destroyMultiple' => route('truongkhoa.chuongtrinhdaotao.destroyMultiple'),
        ];

        return view('shared.crud-template', compact('items', 'columns', 'fields', 'routes'))
            ->with('title', 'Chương trình đào tạo');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'program_code' => 'required|string|max:100',
            'program_name' => 'required|string|max:250',
            'education_system_code' => 'required|string|max:100',
            'education_system_name' => 'required|string|max:250',
        ]);

        $facultyId = DB::table('lecture_roles')
            ->where('lecture_id', Auth::user()->lecture_id)
            ->whereNotNull('faculty_id')
            ->value('faculty_id');

        $data['faculty_id'] = $facultyId;

        $data['updated_at'] = now();

        if ($r->id) {
            DB::table('education_programs')->where('id', $r->id)->update($data);
        } else {
            $data['created_at'] = now();
            DB::table('education_programs')->insert($data);
        }

        return response()->json(['success' => true]);
    }

    public function destroyMultiple(Request $r)
    {
        DB::table('education_programs')->whereIn('id', $r->ids ?? [])->delete();
        return response()->json(['success' => true]);
    }
}
