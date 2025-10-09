<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Attachment;
use App\Models\Course;
use App\Models\ModuleType;

class CourseSyllabusController extends Controller
{
    private $moduleTypeId;

    public function __construct()
    {
        $this->moduleTypeId = ModuleType::where('name', 'course_syllabus')->value('id');
    }

    public function index(Request $request)
    {
        $syllabuses = Attachment::with(['course', 'uploader'])
            ->where('module_type_id', $this->moduleTypeId)
            ->orderByDesc('uploaded_at')
            ->get();

        $courses = Course::all();

        return view('admin.decuonghocphan.index', compact('syllabuses', 'courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'file' => 'required|mimes:pdf,doc,docx|max:5120',
            'id' => 'nullable|integer'
        ]);

        $path = $request->file('file')->store('syllabuses', 'public');

        $data = [
            'module_type_id' => $this->moduleTypeId,
            'entity_id' => $request->course_id,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_path' => $path,
            'uploaded_by' => Auth::id(),
            'uploaded_at' => now(),
        ];

        if ($request->filled('id')) {
            $syllabus = Attachment::findOrFail($request->id);
            Storage::disk('public')->delete($syllabus->file_path); 
            $syllabus->update($data);
        } else {
            Attachment::create($data);
        }

        return response()->json(['success' => true]);
    }
    public function destroy($id)
    {
        $syllabus = Attachment::findOrFail($id);
        Storage::disk('public')->delete($syllabus->file_path);
        $syllabus->delete();

        return response()->json(['success' => true]);
    }

   
    public function destroyMultiple(Request $request)
    {
        $ids = array_map('intval', $request->ids ?? []);
        $syllabuses = Attachment::whereIn('id', $ids)
            ->where('module_type_id', $this->moduleTypeId)
            ->get();

        foreach ($syllabuses as $s) {
            Storage::disk('public')->delete($s->file_path);
            $s->delete();
        }

        return response()->json(['success' => true]);
    }
}
