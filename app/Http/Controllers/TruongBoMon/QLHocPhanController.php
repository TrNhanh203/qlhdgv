<?php

namespace App\Http\Controllers\TruongBoMon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QLHocPhanController extends Controller
{
    private function resolveDepartmentsForTBM($user)
    {
        $lectureId = DB::table('users')->where('id', $user->id)->value('lecture_id');
        if (!$lectureId) return collect();

        $now = now();
        $tbmRoleIds = DB::table('roles')
            ->whereIn(DB::raw('LOWER(role_name)'), ['trưởng bộ môn', 'truong bo mon', 'truongbomon', 'tbm'])
            ->pluck('id');

        $query = DB::table('lecture_roles')
            ->where('lecture_id', $lectureId)
            ->whereNotNull('department_id')
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            });

        if ($tbmRoleIds->count() > 0) {
            $query->whereIn('role_id', $tbmRoleIds);
        }

        $deptIds = $query->pluck('department_id')->unique();
        if ($deptIds->isEmpty()) return collect();

        return DB::table('departments')
            ->whereIn('id', $deptIds)
            ->orderBy('department_name')
            ->get(['id', 'department_name as name']);
    }

    public function dshocphan(Request $r)
    {
        $user = $r->user();
        $deptList = $this->resolveDepartmentsForTBM($user);
        abort_if($deptList->isEmpty(), 403, 'Tài khoản chưa được gán Bộ môn để quản lý.');

        $q = DB::table('courses as c')
            ->leftJoin('departments as d', 'd.id', '=', 'c.department_id')
            ->select('c.id', 'c.course_code', 'c.course_name', 'd.department_name')
            ->orderBy('c.course_code');

        if ($deptList->count() === 1) {
            $q->where('c.department_id', $deptList->first()->id);
        } else {
            $q->whereIn('c.department_id', $deptList->pluck('id'));
        }

        $items = $q->get();

        // ====== cấu hình cho crud-template ======
        $columns = [
            ['label' => 'Mã HP', 'field' => 'course_code'],
            ['label' => 'Tên học phần', 'field' => 'course_name'],
            ['label' => 'Bộ môn', 'field' => 'department_name'],
        ];

        $fields = [
            ['name' => 'course_code', 'label' => 'Mã học phần', 'type' => 'text', 'required' => true],
            ['name' => 'course_name', 'label' => 'Tên học phần', 'type' => 'text', 'required' => true],
        ];

        if ($deptList->count() > 1) {
            $fields[] = [
                'name' => 'department_id',
                'label' => 'Bộ môn',
                'type' => 'select',
                'options' => $deptList->pluck('name', 'id')->toArray(),
                'required' => true,
            ];
        }

        $routes = [
            'store' => route('truongbomon.quanlyhocphan.store'),
            'destroyMultiple' => route('truongbomon.quanlyhocphan.destroyMultiple'),
        ];

        return view('shared.crud-template', [
            'title' => 'Quản lý học phần',
            'modalTitle' => 'Thêm / Sửa học phần',
            'items' => $items,
            'columns' => $columns,
            'fields' => $fields,
            'routes' => $routes,
        ])->with('layout', 'layouts.appbomon');
    }

    public function store(Request $r)
    {
        $user = $r->user();
        $deptList = $this->resolveDepartmentsForTBM($user);
        if ($deptList->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không có bộ môn hợp lệ']);
        }

        $id = $r->input('id');

        $rules = [
            'course_code' => ['required', 'string', 'max:100', Rule::unique('courses', 'course_code')->ignore($id)],
            'course_name' => ['required', 'string', 'max:255'],
        ];

        if ($deptList->count() > 1) {
            $rules['department_id'] = ['required', 'integer', Rule::in($deptList->pluck('id')->toArray())];
        }

        $data = $r->validate($rules);

        $departmentId = $deptList->count() === 1
            ? $deptList->first()->id
            : (int)$data['department_id'];

        $payload = [
            'course_code'   => $data['course_code'],
            'course_name'   => $data['course_name'],
            'department_id' => $departmentId,
            'updated_by'    => $user->id,
            'updated_at'    => now(),
        ];

        if ($id) {
            DB::table('courses')->where('id', $id)->update($payload);
        } else {
            $payload['created_by'] = $user->id;
            $payload['created_at'] = now();
            DB::table('courses')->insert($payload);
        }

        return response()->json(['success' => true]);
    }

    public function destroyMultiple(Request $r)
    {
        $user = $r->user();
        $deptList = $this->resolveDepartmentsForTBM($user);
        if ($deptList->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không có bộ môn hợp lệ']);
        }

        $ids = (array)$r->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Chưa chọn học phần nào']);
        }

        DB::table('courses')
            ->whereIn('id', $ids)
            ->whereIn('department_id', $deptList->pluck('id'))
            ->delete();

        return response()->json(['success' => true]);
    }
}
