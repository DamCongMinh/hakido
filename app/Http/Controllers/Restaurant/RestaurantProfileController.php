<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RestaurantProfileController extends Controller
{
    public function create()
    {
        // Kiểm tra nếu đã có hồ sơ thì không cho tạo lại
        if (Auth::user()->restaurant) {
            return redirect()->route('restaurant.dashboard')->with('info', 'Bạn đã có hồ sơ nhà hàng.');
        }

        return view('restaurant.create'); // View bạn vừa gửi
    }

    public function store(Request $request)
    {
        $request->validate([
            'restaurant_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();

        // Kiểm tra lại để tránh trùng hồ sơ
        if ($user->restaurant) {
            return redirect()->route('restaurant.dashboard')->with('info', 'Hồ sơ nhà hàng đã tồn tại.');
        }

        $logoPath = $request->hasFile('logo')
            ? $request->file('logo')->store('logos', 'public')
            : null;

        Restaurant::create([
            'user_id' => $user->id,
            'restaurant_name' => $request->restaurant_name,
            'address' => $request->address,
            'phone' => $request->phone,
            'description' => $request->description,
            'logo' => $logoPath,
        ]);

        return redirect()->route('restaurant.dashboard')->with('success', 'Tạo hồ sơ nhà hàng thành công!');
    }

    public function dashboard()
    {
        $restaurant = Auth::user()->restaurant;

        if (!$restaurant) {
            return redirect()->route('restaurant.create')->with('warning', 'Bạn cần thiết lập hồ sơ nhà hàng trước.');
        }

        return view('restaurant.dashboard', compact('restaurant'));
    }
}
