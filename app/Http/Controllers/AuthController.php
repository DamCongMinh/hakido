<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
    $request->validate([
        'name' => 'required|string|max:100',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6|confirmed',
        'role' => 'required|in:customer,restaurant,shipper'
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => $request->role,
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'user' => $user,
        'token' => $token,
        'message' => 'Đăng ký thành công!'
    ], 201);
    }


    public function login(Request $request)
    {
    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['message' => 'Thông tin đăng nhập không chính xác'], 401);
    }

    $user = User::where('email', $request->email)->first();
    $token = $user->createToken('auth_token')->plainTextToken;

    // Kiểm tra vai trò của người dùng để chuyển hướng
    switch ($user->role) {
        case 'customer':
            return response()->json([
                'user' => $user,
                'token' => $token,
                'message' => 'Đăng nhập thành công!',
                'redirect_url' => '/home',
            ]);
        case 'restaurant':
            return response()->json([
                'user' => $user,
                'token' => $token,
                'message' => 'Đăng nhập thành công!',
                'redirect_url' => '/restaurant', 
            ]);
        case 'shipper':
            return response()->json([
                'user' => $user,
                'token' => $token,
                'message' => 'Đăng nhập thành công!',
                'redirect_url' => '/shiper', 
            ]);
        case 'admin': 
            return response()->json([
                'user' => $user,
                'token' => $token,
                'message' => 'Đăng nhập thành công!',
                'redirect_url' => '/home_admin',
            ]);
        default:
            return response()->json([
                'user' => $user,
                'token' => $token,
                'message' => 'Đăng nhập thành công!',
                'redirect_url' => '/home',
            ]);
        }
    }




}
