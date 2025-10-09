<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        \Log::info('CheckRole Middleware - User: ' . $user->email . ', User Type: ' . $user->user_type);
        \Log::info('Required roles: ' . implode(', ', $roles));

        if (in_array($user->user_type, $roles)) {
            \Log::info('User has required role, proceeding...');
            return $next($request);
        }

        \Log::info('User does not have required role, redirecting...');

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
                return redirect()->route('login')->withErrors(['email' => 'Không có quyền truy cập']);
        }
    }
} 