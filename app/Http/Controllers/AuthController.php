<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Restaurant;
use App\Models\Shipper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Đăng ký
    public function register(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'role' => 'required|in:customer,shipper,restaurant',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validated->fails()) {
            return redirect()->back()
                ->withErrors($validated)
                ->withInput()
                ->with('show_signup', true);
        }

        $role = $request->role;

        // ✅ Tạo user trước
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'is_active' => true,
            'is_approved' => false,
        ]);

        // ✅ Tạo bảng phụ gắn với user_id
        $commonData = [
            'user_id' => $user->id,
            'name' => $user->name,
            'phone' => '',
            'avatar' => null,
            'date_of_birth' => null,
            'address' => null,
        ];

        match ($role) {
            'customer'   => Customer::create($commonData),
            'shipper'    => Shipper::create($commonData),
            'restaurant' => Restaurant::create($commonData),
        };

        Auth::login($user);

        return redirect()->route('home')->with('status', 'Đăng ký và đăng nhập thành công!');
    }

    // Đăng nhập
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'customer' => redirect()->route('home'),
                'restaurant' => redirect()->route('home'),
                'shipper' => redirect()->route('home'),
                default => redirect('/login'),
            };
        }

        return back()->with('status', 'Email hoặc mật khẩu không đúng!');
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // Cập nhật thông tin người dùng
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'extra' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Cập nhật thông tin bảng phụ tương ứng
        $profileData = [
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'extra' => $validated['extra'],
        ];
        if (isset($validated['avatar'])) {
            $profileData['avatar'] = $validated['avatar'];
        }

        switch ($user->role) {
            case 'restaurant':
                $user->restaurant?->update(array_merge($profileData, ['name_restaurant' => $validated['name']]));
                break;
            case 'shipper':
                $user->shipper?->update(array_merge($profileData, ['name_shipper' => $validated['name']]));
                break;
            case 'customer':
                $user->customer?->update(array_merge($profileData, ['name_customer' => $validated['name']]));
                break;
        }

        $user->update(['email' => $validated['email']]);

        return redirect()->route('profile.home_info')->with('success', 'Cập nhật thành công!');
    }
}
