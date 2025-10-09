<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Faculty;
use App\Models\LectureRole;
use App\Models\Lecture;

class KhoaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $universityId = $user->university_id ?? ($user->lecture->university_id ?? null);

        $faculties = Faculty::where('university_id', $universityId)
            ->orderBy('created_at', 'desc')
            ->get();

        $truongKhoa = LectureRole::with(['lecture', 'faculty'])
            ->where('role_id', 1)
            ->whereHas('faculty', fn($q) => $q->where('university_id', $universityId))
            ->get();

        $tkFacultyIds = $truongKhoa->pluck('faculty_id')->toArray();

        $lectureIsTruongBoMon = LectureRole::where('role_id', 2)->pluck('lecture_id')->toArray();

        $lectures = Lecture::where('university_id', $universityId)
            ->whereNotIn('id', $lectureIsTruongBoMon)
            ->whereHas('department', function($q) use ($tkFacultyIds) {
                $q->whereNotIn('faculty_id', $tkFacultyIds);
            })
            ->orderBy('full_name')
            ->get();

        return view('admin.khoa.index', compact('faculties', 'truongKhoa', 'lectures'));
    }

    public function storeTruongKhoa(Request $request)
    {
        $request->validate([
            'lecture_id' => 'required|exists:lectures,id',
            'faculty_id' => 'required|exists:faculties,id',
            'id' => 'nullable|integer',
        ]);

        $user = Auth::user();
        $universityId = $user->university_id ?? ($user->lecture->university_id ?? null);

        $existing = LectureRole::where('role_id', 1)
            ->where('faculty_id', $request->faculty_id)
            ->first();

        if ($existing && (!$request->id || $request->id != $existing->id)) {
            return response()->json(['success' => false, 'message' => 'Khoa này đã có Trưởng Khoa!']);
        }

        $isTruongBM = LectureRole::where('role_id', 2)
            ->where('lecture_id', $request->lecture_id)
            ->exists();

        if ($isTruongBM) {
            return response()->json(['success' => false, 'message' => 'Giảng viên này đang là Trưởng Bộ Môn!']);
        }

        $data = [
            'lecture_id' => $request->lecture_id,
            'faculty_id' => $request->faculty_id,
            'role_id'    => 1,
            'status_id'  => 11,
        ];

        $lectureRole = null;

        if ($request->filled('id')) {
            $lectureRole = LectureRole::updateOrCreate(['id' => $request->id], $data);
        } else {
            $lectureRole = LectureRole::create($data);
        }

        if ($lectureRole && $lectureRole->status_id == 11) {
            $lecture = Lecture::find($request->lecture_id);

            if ($lecture) {
                $existsUser = \App\Models\User::where('lecture_id', $lecture->id)->first();

                if (!$existsUser) {
                    \App\Models\User::create([
                        'university_id' => $universityId,
                        'lecture_id'    => $lecture->id,
                        'role'          => 'truongkhoa',
                        'name'          => $lecture->full_name,
                        'email'         => $lecture->email,
                        'password_hash' => bcrypt('123456'),
                        'user_type'     => 'truongkhoa',
                        'status_id'     => 1,
                    ]);
                } else {
                    $existsUser->update([
                        'role'      => 'truongkhoa',
                        'user_type' => 'truongkhoa',
                    ]);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    public function destroyMultipleTruongKhoa(Request $request)
    {
        $ids = array_map('intval', $request->ids ?? []);
        
        $lectureRoles = LectureRole::whereIn('id', $ids)->where('role_id', 1)->get();

        foreach ($lectureRoles as $lectureRole) {
            $lectureId = $lectureRole->lecture_id;
            $lectureRole->delete();

            $user = \App\Models\User::where('lecture_id', $lectureId)
                    ->where('user_type', 'truongkhoa')
                    ->first();

            if ($user) {
                $user->update([
                    'role' => 'giangvien',
                    'user_type' => 'giangvien',
                ]);
            }
        }

        return response()->json(['success'=>true]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'faculty_name' => 'required|string|max:255',
            'status_id' => 'required|in:1,2',
        ]);

        $normalizeName = function ($name) {
            $name = trim(mb_strtolower($name));
            $name = preg_replace('/^khoa\s+/u', '', $name);
            return $name;
        };

        $normalized = $normalizeName($request->faculty_name);

        $exists = Faculty::where('university_id', $user->university_id)
            ->when($request->filled('id'), function($q) use ($request) {
                $q->where('id', '!=', $request->id);
            })
            ->get()
            ->contains(function($faculty) use ($normalizeName, $normalized) {
                return $normalizeName($faculty->faculty_name) === $normalized;
            });

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Tên khoa đã tồn tại vui lòng thêm mới.'
            ]);
        }

        $data = [
            'faculty_name' => $request->faculty_name,
            'status_id' => $request->status_id,
            'university_id' => $user->university_id,
        ];

        if ($request->filled('id')) {
            Faculty::updateOrCreate(['id' => $request->id], $data);
        } else {
            Faculty::create($data);
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Faculty::where('id', (int)$id)->delete();
        return response()->json(['success'=>true]);
    }

    public function destroyMultiple(Request $request)
    {
        $ids = array_map('intval', $request->ids ?? []);
        Faculty::whereIn('id', $ids)->delete();
        return response()->json(['success'=>true]);
    }
}
