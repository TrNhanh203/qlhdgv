<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Lecture;

class TaiKhoanController extends Controller
{
    /**
     * Hiển thị danh sách tài khoản
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $users = User::where('university_id', $user->university_id)->get();

        return view('admin.taikhoan.index', compact('users'));
    }

    /**
     * Lưu tài khoản mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'role'      => 'required|in:truongkhoa,truongbomon,giangvien',
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6',
            'user_type' => 'required|string|max:50',
            'status_id' => 'required|integer',
        ]);

        $user = Auth::user();

        User::create([
            'university_id' => $user->university_id,
            'lecture_id'    => $request->lecture_id ?? null,
            'role'          => $request->role,
            'name'          => $request->name,
            'email'         => $request->email,
            'password_hash' => Hash::make($request->password),
            'user_type'     => $request->user_type,
            'status_id'     => $request->status_id,
            'last_login_at' => null,
            'remember_token'=> null,
        ]);

        return redirect()->route('admin.taikhoan.index')->with('success', 'Tạo tài khoản thành công!');
    }
}
