<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminAccountController extends Controller
{
    public function account()
    {
        $customers = User::with('customer')
            ->where('role', 'customer')
            ->get()
            ->filter(fn($user) => $user->customer) 
            ->map(function ($user) {
                $profile = $user->customer;
                $profile->user_id = $user->id;
                $profile->is_active = $user->is_active;
                $profile->is_approved = $user->is_approved;
                $profile->email = $user->email;
                $profile->name = $user->name;
                return $profile;
            });

        $restaurants = User::with('restaurant')
            ->where('role', 'restaurant')
            ->get()
            ->filter(fn($user) => $user->restaurant)
            ->map(function ($user) {
                $profile = $user->restaurant;
                $profile->user_id = $user->id;
                $profile->is_active = $user->is_active;
                $profile->is_approved = $user->is_approved;
                $profile->email = $user->email;
                $profile->name = $user->name;
                return $profile;
            });

        $shippers = User::with('shipper')
            ->where('role', 'shipper')
            ->get()
            ->filter(fn($user) => $user->shipper)
            ->map(function ($user) {
                $profile = $user->shipper;
                $profile->user_id = $user->id;
                $profile->is_active = $user->is_active;
                $profile->is_approved = $user->is_approved;
                $profile->email = $user->email;
                $profile->name = $user->name;
                return $profile;
            });

        return view('admin.accounts.index', compact('customers', 'restaurants', 'shippers'));
    }



    public function getProfileInfo()
    {
        $profile = match ($this->role) {
            'customer' => $this->customer,
            'restaurant' => $this->restaurant,
            'shipper' => $this->shipper,
            default => null,
        };

        if ($profile) {
            
            foreach (['email', 'name', 'password', 'is_active', 'is_approved', 'id'] as $field) {
                $profile->$field = $this->$field;
            }

            
            $profile->user_id = $this->id;
        }

        return $profile;
    }





    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        
        
        if (!$user->is_approved) {
            $user->is_approved = 1;
            $user->save();
            
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


