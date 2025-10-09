<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\LectureRole;
use App\Models\Lecture;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class BoMonController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $universityId = $user->university_id;

        $faculties = Faculty::where('university_id', $universityId)
            ->orderBy('faculty_name')
            ->get();

        
        $departments = Department::with('faculty')
            ->whereHas('faculty', fn($q) => $q->where('university_id', $universityId))
            ->join('faculties', 'departments.faculty_id', '=', 'faculties.id')
            ->orderBy('faculties.faculty_name', 'asc')
            ->select('departments.*')
            ->paginate(10);

        
        $roleTbmId = Role::where('id', '2')->value('id'); 
        $roleTkkId = Role::where('id', '1')->value('id');   


       $departmentsWithHead = LectureRole::where('role_id', $roleTbmId)
        ->whereNull('end_date')
        ->pluck('department_id')
        ->toArray();

        $availableDepartments = Department::whereNotIn('id', $departmentsWithHead)
        ->whereHas('faculty', fn($q) => $q->where('university_id', $universityId))
        ->get();

        $excludeLectureIds = LectureRole::whereIn('role_id', [$roleTbmId, $roleTkkId])
        ->whereNull('end_date')
        ->pluck('lecture_id')
        ->toArray();

        $lectures = Lecture::where('university_id', $universityId)
            ->whereIn('department_id', $availableDepartments->pluck('id'))
            ->whereNotIn('id', $excludeLectureIds)
            ->orderBy('full_name')
            ->get();

        $truongBoMon = LectureRole::with(['lecture','department.faculty','faculty'])
            ->where('role_id', $roleTbmId)
            ->whereHas('lecture', fn($q) => $q->where('university_id', $universityId))
            ->orderByDesc('start_date')
            ->paginate(10);

        return view('admin.bomon.index', compact(
            'departments',
            'faculties',
            'truongBoMon',
            'lectures'
        ));
    }
    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!empty($ids)) {
            Department::whereIn('id', $ids)->delete();
        }

        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'faculty_id'       => 'required|exists:faculties,id',
            'department_name'  => 'required|string|max:255',
            'department_code'  => 'nullable|string|max:50|unique:departments,department_code,' . $request->id,
            'status_id'        => 'required|in:1,2',
            'id'               => 'nullable|integer',
        ]);

        $faculty = Faculty::where('id', $request->faculty_id)
            ->where('university_id', $user->university_id)
            ->firstOrFail();

        $exists = Department::where('faculty_id', $faculty->id)
            ->where('department_code', $request->department_code)
            ->where('department_name', $request->department_name)
            ->when($request->id, fn($q) => $q->where('id', '!=', $request->id))
            ->first();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Bộ môn đã tồn tại trong cơ sở dữ liệu!'
            ]);
        }

        if (empty($request->id)) {
            Department::create([
                'department_code' => $request->department_code ?? strtoupper('BM' . rand(100, 999)),
                'department_name' => $request->department_name,
                'faculty_id'      => $faculty->id,
                'status_id'       => $request->status_id,
            ]);
            $message = 'Thêm bộ môn thành công!';
        } else {
            $department = Department::findOrFail($request->id);
            $department->update([
                'department_code' => $request->department_code,
                'department_name' => $request->department_name,
                'faculty_id'      => $faculty->id,
                'status_id'       => $request->status_id,
            ]);
            $message = 'Cập nhật bộ môn thành công!';
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function destroy($id)
    {
        Department::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    


    public function destroyTruongBoMon($id)
{
    $roleTbmId = 2;
    $lr = LectureRole::where('id', $id)->where('role_id', $roleTbmId)->first();

    if ($lr) {
        $lectureId = $lr->lecture_id;
        $lr->delete();

        $stillHasRole = LectureRole::where('lecture_id', $lectureId)
            ->whereNull('end_date')
            ->exists();

        if (!$stillHasRole) {
            \App\Models\User::where('lecture_id', $lectureId)
                ->update([
                    'role' => 'giangvien',
                    'user_type' => 'giangvien',
                ]);
        }
    }

    return response()->json(['success'=>true]);
}




    public function storeTruongBoMon(Request $request)
{
    $user = Auth::user();
    $universityId = $user->university_id;

    $validated = $request->validate([
        'lecture_id' => 'required|exists:lectures,id',
        'department_id' => 'nullable|exists:departments,id',
        'faculty_id' => 'nullable|exists:faculties,id',
        'status_id' => 'nullable|in:11,12',
    ]);

    $roleTbmId = 2;
    $roleTkkId = 1;

    $lecture = Lecture::findOrFail($request->lecture_id);
    if ($lecture->university_id != $universityId) {
        return response()->json(['success'=>false, 'message'=>'Giảng viên không thuộc trường của bạn.']);
    }

    $departmentId = $request->department_id ?: $lecture->department_id;
    $department = Department::with('faculty')->findOrFail($departmentId);
    if ($department->faculty->university_id != $universityId) {
        return response()->json(['success'=>false, 'message'=>'Bộ môn không thuộc trường của bạn.']);
    }

    $facultyId = $request->faculty_id ?: $department->faculty_id;

    $isTruongKhoa = LectureRole::where('lecture_id', $lecture->id)
        ->where('role_id', $roleTkkId)
        ->whereNull('end_date')
        ->exists();
    if ($isTruongKhoa) {
        return response()->json(['success'=>false, 'message'=>'Giảng viên này đang là Trưởng Khoa.']);
    }

    $existing = LectureRole::where('role_id', $roleTbmId)
        ->where('department_id', $departmentId)
        ->whereNull('end_date')
        ->first();

    if ($existing && (!$request->id || $request->id != $existing->id)) {
        return response()->json(['success'=>false, 'message'=>'Bộ môn này đã có Trưởng Bộ Môn.']);
    }

    $data = [
        'lecture_id' => $lecture->id,
        'role_id' => $roleTbmId,
        'faculty_id' => $facultyId,
        'department_id' => $departmentId,
        'status_id' => $request->status_id ?? 12, 
        'start_date' => now(),
    ];

    $lectureRole = null;
    if ($request->filled('id')) {
        $lectureRole = LectureRole::find($request->id);
        if ($lectureRole) {
            $lectureRole->update($data);
        } else {
            return response()->json(['success'=>false,'message'=>'Không tìm thấy Trưởng Bộ Môn.']);
        }
    } else {
        $lectureRole = LectureRole::create($data);
    }

    $userModel = \App\Models\User::where('lecture_id', $lecture->id)->first();
    if ($lectureRole->status_id == 11) {
        if (!$userModel) {
            \App\Models\User::create([
                'university_id' => $universityId,
                'lecture_id'    => $lecture->id,
                'role'          => 'truongbomon',
                'user_type'     => 'truongbomon',
                'name'          => $lecture->full_name,
                'email'         => $lecture->email,
                'password_hash' => bcrypt('123456'),
                'status_id'     => 1,
            ]);
        } else {
            $userModel->update([
                'role' => 'truongbomon',
                'user_type' => 'truongbomon',
            ]);
        }
    } elseif ($lectureRole->status_id == 12) {
        if ($userModel) {
            $userModel->update([
                'role' => 'giangvien',
                'user_type' => 'giangvien',
            ]);
        }
    }

    return response()->json(['success'=>true, 'message'=>'Lưu Trưởng Bộ Môn thành công.']);
}

    
    public function getTruongBoMon($id)
    {
        $user = Auth::user();
        $universityId = $user->university_id;

        $roleTbmId = Role::where('id', '2')->value('id');

        $tbm = LectureRole::with(['lecture','department.faculty','faculty'])
            ->where('id', $id)
            ->where('role_id', $roleTbmId)
            ->firstOrFail();

        if ($tbm->lecture->university_id != $universityId) {
            return response()->json(['success'=>false,'message'=>'Không có quyền truy cập.'], 403);
        }

        return response()->json(['success'=>true,'data'=>$tbm]);
    }

    public function destroyMultipleTruongBoMon(Request $request)
{
    $ids = $request->input('ids', []); 
    $roleTbmId = 2;

    if (!empty($ids)) {
        $lectureRoles = LectureRole::whereIn('id', $ids)->where('role_id', $roleTbmId)->get();

        foreach ($lectureRoles as $lr) {
            $lectureId = $lr->lecture_id;
            $lr->delete();

            $stillHasRole = LectureRole::where('lecture_id', $lectureId)
                ->whereNull('end_date')
                ->exists();

            if (!$stillHasRole) {
                \App\Models\User::where('lecture_id', $lectureId)
                    ->update(['role'=>'giangvien','user_type'=>'giangvien']);
            }
        }
    }

    return response()->json(['success' => true]);
}




}
