<?php

namespace App\Http\Controllers\TruongKhoa;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PLOController extends Controller
{
    // 🧱 Load view + dữ liệu
    public function index($version_id)
    {
        $version = DB::table('outline_program_versions')->find($version_id);
        $program = DB::table('education_programs')->find($version->education_program_id);

        $plos = DB::table('outline_plos')
            ->where('program_version_id', $version_id)
            ->orderBy('id')
            ->get()
            ->map(function ($plo) {
                $plo->pis = DB::table('outline_pis')
                    ->where('plo_id', $plo->id)
                    ->orderBy('id')
                    ->get();
                return $plo;
            });

        return view('truongkhoa.plo-pi', compact('plos', 'version_id', 'version', 'program'))
            ->with('title', 'Quản lý PLO và PI')
            ->with('layout', 'layouts.apptruongkhoa');
    }

    // ➕ Thêm hoặc sửa PLO
    public function storePLO(Request $r, $version_id)
    {
        $data = $r->validate([
            'code' => 'required|string|max:50',
            'description' => 'required|string',
        ]);
        $data['program_version_id'] = $version_id;
        $data['updated_at'] = now();

        if ($r->id) {
            DB::table('outline_plos')->where('id', $r->id)->update($data);
        } else {
            $data['created_at'] = now();
            DB::table('outline_plos')->insert($data);
        }

        return response()->json(['success' => true]);
    }

    // ❌ Xóa PLO (kèm PI con)
    public function deletePLO(Request $r)
    {
        DB::table('outline_pis')->where('plo_id', $r->id)->delete();
        DB::table('outline_plos')->where('id', $r->id)->delete();
        return response()->json(['success' => true]);
    }

    // ➕ Thêm hoặc sửa PI
    public function storePI(Request $r)
    {
        $data = $r->validate([
            'plo_id' => 'required|integer',
            'code' => 'required|string|max:50',
            'description' => 'required|string',
        ]);
        $data['updated_at'] = now();

        if ($r->id) {
            DB::table('outline_pis')->where('id', $r->id)->update($data);
        } else {
            $data['created_at'] = now();
            DB::table('outline_pis')->insert($data);
        }

        return response()->json(['success' => true]);
    }

    // ❌ Xóa PI
    public function deletePI(Request $r)
    {
        DB::table('outline_pis')->where('id', $r->id)->delete();
        return response()->json(['success' => true]);
    }
}
