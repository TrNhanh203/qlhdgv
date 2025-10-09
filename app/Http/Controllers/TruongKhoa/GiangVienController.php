<?php

namespace App\Http\Controllers\TruongKhoa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lecture;
use Illuminate\Support\Facades\Auth;

class GiangVienController extends Controller
{
    /**
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $lecture = $user->lecture;

        $facultyId = $lecture->department->faculty_id ?? null;

        if (!$facultyId) {
            $giangViens = Lecture::whereNull('id')->paginate(10);
        } else {
            $giangViens = Lecture::with('department.faculty')
                ->whereHas('department', function ($q) use ($facultyId) {
                    $q->where('faculty_id', $facultyId);
                })
                ->when($request->search, fn($q, $s) =>
                    $q->where('full_name', 'like', "%$s%")
                      ->orWhere('email', 'like', "%$s%")
                      ->orWhere('phone', 'like', "%$s%")
                )
                ->paginate(10);
        }

        return view('truongkhoa.giangvien.index', compact('giangViens'));
    }
}