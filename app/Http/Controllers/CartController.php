<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Food;
use App\Models\Beverage;
use App\Models\PendingPayment;
use App\Models\Voucher;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function add(Request $request)
    {
        dd($request);
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

        $cart = $user->cart()->firstOrCreate([
            'user_id' => $user->id,
        ]);

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
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm vào giỏ hàng!'
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

    //  hiển thị form thanh toán
    public function showCheckout(Request $request)
    {
        $user = auth()->user();
        $cart = $user->cart()->with('items')->first();
        $customer = $user->customer;
    
        if (!$cart || !$customer) {
            return redirect()->route('cart.show')->with('error', 'Không có sản phẩm để thanh toán.');
        }
    
        $groupedItems = [];
        $restaurantShippingFees = [];
        $restaurantDistances = [];
        $restaurantNames = [];
    
        foreach ($cart->items as $item) {
            if ($item->product_type === 'food') {
                $product = Food::find($item->product_id);
            } else {
                $product = Beverage::find($item->product_id);
            }
    
            if (!$product) continue;
    
            $restaurantId = $product->restaurant_id;
    
            if (!isset($restaurantNames[$restaurantId])) {
                $restaurant = $product->restaurant;
                $restaurantNames[$restaurantId] = $restaurant ? $restaurant->name : 'Không rõ';
            }
    
            $groupedItems[$restaurantId][] = [
                'product_id' => $item->product_id,
                'product_type' => $item->product_type,
                'size' => $item->size,
                'name' => $product->name . ($item->size ? " (Size {$item->size})" : ''),
                'image' => $product->image,
                'price' => $item->unit_price,
                'quantity' => $item->quantity,
                'total' => $item->unit_price * $item->quantity,
            ];
    
            if (!isset($restaurantShippingFees[$restaurantId])) {
                $restaurant = $product->restaurant;
            
                if (
                    $restaurant && $restaurant->latitude && $restaurant->longitude &&
                    $customer->latitude && $customer->longitude
                ) {
                    $distance = $this->haversineDistance(
                        $restaurant->latitude,
                        $restaurant->longitude,
                        $customer->latitude,
                        $customer->longitude
                    );
            
                    $restaurantDistances[$restaurantId] = $distance;
            
                    if ($distance < 10) {
                        $shippingFee = 15000;
                    } elseif ($distance < 20) {
                        $shippingFee = 25000;
                    } elseif ($distance <= 30) {
                        $shippingFee = 35000;
                    } else {
                        $shippingFee = 50000;
                    }
            
                    $restaurantShippingFees[$restaurantId] = $shippingFee;
                } else {
                    $restaurantDistances[$restaurantId] = null;
                    $restaurantShippingFees[$restaurantId] = 0;
                }
            }
        }
    
        // Tính tổng tiền theo từng nhà hàng
        $restaurantTotalAmounts = [];
        $restaurantTotalSums = [];
    
        foreach ($groupedItems as $restaurantId => $items) {
            $totalItems = collect($items)->sum('total');
            $shipping = $restaurantShippingFees[$restaurantId] ?? 0;
    
            $restaurantTotalAmounts[$restaurantId] = $totalItems;
            $restaurantTotalSums[$restaurantId] = $totalItems + $shipping;
        }
    
        $totalAmount = array_sum($restaurantTotalAmounts);
        $totalShippingFee = array_sum($restaurantShippingFees);
        $finalTotal = array_sum($restaurantTotalSums);
    
        // $voucherData = session('voucher_data');
        // $discount = 0;
        // $voucher = null;
    
        // if ($voucherData) {
        //     $voucher = \App\Models\Voucher::find($voucherData['id']);
    
        //     if ($voucher && $voucher->is_active && (is_null($voucher->end_date) || $voucher->end_date > now())) {
        //         if ($voucher->type === 'percent') {
        //             $discount = ($voucher->value / 100) * $totalAmount;
        //         } elseif ($voucher->type === 'free_shipping') {
        //             $discount = $totalShippingFee;
        //         }
    
        //         $finalTotal -= $discount;
        //     } else {
                
        //         session()->forget('voucher_data');
        //     }
        // }

        return view('web.checkout', [
            'groupedItems' => $groupedItems,
            'restaurantDistances' => $restaurantDistances,
            'restaurantShippingFees' => $restaurantShippingFees,
            'restaurantNames' => $restaurantNames,
            'restaurantTotalAmounts' => $restaurantTotalAmounts,
            'restaurantTotalSums' => $restaurantTotalSums,
            'totalAmount' => $totalAmount,
            'totalShippingFee' => $totalShippingFee,
            'finalTotal' => $finalTotal,
            'customer' => $customer,
            // 'voucher' => $voucher, 
            // 'discount' => $discount, 
        ]);
    }

    public function applyVoucher(Request $request)
    {
        $voucherCode = $request->input('voucher_code');
        $groupedItems = $request->input('groupedItems', []);
        $shippingFees = $request->input('shippingFees', []);
    
        // Tìm voucher
        $voucher = \App\Models\Voucher::where('code', $voucherCode)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>', now());
            })
            ->first();
    
        if (!$voucher) {
            session()->forget(['code', 'id', 'type', 'value', 'discount']);
            return response()->json(['success' => false, 'message' => 'Mã không hợp lệ hoặc đã hết hạn.']);
        }
    
        $restaurantId = $voucher->restaurant_id;
        if (!isset($groupedItems[$restaurantId])) {
            return response()->json(['success' => false, 'message' => 'Voucher này không áp dụng cho nhà hàng đã chọn.']);
        }
    
        $items = $groupedItems[$restaurantId];
        $totalAmount = collect($items)->sum('total');
        $shippingFee = $shippingFees[$restaurantId] ?? 0;
    
        // Kiểm tra điều kiện đơn hàng tối thiểu
        if ($voucher->min_order_value && $totalAmount < $voucher->min_order_value) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng chưa đạt giá trị tối thiểu để sử dụng mã giảm giá.']);
        }
    
        $discount = 0;
        if ($voucher->type === 'percent') {
            $discount = ($voucher->value / 100) * $totalAmount;
        } elseif ($voucher->type === 'free_shipping') {
            $discount = $shippingFee;
        }
    
        $finalTotal = $totalAmount + $shippingFee - $discount;
    
        session([
            'code' => $voucher->code,
            'id' => $voucher->id,
            'type' => $voucher->type,
            'value' => $voucher->value,
            'discount' => $discount,
        ]);
        return response()->json([
            'success' => true,
            'discount' => $discount,
            'finalTotal' => $finalTotal,
            'voucher_id' => $voucher->id,
            'message' => 'Áp dụng mã thành công!',
        ]);
    }
    
    

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);
        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($lat1) * cos($lat2) * pow(sin($lonDelta / 2), 2)));

        return $earthRadius * $angle;
    }



    
    public function processCheckout(Request $request)
    {

        $selectedItems = $request->input('selected_items');

        if (!$selectedItems || !is_array($selectedItems)) {
            return back()->with('error', 'Vui lòng chọn sản phẩm để thanh toán.');
        }

        $user = auth()->user();
        $cart = $user->cart;
        $customer = $user->customer;

        $groupedItems = [];
        $restaurantShippingFees = [];
        $restaurantDistances = [];
        $restaurantTotalAmounts = [];
        $restaurantTotalSums = [];
        $restaurantNames = [];

        foreach ($selectedItems as $data) {
            if (empty($data['selected']) || !isset($data['product_id'], $data['product_type'])) continue;

            $query = $cart->items()
                ->where('product_id', $data['product_id'])
                ->where('product_type', $data['product_type']);

            if ($data['product_type'] === 'beverage') {
                $query->where('size', $data['size'] ?? null);
            }

            $item = $query->first();
            if (!$item) continue;

            $product = $item->product_type === 'food'
                ? Food::find($item->product_id)
                : Beverage::find($item->product_id);

            if (!$product || !$product->restaurant_id) continue;

            $productName = $product->name . ($item->product_type === 'beverage' ? ' (Size ' . $item->size . ')' : '');
            $image = $product->image;
            $restaurantId = $product->restaurant_id;

            $groupedItems[$restaurantId][] = [
                'product_id' => $item->product_id,
                'product_type' => $item->product_type,
                'size' => $item->size,
                'name' => $productName,
                'image' => $image,
                'quantity' => $item->quantity,
                'price' => $item->unit_price,
                'total' => $item->unit_price * $item->quantity,
            ];

            $restaurantNames[$restaurantId] = $product->restaurant->name ?? 'Không rõ';

            if (!isset($restaurantShippingFees[$restaurantId])) {
                $restaurant = $product->restaurant;

                if ($restaurant && $restaurant->latitude && $restaurant->longitude && $customer->latitude && $customer->longitude) {
                    $distance = $this->haversineDistance(
                        $restaurant->latitude,
                        $restaurant->longitude,
                        $customer->latitude,
                        $customer->longitude
                    );
                    $restaurantDistances[$restaurantId] = $distance;

                    if ($distance < 10) $shippingFee = 15000;
                    elseif ($distance < 20) $shippingFee = 25000;
                    elseif ($distance <= 30) $shippingFee = 35000;
                    else $shippingFee = 50000;

                    $restaurantShippingFees[$restaurantId] = $shippingFee;
                } else {
                    $restaurantDistances[$restaurantId] = null;
                    $restaurantShippingFees[$restaurantId] = 0;
                }
            }
        }

        if (empty($groupedItems)) {
            return back()->with('error', 'Không có sản phẩm hợp lệ để thanh toán.');
        }

        foreach ($groupedItems as $restaurantId => $items) {
            $totalItems = collect($items)->sum('total');
            $shipping = $restaurantShippingFees[$restaurantId] ?? 0;

            $restaurantTotalAmounts[$restaurantId] = $totalItems;
            $restaurantTotalSums[$restaurantId] = $totalItems + $shipping;
        }

        $totalAmount = array_sum($restaurantTotalAmounts);
        $totalShippingFee = array_sum($restaurantShippingFees);
        $finalTotal = array_sum($restaurantTotalSums);

        // Dùng dữ liệu voucher từ session (đã set trong applyVoucher)
        $voucherData = session()->only(['id', 'code', 'type', 'value', 'discount']);
        $voucher = null;
        $discount = 0;

        if (!empty($voucherData['id']) && !empty($voucherData['discount'])) {
            $voucher = (object) $voucherData; // Tạo object giả để hiển thị trong view
            $discount = $voucherData['discount'];
            $finalTotal -= $discount;
        } else {
            session()->forget(['id', 'code', 'type', 'value', 'discount']);
        }

        session([
            'checkout_data' => [
                'groupedItems' => $groupedItems,
                'restaurant_id' => $restaurantId,
                'restaurantShippingFees' => $restaurantShippingFees,
                'restaurantTotalAmounts' => $restaurantTotalAmounts,
                'restaurantTotalSums' => $restaurantTotalSums,
                'totalAmount' => $totalAmount,
                'totalShippingFee' => $totalShippingFee,
                'finalTotal' => $finalTotal,
                'receiver_name' => $customer->name,
                'receiver_phone' => $customer->phone,
                'receiver_address' => $customer->address,
                'voucher_id' => $voucherData['id'] ?? null,
                'voucher_code' => $voucherData['code'] ?? null,
                'voucher_discount' => $discount,
            ]
        ]);

        return view('web.checkout', compact(
            'groupedItems', 'restaurantDistances', 'restaurantShippingFees', 'restaurantTotalAmounts',
            'restaurantTotalSums', 'restaurantNames', 'totalAmount', 'totalShippingFee',
            'finalTotal', 'customer'
        ));
    }



    public function removeItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'product_type' => 'required|in:food,beverage',
            'size' => 'nullable|string',
        ]);

        $user = auth()->user();
        if (!$user || !$user->cart) {
            return back()->with('error', 'Không thể thực hiện thao tác.');
        }

        $query = $user->cart->items()
            ->where('product_id', $request->product_id)
            ->where('product_type', $request->product_type);

        if ($request->product_type === 'beverage') {
            $query->where('size', $request->size);
        }

        $query->delete();

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }


    public function clear()
    {
        $user = auth()->user();
        if ($user && $user->cart) {
            $user->cart->items()->delete();
        }
        return back()->with('success', 'Đã xóa giỏ hàng');
    }
}