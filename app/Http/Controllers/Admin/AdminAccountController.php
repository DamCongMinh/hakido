<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminAccountController extends Controller
{
    public function account()
    {
        $customers = User::where('role', 'customer')->get();
        $restaurants = User::where('role', 'restaurant')->get();
        $shippers = User::where('role', 'shipper')->get();

        return view('admin.accounts.index', compact('customers', 'restaurants', 'shippers'));
    }

    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->is_approved = 1;
        $user->save();

        // Nếu là restaurant, customer hoặc shipper thì update thêm bảng liên quan
        switch ($user->role) {
            case 'restaurant':
                \DB::table('restaurants')->where('user_id', $user->id)->update(['is_approved' => 1]);
                break;
            case 'shipper':
                \DB::table('shippers')->where('user_id', $user->id)->update(['is_approved' => 1]);
                break;
            case 'customer':
                \DB::table('customers')->where('user_id', $user->id)->update(['is_approved' => 1]);
                break;
        }

        return back()->with('success', 'Duyệt tài khoản thành công!');
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


