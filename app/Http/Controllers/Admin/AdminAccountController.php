<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminAccountController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')->get();
        $restaurants = User::where('role', 'restaurant')->get();
        $shippers = User::where('role', 'shipper')->get();

        return view('admin.accounts.index', compact('customers', 'restaurants', 'shippers'));
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->is_approved = true;
        $user->save();

        return redirect()->back()->with('success', 'Duyệt tài khoản thành công.');
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'Xóa tài khoản thành công.');
    }

}


