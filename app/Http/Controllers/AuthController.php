<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Restaurant;
use App\Models\Shipper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Đăng ký tài khoản mới
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:customer,restaurant,shipper',
        ]);

        $hashedPassword = Hash::make($validated['password']);

        // Tạo user trước
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $hashedPassword,
            'role' => $validated['role'],
        ]);

        // Tạo record theo role
        switch ($validated['role']) {
            case 'customer':
                $customer = Customer::create([
                    'name_customer' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => $hashedPassword,
                    // các trường nullable thì để trống
                    'phone' => null,
                    'address' => null,
                    'avata' => null,
                ]);
                $user->update(['customer_id' => $customer->id]);
                break;

            case 'restaurant':
                $restaurant = Restaurant::create([
                    'user_id' => $user->id,
                    'name_restaurant' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => $hashedPassword,
                    'phone' => null,
                    'address' => null,
                    'avata' => null,
                    'time_open' => null,
                    'time_close' => null,
                    'is_approved' => false,
                    'is_active' => false,
                ]);
                $user->update(['restaurant_id' => $restaurant->id]);
                break;

            case 'shipper':
                $shipper = Shipper::create([
                    'name_shipper' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => $hashedPassword,
                    'phone' => null,
                    'address' => null,
                    'avata' => null,
                    'area' => null,
                ]);
                $user->update(['shipper_id' => $shipper->id]);
                break;
        }

        Auth::login($user);
        return redirect()->route('home')->with('success', 'Đăng ký thành công!');
    }




    // Đăng nhập
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            switch ($user->role) {
                case 'admin': return redirect()->route('admin.dashboard');
                case 'customer': return redirect()->route('home');
                case 'restaurant': return redirect()->route('restaurant');
                case 'shipper': return redirect()->route('shiper');
                default: return redirect('/login');
            }
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

    // Cập nhật thông tin
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
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        switch ($user->role) {
            case 'restaurant':
                $user->restaurant?->update([
                    'name_restaurant' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                    'extra' => $validated['extra'],
                    'avatar' => $validated['avatar'] ?? $user->restaurant->avatar,
                ]);
                break;
            case 'shipper':
                $user->shipper?->update([
                    'name_shipper' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                    'extra' => $validated['extra'],
                    'avatar' => $validated['avatar'] ?? $user->shipper->avatar,
                ]);
                break;
            case 'customer':
                $user->customer?->update([
                    'name_customer' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                    'extra' => $validated['extra'],
                    'avatar' => $validated['avatar'] ?? $user->customer->avatar,
                ]);
                break;
        }

        $user->update([
            'email' => $validated['email']
        ]);

        return redirect()->route('profile.home_info')->with('success', 'Cập nhật thành công!');
    }
}
