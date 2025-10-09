<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\EducationProgram;
use App\Models\Faculty;

class CurriculumController extends Controller
{
    private function getEducationSystemsForUser($user)
    {
        $systems = config('education_systems');

        return collect($systems)
            ->filter(fn($sys) => isset($sys['university_id']) && (int)$sys['university_id'] === (int)$user->university_id)
            ->values();
    }

    public function index()
    {
        $user = Auth::user();
        $educationSystems = $this->getEducationSystemsForUser($user);

        $programs = EducationProgram::with('faculty')
            ->whereHas('faculty', fn($q) => $q->where('university_id', $user->university_id))
            ->orderBy('created_at', 'desc')
            ->get();

        $faculties = Faculty::where('university_id', $user->university_id)->get();

        return view('admin.chuongtrinhdaotao.index', compact('programs', 'faculties', 'educationSystems'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $educationSystems = $this->getEducationSystemsForUser($user);
        $validCodes = $educationSystems->pluck('code')->toArray();

        $request->validate([
            'education_system_code' => 'required|string|in:' . implode(',', $validCodes),
            'program_code' => 'required|string|max:100|unique:education_programs,program_code,' . $request->id,
            'program_name' => 'required|string|max:250',
            'faculty_id' => 'required|exists:faculties,id',
        ]);

        $faculty = Faculty::where('id', $request->faculty_id)
            ->where('university_id', $user->university_id)
            ->firstOrFail();

        $educationSystemName = $educationSystems->firstWhere('code', $request->education_system_code)['name'] ?? '';

        if ($request->id) {
            $program = EducationProgram::where('id', $request->id)
                ->whereHas('faculty', fn($q) => $q->where('university_id', $user->university_id))
                ->firstOrFail();

            $program->update([
                'program_code' => $request->program_code,
                'program_name' => $request->program_name,
                'faculty_id' => $faculty->id,
                'education_system_code' => $request->education_system_code,
                'education_system_name' => $educationSystemName,
            ]);
        } else {
            $program = EducationProgram::create([
                'program_code' => $request->program_code,
                'program_name' => $request->program_name,
                'faculty_id' => $faculty->id,
                'education_system_code' => $request->education_system_code,
                'education_system_name' => $educationSystemName,
            ]);
        }

        return response()->json([
            'success' => true,
            'program' => $program
        ]);
    }

    public function destroyMultiple(Request $request)
    {
        $user = Auth::user();
        $ids = $request->ids ?? [];

        try {
            EducationProgram::whereIn('id', $ids)
                ->whereHas('faculty', fn($q) => $q->where('university_id', $user->university_id))
                ->delete();

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('❌ Lỗi xóa nhiều chương trình: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Xóa nhiều thất bại'], 500);
        }
    }
}
