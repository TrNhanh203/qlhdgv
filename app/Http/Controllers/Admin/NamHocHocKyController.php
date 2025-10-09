<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\AcademicYear;
use App\Models\Semester;

class NamHocHocKyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $academicYears = AcademicYear::where('university_id', $user->university_id)
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        $semesters = Semester::with('academicYear')
            ->whereHas('academicYear', fn($q) => $q->where('university_id', $user->university_id))
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('admin.namhochocky.index', compact('academicYears', 'semesters'));
    }

    public function storeYear(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                'year_code'   => 'required|string|max:20',
                'start_date'  => 'required|date',
                'end_date'    => 'required|date|after_or_equal:start_date',
                'id'          => 'nullable|integer',
            ]);

            $data = [
                'year_code'     => $request->year_code,
                'start_date'    => $request->start_date,
                'end_date'      => $request->end_date,
                'university_id' => $user->university_id,
            ];

            $exists = AcademicYear::where('university_id', $user->university_id)
                ->where('year_code', $data['year_code'])
                ->where('start_date', $data['start_date'])
                ->where('end_date', $data['end_date'])
                ->when($request->id, fn($q) => $q->where('id', '!=', $request->id))
                ->first();

            if ($exists) {
                return redirect()->back()->with('error', 'Năm học đã tồn tại!')->withInput();
            }

            AcademicYear::updateOrCreate(['id' => $request->id], $data);

            return redirect()->back()->with('success', $request->id ? 'Cập nhật năm học thành công!' : 'Thêm năm học mới thành công!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in storeYear: ', $e->errors());
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('storeYear error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi lưu năm học: ' . $e->getMessage());
        }
    }

    public function storeSemester(Request $request)
    {
        try {
            $request->validate([
                'semester_name'   => 'required|string|max:50',
                'order_number'    => 'required|integer|min:1',
                'academic_year_id'=> 'required|integer|exists:academic_years,id',
                'id'              => 'nullable|integer',
            ]);

            $data = [
                'semester_name'   => $request->semester_name,
                'order_number'    => $request->order_number,
                'academic_year_id'=> $request->academic_year_id,
            ];

            $exists = Semester::where('academic_year_id', $data['academic_year_id'])
                ->where('semester_name', $data['semester_name'])
                ->where('order_number', $data['order_number'])
                ->when($request->id, fn($q) => $q->where('id', '!=', $request->id))
                ->first();

            if ($exists) {
                return redirect()->back()->with('error', 'Học kỳ đã tồn tại!')->withInput();
            }

            Semester::updateOrCreate(['id' => $request->id], $data);

            return redirect()->back()->with('success', $request->id ? 'Cập nhật học kỳ thành công!' : 'Thêm học kỳ mới thành công!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in storeSemester: ', $e->errors());
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('storeSemester error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi lưu học kỳ: ' . $e->getMessage());
        }
    }

    public function destroyYear($id)
    {
        AcademicYear::where('id', (int)$id)->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa năm học']);
    }

    public function destroySemester($id)
    {
        Semester::where('id', (int)$id)->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa học kỳ']);
    }

    public function deleteYears(Request $request)
    {
        $ids = $request->input('ids', []);
        AcademicYear::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', 'Đã xóa các năm học đã chọn!');
    }

    public function deleteSemesters(Request $request)
    {
        $ids = $request->input('ids', []);
        Semester::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', 'Đã xóa các học kỳ đã chọn!');
    }
}
