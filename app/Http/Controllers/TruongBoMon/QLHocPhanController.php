<?php

namespace App\Http\Controllers\TruongBoMon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QLHocPhanController extends Controller
{
    // ==== helper: danh sách bộ môn mà user TBM quản lý (đang hiệu lực) ====
    private function resolveDepartmentsForTBM($user)
    {
        // Lấy lecture_id từ users
        $lectureId = DB::table('users')->where('id', $user->id)->value('lecture_id');
        if (!$lectureId) return collect(); // chưa map giảng viên ↔ user

        // Lọc theo thời gian hiệu lực
        $now = now();

        // (Tuỳ) lọc theo role 'Trưởng bộ môn' nếu dữ liệu roles có role_name tương ứng
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

    // ==== GET: danh sách học phần + dữ liệu cho modal ====
    public function dshocphan(Request $request)
    {
        $user = $request->user();

        $deptList = $this->resolveDepartmentsForTBM($user);

        // Lấy courses + join tên hiển thị
        $coursesQuery = DB::table('courses as c')
            ->leftJoin('education_programs as ep', 'ep.id', '=', 'c.education_program_id')
            ->leftJoin('departments as d', 'd.id', '=', 'c.department_id')
            ->leftJoin('semesters as s', 's.id', '=', 'c.semester_id')
            ->leftJoin('users as uc', 'uc.id', '=', 'c.created_by')
            ->leftJoin('users as uu', 'uu.id', '=', 'c.updated_by')
            ->selectRaw('c.*, ep.program_name as program_name, d.department_name as department_name, s.semester_name as semester_name, uc.name as creator_name, uu.name as updater_name')
            ->orderBy('c.course_code');

        // TBM: nếu chỉ có 1 bộ môn → chỉ xem bộ môn đó; nếu >1, để xem tất cả mình quản
        if ($deptList->count() === 1) {
            $coursesQuery->where('c.department_id', $deptList->first()->id);
        } elseif ($deptList->count() > 1) {
            $coursesQuery->whereIn('c.department_id', $deptList->pluck('id'));
        }

        $courses = $coursesQuery->paginate(15)->withQueryString();

        // CTĐT: lấy theo program_name
        $programs = DB::table('education_programs')
            ->orderBy('program_name')
            ->get(['id', 'program_name']);

        return view('truongbomon.quanlyhocphan.dshocphan', [
            'courses'  => $courses,
            'programs' => $programs,
            'deptList' => $deptList,
        ]);
    }

    // ==== POST: lưu học phần mới ====
    public function store(Request $request)
    {
        $user = $request->user();
        $deptList = $this->resolveDepartmentsForTBM($user);

        if ($deptList->count() === 0) {
            return back()->withErrors([
                'department_id' => 'Tài khoản chưa được gán Bộ môn (lecture_roles). Vui lòng liên hệ quản trị để cấu hình.'
            ])->withInput();
        }

        // Rule cơ bản
        $rules = [
            'course_code' => ['required', 'string', 'max:100', Rule::unique('courses', 'course_code')],
            'course_name' => ['required', 'string', 'max:250'],
            'credit'      => ['required', 'integer', 'min:1', 'max:10'],
            'education_program_id' => ['required', 'integer', 'exists:education_programs,id'],
        ];

        // Nếu TBM quản >1 bộ môn → bắt buộc chọn trong phạm vi cho phép
        if ($deptList->count() > 1) {
            $rules['department_id'] = ['required', 'integer', Rule::in($deptList->pluck('id')->toArray())];
        }

        $data = $request->validate($rules);

        $departmentId = $deptList->count() === 1
            ? $deptList->first()->id
            : (int) $data['department_id'];

        // created_at/updated_at trong courses là DATE → set 'Y-m-d'
        $today = now()->toDateString();

        DB::table('courses')->insert([
            'course_code'          => $data['course_code'],
            'course_name'          => $data['course_name'],
            'credit'               => $data['credit'],
            'education_program_id' => $data['education_program_id'],
            'department_id'        => $departmentId,
            'semester_id'          => null, // nếu sau này chọn HK thì nhận từ form
            'created_by'           => $user->id,
            'updated_by'           => $user->id,
            'created_at'           => $today,
            'updated_at'           => $today,
        ]);

        return back()->with('ok', 'Đã thêm học phần.');
    }
}
