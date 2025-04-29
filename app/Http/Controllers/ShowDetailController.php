<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Food;
use App\Models\Beverage;
use Illuminate\Http\Request;

class ShowDetailController extends Controller
{
    public function show($type, $id)
    {
        if ($type === 'food') {
            $product = Food::with(['restaurant' => function ($query) {
                $query->withCount(['foods', 'beverages']);
            }])->findOrFail($id);
            
        } elseif ($type === 'beverage') {
            $product = Beverage::with([
                'restaurant' => function ($query) {
                    $query->withCount(['foods', 'beverages']);
                },
                'beverageSizes'
            ])->findOrFail($id);
            
        } else {
            abort(404);
        }

        $user = auth()->user();
        if ($user && $user->role === 'restaurant' && $user->restaurant) {
            // Chỉ cập nhật nếu lần cuối cách hiện tại ít nhất 1 phút
            if (
                !$user->restaurant->last_active_at ||
                $user->restaurant->last_active_at->lt(now()->subMinute())
            ) {
                $user->restaurant->update(['last_active_at' => now()]);
            }
        }

        return view('web.detail_product', compact('product', 'type'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:food,beverage',
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string',
        ]);

        if ($validated['type'] === 'food') {
            $product = Food::findOrFail($validated['product_id']);

            if ($validated['quantity'] > $product->quantity) {
                return back()->with('error', 'Số lượng vượt quá tồn kho.');
            }

            // Xử lý đặt hàng cho food...
            // Ví dụ:
            // Order::create([...]);
            return back()->with('success', 'Đặt hàng thành công!');
        }

        if ($validated['type'] === 'beverage') {
            $beverage = Beverage::with('beverageSizes')->findOrFail($validated['product_id']);

            $selectedSize = $beverage->beverageSizes->firstWhere('size', $validated['size']);

            if (!$selectedSize) {
                return back()->with('error', 'Không tìm thấy size đồ uống.');
            }

            if ($validated['quantity'] > $selectedSize->quantity) {
                return back()->with('error', 'Số lượng vượt quá tồn kho của size đã chọn.');
            }

            // Xử lý đặt hàng cho beverage...
            // Ví dụ:
            // Order::create([...]);
            return back()->with('success', 'Đặt hàng thành công!');
        }

        return back()->with('error', 'Dữ liệu không hợp lệ.');
    }


}

