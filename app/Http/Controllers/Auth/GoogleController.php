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

    // Tìm hoặc tạo người dùng trong DB
    $user = User::updateOrCreate(
        ['email' => $googleUser->getEmail()],
        [
            'name' => $googleUser->getName(),
            // có thể thêm google_id, avatar,...
        ]
    );

    // Đăng nhập user
    Auth::login($user);

    // Tạo token Sanctum
    $token = $user->createToken('google_token')->plainTextToken;

    // Chuyển hướng về trang home kèm dữ liệu trong session (nếu cần)
    return redirect('/home')->with([
        'token' => $token,
        'user' => json_encode([
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $googleUser->getAvatar()
        ])
    ]);
}

}
