<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\University;
use App\Models\StatusCode;

class UniversityController extends Controller
{
    public function index()
    {
        $universities = University::latest()->paginate(10);
        $statusCodes  = StatusCode::all();

        $firstUni = $universities->first();
        $universityCodeShort = $firstUni?->code_short;
        $universityLogo      = $firstUni?->logo_url;

        return view(
            'superadmin.universities.index',
            compact('universities', 'universityCodeShort', 'universityLogo','statusCodes')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'university_name' => 'required|string|max:255',
            'university_type' => 'nullable|string|max:50',
            'address'         => 'nullable|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:100',
            'website'         => 'nullable|string|max:100',
            'logo'            => 'nullable|string|max:255',
            'status_id'       => 'required|integer|in:1,3',
        ]);

        $message = $request->id
            ? tap(University::findOrFail($request->id))->update($data) && 'Cập nhật trường thành công!'
            : tap(University::create($data)) && 'Thêm trường thành công!';

        return $request->expectsJson()
            ? response()->json(['success' => true, 'message' => $message])
            : redirect()->route('superadmin.university.index')->with('success', $message);
    }

    public function destroy($id, Request $request)
    {
        University::findOrFail($id)->delete();
        $message = 'Xóa trường thành công!';

        return $request->expectsJson()
            ? response()->json(['success' => true, 'message' => $message])
            : redirect()->route('superadmin.university.index')->with('success', $message);
    }

    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        if ($ids) University::whereIn('id', $ids)->delete();

        $message = 'Đã xóa các trường được chọn!';

        return $request->expectsJson()
            ? response()->json(['success' => true, 'message' => $message])
            : redirect()->route('superadmin.university.index')->with('success', $message);
    }
}
