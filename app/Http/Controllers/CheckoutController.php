<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function startCheckout(Request $request)
    {
        if (Auth::check()) {
            // Người dùng đã đăng nhập
            return redirect()->route('checkout.show');
        } else {
            // Khách vãng lai
            return view('web.guest_info');
        }
    }

    public function saveGuestInfo(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        $guestInfo = [
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'province' => $request->province,
        ];

        session(['guest_info' => $guestInfo]);
        

        return redirect()->route('checkout.show');
    }



}
