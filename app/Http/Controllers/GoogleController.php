<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name'     => $googleUser->getName(),
                    'email'    => $googleUser->getEmail(),
                    'password' => bcrypt('default_password'), 
                    'avatar'   => $googleUser->getAvatar(),
                    'role_id'  => 2 
                ]);
            }

            Auth::login($user);

            return redirect()->intended('/admin/dashboard');

        } catch (\Exception $e) {
            return redirect('/login')->with('status', 'Lỗi đăng nhập bằng Google!');
        }
    }
}
