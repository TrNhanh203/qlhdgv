<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        $user = Auth::user();
        $user->password_hash = Hash::make($request->password);
        $user->last_login_at = now(); 
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Đổi mật khẩu thành công!');
    }
}
