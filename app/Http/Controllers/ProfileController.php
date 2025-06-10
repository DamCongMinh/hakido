<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

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

        $commonRules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'extra' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
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
        }

        $validated = $request->validate($rules);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        // Cập nhật theo role
        switch ($user->role) {
            case 'customer':
                $customer = $user->customer;
                if ($customer) {
                    $customer->update([
                        'name' => $validated['name'],
                        'phone' => $validated['phone'],
                        'email' => $validated['email'],
                        'address' => $validated['address'],
                        'latitude' => $validated['latitude'] ?? $customer->latitude,
                        'longitude' => $validated['longitude'] ?? $customer->longitude,
                        'avatar' => $avatarPath ?? $customer->avatar,
                        'date_of_birth' => $validated['date_of_birth'] ?? $customer->date_of_birth,
                        'extra' => $validated['extra'] ?? $customer->extra,
                    ]);
                }
                break;

            case 'restaurant':
                $restaurant = $user->restaurant;
                if ($restaurant) {
                    $restaurant->update([
                        'name' => $validated['name'],
                        'phone' => $validated['phone'],
                        'email' => $validated['email'],
                        'address' => $validated['address'],
                        'latitude' => $validated['latitude'] ?? $restaurant->latitude,
                        'longitude' => $validated['longitude'] ?? $restaurant->longitude,
                        'avatar' => $avatarPath ?? $restaurant->avatar,
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
                        'name' => $validated['name'],
                        'phone' => $validated['phone'],
                        'email' => $validated['email'],
                        'address' => $validated['address'],
                        'avatar' => $avatarPath ?? $shipper->avatar,
                        'area' => $validated['area'] ?? $shipper->area,
                        'extra' => $validated['extra'] ?? $shipper->extra,
                    ]);
                }
                break;
        }

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

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }



    public function destroy(Request $request)
    {
        $user = auth()->user();
        $user->delete();
        Auth::logout();
        return redirect('/')->with('success', 'Tài khoản đã được xóa!');
    }

    public function getDistricts($provinceId)
    {
        $districts = District::where('province_id', $provinceId)->get();
        return response()->json($districts);
    }

    // API lấy danh sách phường/xã theo quận
    public function getWards($districtId)
    {
        $wards = Ward::where('district_id', $districtId)->get();
        return response()->json($wards);
    }
}