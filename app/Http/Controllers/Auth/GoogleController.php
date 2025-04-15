<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = Socialite::driver('google')->user();
        // Tạo token (nếu dùng Laravel Passport hoặc Sanctum)
        $token = $user->token;

        return redirect('/')->with([
            'token' => $token,
            'user' => json_encode([
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'avatar' => $user->getAvatar()
            ])
        ]);

        Auth::login($user);

        return redirect('/home');
    }
}
