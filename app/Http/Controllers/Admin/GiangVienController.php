<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Imports\GiangVienImport;
use Illuminate\Support\Facades\Auth;
use App\Exports\GiangVienExport;
use App\Models\Department;

class GiangVienController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $lectures = Lecture::with('department','status')
            ->where('university_id', $user->university_id)
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        $departments = Department::whereHas('faculty', function ($query) use ($user) {
            $query->where('university_id', $user->university_id);
        })->paginate(10);

        return view('admin.giangvien.index', compact('lectures', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lecturer_code' => 'required|string|max:50',
            'full_name'     => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'required|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'status_id'     => 'required|in:8,9,10',
        ]);

        if (empty($request->id)) {
            Lecture::create([
                'lecturer_code' => $request->lecturer_code,
                'full_name'     => $request->full_name,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'degree'        => $request->degree,
                'major'         => $request->major,
                'department_id' => $request->department_id,
                'university_id' => auth()->user()->university_id,
                'status_id'     => $request->status_id,
            ]);
            $message = 'Thêm giảng viên thành công!';
        } else {
            $lecture = Lecture::findOrFail($request->id);
            $lecture->update([
                'lecturer_code' => $request->lecturer_code,
                'full_name'     => $request->full_name,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'degree'        => $request->degree,
                'major'         => $request->major,
                'department_id' => $request->department_id,
                'status_id'     => $request->status_id,
            ]);
            $message = 'Cập nhật giảng viên thành công!';
        }

        return redirect()->route('admin.giangvien.index')->with('success', $message);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'lecturer_code' => 'required|string|max:50',
            'full_name'     => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'required|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'status_id'     => 'required|in:8,9,10',
        ]);

        $lecture = Lecture::findOrFail($id);

        $lecture->update([
            'lecturer_code' => $request->lecturer_code,
            'full_name'     => $request->full_name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'degree'        => $request->degree,
            'major'         => $request->major,
            'department_id' => $request->department_id,
            'status_id'     => $request->status_id,
        ]);

        return redirect()
            ->route('admin.giangvien.index')
            ->with('success', 'Cập nhật giảng viên thành công!');
    }

    public function destroy($id)
    {
        Log::info("📌 Bắt đầu xóa giảng viên ID: " . $id);

        try {
            $lecture = Lecture::findOrFail($id);
            $lecture->delete();

            Log::info("✅ Xóa thành công giảng viên ID: " . $id);

            return redirect()
                ->route('admin.giangvien.index')
                ->with('success', 'Xóa giảng viên thành công');
        } catch (\Exception $e) {
            Log::error("❌ Lỗi khi xóa giảng viên ID: $id. Chi tiết: " . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi xóa giảng viên.');
        }
    }

    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids', []);

        Log::info("📌 Bắt đầu xóa giảng viên ID(s):", $ids);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có ID nào được chọn!'
            ], 400);
        }

        try {
            $lectures = Lecture::with(['roles', 'teachingDuties', 'examProctorings', 'workloads', 'meetings'])
                ->whereIn('id', $ids)
                ->get();

            if ($lectures->isEmpty()) {
                Log::warning("⚠️ Không tìm thấy giảng viên nào với ID(s): " . implode(',', $ids));
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy giảng viên nào với ID đã chọn!'
                ], 404);
            }

            foreach ($lectures as $lecture) {
                $lecture->roles()->detach();
                $lecture->teachingDuties()->delete();
                $lecture->examProctorings()->delete();
                $lecture->workloads()->delete();
                $lecture->meetings()->detach();
                $lecture->delete();
            }

            Log::info("✅ Đã xóa giảng viên ID(s):", $ids);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa giảng viên thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Lỗi destroyMultiple: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'ids'   => $ids
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi khi xóa: ' . $e->getMessage()
            ], 500);
        }
    }

    public function import(Request $request)
    {
        Log::info("📥 Bắt đầu import giảng viên...");

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        Log::info("✅ File hợp lệ: " . $request->file('file')->getClientOriginalName());

        $user = auth()->user();
        Log::info("👤 User import", [
            'id' => $user->id,
            'faculty_id' => $user->faculty_id,
            'university_id' => $user->university_id
        ]);

        if (!$user->university_id) {
            Log::warning("⚠️ User chưa được gán university_id");
            return back()->withErrors([
                'error' => 'User chưa có university_id. Vui lòng cập nhật thông tin user.'
            ]);
        }

        $import = new GiangVienImport($user->faculty_id, $user->university_id);

        try {
            Excel::import($import, $request->file('file'));
            Log::info("✅ Import Excel hoàn tất (không ném exception)");
        } catch (\Throwable $e) {
            Log::error("❌ Lỗi khi import: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Có lỗi khi import: ' . $e->getMessage()]);
        }

        $errors = $import->getErrors();
        if (!is_array($errors)) $errors = [];

        Log::info("📊 Kết quả import", ['total_errors' => count($errors)]);
        return back()->with([
            'success' => 'Thêm danh sách giảng viên hoàn tất!',
            'errors'  => $errors
        ]);
    }

    public function export()
    {
        $user = auth()->user();
        return Excel::download(new GiangVienExport($user->university_id), 'dsgiangvien.xlsx');
    }
}
