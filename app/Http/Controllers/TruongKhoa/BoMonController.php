<?php

namespace App\Http\Controllers\TruongKhoa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\LectureRole;
use Illuminate\Support\Facades\Auth;

class BoMonController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $lecture = $user->lecture;

        
        $facultyId = $lecture->department->faculty_id ?? null;

        
        if (!$facultyId) {
            $departments = Department::whereNull('id')->paginate(10);
            $truongBoMon = collect();
        } else {
            $departments = Department::with('faculty.university')
                ->where('faculty_id', $facultyId)
                ->when($request->search, fn($q, $s) => $q->where('department_name', 'like', "%$s%"))
                ->paginate(10);

            $truongBoMon = LectureRole::with(['lecture', 'department'])
            ->where('role_id', 2)
            ->whereHas('department', fn($q) => $q->where('faculty_id', $facultyId))
            ->when($request->search_tbm, function($q, $s) {
                $q->whereHas('lecture', function($sub) use ($s) {
                    $sub->where('full_name', 'like', "%$s%")
                        ->orWhere('email', 'like', "%$s%");
                });
            })
            ->get();

        }
        

        return view('truongkhoa.bomon.index', compact('departments', 'truongBoMon'));
    }
}
