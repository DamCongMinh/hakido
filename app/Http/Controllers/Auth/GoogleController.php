<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http; 
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // 
    
    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Tải ảnh từ Google avatar URL
            $googleAvatarUrl = $googleUser->getAvatar();
            $response = Http::get($googleAvatarUrl);
            $avatarPath = null;

            if ($response->successful()) {
                // Tạo tên file duy nhất và lưu
                $avatarFilename = 'avatars/' . uniqid() . '.jpg';
                Storage::disk('public')->put($avatarFilename, $response->body());
                $avatarPath = $avatarFilename;
            }

            // Tạo hoặc cập nhật user
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $avatarPath,
                    'role' => 'customer',
                    'is_active' => 1,
                    'is_approved' => 1,
                    'password' => bcrypt(Str::random(16)),
                ]
            );

            // Tạo bản ghi customer nếu chưa có
            if ($user->role === 'customer' && !$user->customer) {
                Customer::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $avatarPath,
                    'phone' => '',
                ]);
            }

            Auth::login($user);
            return redirect('/home');

        } catch (\Exception $e) {
            Log::error(' Lỗi khi đăng nhập Google: ' . $e->getMessage());
            return redirect('/login')->withErrors([
                'google_error' => 'Đăng nhập Google thất bại: ' . $e->getMessage()
            ]);
        }
    }

}
