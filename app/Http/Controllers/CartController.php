<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Food;
use App\Models\Beverage;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:food,beverage',
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string',
        ]);

        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thêm vào giỏ hàng.');
        }

        $cart = $user->cart()->firstOrCreate([]);

        $query = $cart->items()
            ->where('product_id', $validated['product_id'])
            ->where('product_type', $validated['type']);

        if ($validated['type'] === 'beverage') {
            $query->where('size', $validated['size']);
        }

        $existingItem = $query->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $validated['quantity']);
        } else {
            if ($validated['type'] === 'food') {
                $food = Food::findOrFail($validated['product_id']);
                $price = $food->old_price * (100 - $food->discount_percent) / 100;
            } else {
                $beverage = Beverage::with('beverageSizes')->findOrFail($validated['product_id']);
                $sizeObj = $beverage->beverageSizes->firstWhere('size', $validated['size']);
                if (!$sizeObj) return back()->with('error', 'Không tìm thấy size');
                $price = $sizeObj->old_price * (100 - $sizeObj->discount_percent) / 100;
            }

            $cart->items()->create([
                'product_id' => $validated['product_id'],
                'product_type' => $validated['type'],
                'size' => $validated['size'] ?? null,
                'unit_price' => $price,
                'quantity' => $validated['quantity'],
            ]);
        }

        return back()->with('success', 'Đã thêm vào giỏ hàng!');
    }

    public function show()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem giỏ hàng.');
        }

        $cart = $user->cart()->with(['items.food', 'items.beverage'])->first();

        return view('web.cart', ['cart' => $cart]);
    }

    // ✅ GET /cart/checkout (hiển thị form thanh toán)
    public function showCheckout()
    {
        $user = auth()->user();
        $cart = $user->cart()->with('items')->first();

        $cartItems = [];

        if ($cart) {
            foreach ($cart->items as $item) {
                if ($item->product_type === 'food') {
                    $product = Food::find($item->product_id);
                    $name = $product?->name ?? 'Không rõ';
                    $image = $product?->image ?? null;
                } else {
                    $product = Beverage::find($item->product_id);
                    $size = $item->size;
                    $name = $product ? $product->name . ' (Size ' . $size . ')' : 'Không rõ';
                    $image = $product?->image ?? null;
                }

                $cartItems[] = [
                    'product_id' => $item->product_id,
                    'product_type' => $item->product_type,
                    'size' => $item->size,
                    'name' => $name,
                    'price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'total' => $item->unit_price * $item->quantity,
                    'image' => $image,
                ];
            }
        }

        $totalAmount = collect($cartItems)->sum('total');

        return view('web.checkout', [
            'items' => $cartItems,
            'totalAmount' => $totalAmount,
        ]);
    }

    // ✅ POST /cart/checkout (submit đơn hàng)
    public function processCheckout(Request $request)
    {
        $selectedItems = $request->input('selected_items');

        if (!$selectedItems || !is_array($selectedItems)) {
            return back()->with('error', 'Vui lòng chọn sản phẩm để thanh toán.');
        }

        $user = auth()->user();
        $cart = $user->cart;
        $cartItems = [];

        foreach ($selectedItems as $data) {
            if (empty($data['selected']) || !isset($data['product_id'], $data['product_type'])) {
                continue;
            }

            $query = $cart->items()
                ->where('product_id', $data['product_id'])
                ->where('product_type', $data['product_type']);

            if ($data['product_type'] === 'beverage') {
                $query->where('size', $data['size'] ?? null);
            }

            $item = $query->first();

            if ($item) {
                if ($item->product_type === 'food') {
                    $product = Food::find($item->product_id);
                    $productName = $product?->name ?? 'Không rõ';
                    $image = $product?->image;
                } else {
                    $product = Beverage::find($item->product_id);
                    $productName = $product ? $product->name . ' (Size ' . $item->size . ')' : 'Không rõ';
                    $image = $product?->image;
                }

                $cartItems[] = [
                    'product_id' => $item->product_id,
                    'product_type' => $item->product_type,
                    'size' => $item->size,
                    'name' => $productName,
                    'image' => $image,
                    'quantity' => $item->quantity,
                    'price' => $item->unit_price,
                    'total' => $item->unit_price * $item->quantity,
                ];
            }
        }

        if (empty($cartItems)) {
            return back()->with('error', 'Không có sản phẩm hợp lệ để thanh toán.');
        }

        $totalAmount = collect($cartItems)->sum('total');

        return view('web.checkout', [
            'items' => $cartItems,
            'totalAmount' => $totalAmount,
        ]);
    }

    // public function removeItem(Request $request)
    // {
    //     $request->validate([
    //         'product_id' => 'required|integer',
    //         'product_type' => 'required|in:food,beverage',
    //         'size' => 'nullable|string',
    //     ]);

    //     $user = auth()->user();
    //     if (!$user || !$user->cart) {
    //         return back()->with('error', 'Không thể thực hiện thao tác.');
    //     }

    //     $query = $user->cart->items()
    //         ->where('product_id', $request->product_id)
    //         ->where('product_type', $request->product_type);

    //     if ($request->product_type === 'beverage') {
    //         $query->where('size', $request->size);
    //     }

    //     $query->delete();

    //     return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    // }


    public function clear()
    {
        $user = auth()->user();
        if ($user && $user->cart) {
            $user->cart->items()->delete();
        }
        return back()->with('success', 'Đã xóa giỏ hàng');
    }
}
