<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;

class VoucherController extends Controller
{
    public function homeVoucher(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->restaurant) {
            return redirect()->back()->with('error', 'Không xác định được nhà hàng.');
        }

        $restaurantId = $user->restaurant->id;

        $vouchers = Voucher::where('restaurant_id', $restaurantId)->get();

        return view('restaurant.voucher.homeVoucher', compact('vouchers'));
    }

    public function createVoucher(Request $request){
        return view('restaurant.voucher.addVouchers');
    }

    public function addVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:vouchers,code',
            'type' => 'required|in:percent,free_shipping',
            'value' => $request->type === 'percent' ? 'required|integer|min:1|max:100' : 'nullable|integer',
            'min_order_value' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        $user = auth()->user();

        if (!$user || $user->role !== 'restaurant' || !$user->restaurant) {
            return response()->json(['error' => 'Không xác định được nhà hàng.'], 403);
        }

        $voucher = new Voucher();
        $voucher->code = $request->code;
        $voucher->type = $request->type;
        $voucher->value = $request->type === 'percent' ? $request->value : 0;
        $voucher->min_order_value = $request->min_order_value ?? 0;
        $voucher->start_date = $request->start_date;
        $voucher->end_date = $request->end_date;
        $voucher->usage_limit = $request->usage_limit ?? 100;
        $voucher->used_count = 0;
        $voucher->is_active = true;
        $voucher->restaurant_id = $user->restaurant->id;

        $voucher->save();

        return response()->json(['success' => 'Tạo voucher thành công!']);
    }


}
