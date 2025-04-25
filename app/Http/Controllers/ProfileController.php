<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function home_info()
    {
        $user = auth()->user();
        return view('profile.home_info', compact('user'));
    }

    public function showChangePasswordForm()
    {
        return view('profile.change_password');
    }
    

    public function update(Request $request)
    {
        $user = auth()->user();

        // Validate dữ liệu
        $commonRules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'extra' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];

        switch ($user->role) {
            case 'customer':
                $rules = array_merge($commonRules, [
                    'date_of_birth' => 'nullable|date',
                ]);
                break;

            case 'restaurant':
                $rules = array_merge($commonRules, [
                    'time_open' => 'nullable|date_format:H:i',
                    'time_close' => 'nullable|date_format:H:i',
                    'is_active' => 'nullable|boolean',
                ]);
                break;

            case 'shipper':
                $rules = array_merge($commonRules, [
                    'area' => 'nullable|string|max:255',
                ]);
                break;

            default:
                $rules = $commonRules;
                break;
        }

        $validated = $request->validate($rules);

        // Upload avatar nếu có
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        // Cập nhật bảng phụ
        switch ($user->role) {
            case 'customer':
                $customer = $user->customer;
                if ($customer) {
                    $customer->update([
                        'name_customer' => $validated['name'],
                        'phone' => $validated['phone'],
                        'email' => $validated['email'],
                        'address' => $validated['address'],
                        'avata' => $validated['avatar'] ?? $customer->avata,
                        'date_of_birth' => $validated['date_of_birth'] ?? $customer->date_of_birth,
                        'extra' => $validated['extra'] ?? $customer->extra,
                    ]);
                }
                break;

            case 'restaurant':
                $restaurant = $user->restaurant;
                if ($restaurant) {
                    $restaurant->update([
                        'name_restaurant' => $validated['name'],
                        'phone' => $validated['phone'],
                        'email' => $validated['email'],
                        'address' => $validated['address'],
                        'avata' => $validated['avatar'] ?? $restaurant->avata,
                        'time_open' => $validated['time_open'] ?? $restaurant->time_open,
                        'time_close' => $validated['time_close'] ?? $restaurant->time_close,
                        'is_active' => $validated['is_active'] ?? $restaurant->is_active,
                        'extra' => $validated['extra'] ?? $restaurant->extra,
                    ]);
                }
                break;

            case 'shipper':
                $shipper = $user->shipper;
                if ($shipper) {
                    $shipper->update([
                        'name_shipper' => $validated['name'],
                        'phone' => $validated['phone'],
                        'email' => $validated['email'],
                        'address' => $validated['address'],
                        'avata' => $validated['avatar'] ?? $shipper->avata,
                        'area' => $validated['area'] ?? $shipper->area,
                        'extra' => $validated['extra'] ?? $shipper->extra,
                    ]);
                }
                break;
        }

        // Cập nhật bảng users (đồng bộ name và email)
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        return redirect()->route('profile.home_info')->with('success', 'Cập nhật thành công!');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $hashedNewPassword = Hash::make($request->new_password);

        // Cập nhật ở bảng users
        $user->password = $hashedNewPassword;
        $user->save();

        // Đồng bộ mật khẩu sang bảng phụ theo role
        match ($user->role) {
            'customer' => $user->customer?->update(['password' => $hashedNewPassword]),
            'restaurant' => $user->restaurant?->update(['password' => $hashedNewPassword]),
            'shipper' => $user->shipper?->update(['password' => $hashedNewPassword]),
            default => null,
        };

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }




    public function destroy(Request $request)
    {
        $user = auth()->user();
        $user->delete();
        Auth::logout();
        return redirect('/')->with('success', 'Tài khoản đã được xóa!');
    }
}
