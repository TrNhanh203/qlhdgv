<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class FirstLoginController extends Controller
{
    public function showChangeForm()
    {
        return view('auth.first-login-change');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        // $user = Auth::user();
        // $user->password_hash = Hash::make($request->password);
        // $user->last_login_at = now();
        // $user->save();

        $user = Auth::user();

        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'password_hash' => Hash::make($request->password),
                'last_login_at' => now(),
                'updated_at' => now(),
            ]);


        // return redirect()->route('dashboard')->with('success', 'Đổi mật khẩu thành công!');
        switch ($user->user_type) {
            case 'superadmin':
                return redirect()->route('superadmin.dashboard');
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'truongkhoa':
                return redirect()->route('truongkhoa.dashboard');
            case 'truongbomon':
                return redirect()->route('truongbomon.dashboard');
            case 'giangvien':
                return redirect()->route('giangvien.dashboard');
            default:
                Auth::logout();
                return redirect()->route('login')->withErrors(['email' => 'Loại tài khoản không hợp lệ']);
        }
    }
}
