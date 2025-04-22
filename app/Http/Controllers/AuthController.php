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
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:customer,restaurant,shipper',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        Auth::login($user);

        switch ($user->role) {
            case 'customer': return redirect()->route('home');
            case 'restaurant': return redirect()->route('restaurant');
            // case 'restaurant': return redirect()->route('restaurant.redirect');
            case 'shipper': return redirect()->route('shiper');
            default: return redirect('/');
        }
    }

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
                // case 'restaurant': return redirect()->route('restaurant.redirect');
                case 'shipper': return redirect()->route('shiper');
                default: return redirect('/login');
            }
        }

        return back()->with('status', 'Email hoặc mật khẩu không đúng!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }


}
