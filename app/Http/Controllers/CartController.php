<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Food;
use App\Models\Beverage;
use App\Models\PendingPayment;
use Illuminate\Support\Str;

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

        // $cart = $user->cart()->firstOrCreate([]);
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

    //  GET /cart/checkout (hiển thị form thanh toán)
    public function showCheckout()
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
    
                    // Nếu cùng tỉnh thì phí cố định là 30.000
                    if ($restaurant->province === $customer->province) {
                        $shippingFee = 30000;
                    } else {
                        $shippingFee = min(100000, max(15000, round($distance * 1000)));
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



    // ✅ POST /cart/checkout (submit đơn hàng)
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
        $restaurantTotalAmounts = []; // Tổng tiền sản phẩm từng nhà hàng
        $restaurantTotalSums = [];    // Tổng cộng từng nhà hàng
        $restaurantNames = [];

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

            if (!$item) continue;

            if ($item->product_type === 'food') {
                $product = Food::find($item->product_id);
                $productName = $product?->name ?? 'Không rõ';
                $image = $product?->image;
                $restaurantId = $product?->restaurant_id;
            } else {
                $product = Beverage::find($item->product_id);
                $productName = $product ? $product->name . ' (Size ' . $item->size . ')' : 'Không rõ';
                $image = $product?->image;
                $restaurantId = $product?->restaurant_id;
            }

            if (!$product || !$restaurantId) continue;

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

                if (
                    $restaurant && $customer &&
                    $restaurant->latitude && $restaurant->longitude &&
                    $customer->latitude && $customer->longitude
                ) {
                    $distance = $this->haversineDistance(
                        $restaurant->latitude,
                        $restaurant->longitude,
                        $customer->latitude,
                        $customer->longitude
                    );

                    $restaurantDistances[$restaurantId] = $distance;

                    $shippingFee = (
                        abs($restaurant->latitude - $customer->latitude) < 0.01 &&
                        abs($restaurant->longitude - $customer->longitude) < 0.01
                    ) ? 30000 : min(100000, max(15000, round($distance * 1000)));

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

        // Tính tổng tiền sản phẩm và tổng cộng của từng nhà hàng
        foreach ($groupedItems as $restaurantId => $items) {
            $totalItems = collect($items)->sum('total');
            $shipping = $restaurantShippingFees[$restaurantId] ?? 0;

            $restaurantTotalAmounts[$restaurantId] = $totalItems;
            $restaurantTotalSums[$restaurantId] = $totalItems + $shipping;
        }

        // Tổng toàn bộ đơn hàng (gồm tất cả nhà hàng)
        $totalAmount = array_sum($restaurantTotalAmounts);
        $totalShippingFee = array_sum($restaurantShippingFees);
        $finalTotal = array_sum($restaurantTotalSums); // Tổng cộng toàn bộ (sản phẩm + ship)
        session([
            'checkout_data' => [
                'groupedItems' => $groupedItems,
                'restaurantShippingFees' => $restaurantShippingFees,
                'restaurantTotalAmounts' => $restaurantTotalAmounts,
                'restaurantTotalSums' => $restaurantTotalSums,
                'totalAmount' => $totalAmount,
                'totalShippingFee' => $totalShippingFee,
                'finalTotal' => $finalTotal,
            ]
        ]);
        // test
        // $vnpTxnRef = strtoupper(Str::random(12)); // Mã giao dịch duy nhất

        // PendingPayment::create([
        //     'user_id' => $user->id,
        //     'vnp_txn_ref' => $vnpTxnRef,
        //     'items' => json_encode($groupedItems),
        //     'total_amount' => $totalAmount,
        //     'shipping_fee' => $totalShippingFee,
        //     'final_amount' => $finalTotal,
        //     'status' => 'pending',
        // ]);

        return view('web.checkout', [
            'groupedItems' => $groupedItems,
            'restaurantDistances' => $restaurantDistances,
            'restaurantShippingFees' => $restaurantShippingFees,
            'restaurantTotalAmounts' => $restaurantTotalAmounts,
            'restaurantTotalSums' => $restaurantTotalSums,
            'restaurantNames' => $restaurantNames,
            'totalAmount' => $totalAmount,
            'totalShippingFee' => $totalShippingFee,
            'finalTotal' => $finalTotal,
            'customer' => $customer,
        ]);
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