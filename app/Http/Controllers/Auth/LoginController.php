<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
   
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
        {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)
                ->where('status_id', 1) 
                ->first();

            if (!$user || !Hash::check($request->password, $user->password_hash)) {
                return back()->withErrors([
                    'email' => 'Email hoặc mật khẩu không đúng',
                ])->withInput();
            }

            if ($user->university && in_array($user->university->status_id, [3])) {
                return back()->withErrors([
                    'email' => 'Tài khoản trường này đang ngừng hoạt động.',
                ])->withInput();
            }

            Auth::login($user);

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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
