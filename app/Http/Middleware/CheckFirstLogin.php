<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// app/Http/Middleware/CheckFirstLogin.php
class CheckFirstLogin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && is_null($user->last_login_at)) {
            // CHO QUA các route công khai/đặc biệt để tránh vòng lặp
            if ($request->routeIs([
                'login',
                'login.post',
                'logout',
                'password.*',
                'first-login.form',
                'first-login.update',
                'welcome',
                'home.redirect',
            ])) {
                return $next($request);
            }

            // KHÔNG dùng dd() ở đây — nó chặn luồng điều hướng
            return redirect()->route('first-login.form');
        }

        return $next($request);
    }
}



// class CheckFirstLogin
// {
//     public function handle(Request $request, Closure $next)
//     {
//         $user = Auth::user();

//         // if ($user && is_null($user->last_login_at)) {
//         //     // Nếu chưa đổi mật khẩu thì chặn về trang đổi mật khẩu
//         //     if (!$request->is('first-change-password') && !$request->is('first-change-password/post')) {
//         //         return redirect()->route('first.change.password.form');
//         //     }
//         // }

//         if (
//             $user && is_null($user->last_login_at) &&
//             ! $request->is('first-change-password*')
//         ) {
//             return redirect()->route('first.change.password.form');
//         }


//         return $next($request);
//     }
// }
