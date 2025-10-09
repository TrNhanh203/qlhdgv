<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckFirstLogin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && is_null($user->last_login_at)) {
            // Nếu chưa đổi mật khẩu thì chặn về trang đổi mật khẩu
            if (!$request->is('first-change-password') && !$request->is('first-change-password/post')) {
                return redirect()->route('first.change.password.form');
            }
        }

        return $next($request);
    }
}
