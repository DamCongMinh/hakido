<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

use App\Models\Food;
use App\Models\Beverage;
use App\Models\PendingPayment;
class CheckoutController extends Controller
{

    //  hiển thị form thanh toán
    public function showCheckout(Request $request)
    {
        $checkoutData = session('checkout_data');

        if (!$checkoutData) {
            return redirect()->route('cart.show')->with('error', 'Không có dữ liệu để hiển thị thanh toán.');
        }

        return view('web.checkout', $checkoutData);
    }

    public function processCheckout(Request $request)
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
    
        $voucherData = session('voucher_data');
        $discount = 0;
        $voucher = null;
    
        if ($voucherData) {
            $voucher = \App\Models\Voucher::find($voucherData['id']);
    
            if ($voucher && $voucher->is_active && (is_null($voucher->end_date) || $voucher->end_date > now())) {
                if ($voucher->type === 'percent') {
                    $discount = ($voucher->value / 100) * $totalAmount;
                } elseif ($voucher->type === 'free_shipping') {
                    $discount = $totalShippingFee;
                }
    
                $finalTotal -= $discount;
            } else {
                
                session()->forget('voucher_data');
            }
        }

        session(['checkout_data' => [
            'groupedItems' => $groupedItems,
            'restaurantShippingFees' => $restaurantShippingFees,
            'restaurantDistances' => $restaurantDistances,
            'restaurantNames' => $restaurantNames,
            'restaurantTotalAmounts' => $restaurantTotalAmounts,
            'restaurantTotalSums' => $restaurantTotalSums,
            'finalTotal' => $finalTotal,
            'discount' => $discount,
            'voucher' => $voucher,
            'totalShippingFee' => $totalShippingFee,
            'totalAmount' => $totalAmount,
            'user' => $user,
            'customer' => $customer,
            'isGuest' => false,
        ]]);

        return redirect()->route('checkout.show');
    }

    // public function preview(Request $request)
    // {
    //     $receiverLat = $request->input('latitude'); // Bạn cần xử lý lấy lat/lng từ địa chỉ mới (có thể dùng Google Maps API ở client để fill vào).
    //     $receiverLng = $request->input('longitude');

    //     $groupedItems = json_decode($request->input('items'), true);
    //     $restaurantShippingFees = [];
    //     $restaurantDistances = [];
    //     $restaurantTotalAmounts = [];
    //     $restaurantTotalSums = [];
    //     $restaurantNames = [];

    //     foreach ($groupedItems as $restaurantId => $items) {
    //         $restaurant = Restaurant::find($restaurantId);
    //         $restaurantNames[$restaurantId] = $restaurant->name ?? 'Không rõ';

    //         $totalAmount = 0;

    //         foreach ($items as $item) {
    //             $totalAmount += $item['price'] * $item['quantity'];
    //         }

    //         $restaurantTotalAmounts[$restaurantId] = $totalAmount;

    //         // Tính khoảng cách
    //         if (
    //             $restaurant && $restaurant->latitude && $restaurant->longitude &&
    //             $receiverLat && $receiverLng
    //         ) {
    //             $distance = $this->haversineDistance(
    //                 $restaurant->latitude,
    //                 $restaurant->longitude,
    //                 $receiverLat,
    //                 $receiverLng
    //             );

    //             $restaurantDistances[$restaurantId] = $distance;

    //             if ($distance < 10) {
    //                 $shippingFee = 15000;
    //             } elseif ($distance < 20) {
    //                 $shippingFee = 25000;
    //             } elseif ($distance <= 30) {
    //                 $shippingFee = 35000;
    //             } else {
    //                 $shippingFee = 50000;
    //             }

    //             $restaurantShippingFees[$restaurantId] = $shippingFee;
    //         } else {
    //             $restaurantShippingFees[$restaurantId] = 0;
    //             $restaurantDistances[$restaurantId] = null;
    //         }

    //         $restaurantTotalSums[$restaurantId] = $restaurantTotalAmounts[$restaurantId] + $restaurantShippingFees[$restaurantId];
    //     }

    //     $finalTotal = array_sum($restaurantTotalSums);

    //     return view('checkout', [
    //         'groupedItems' => $groupedItems,
    //         'restaurantShippingFees' => $restaurantShippingFees,
    //         'restaurantDistances' => $restaurantDistances,
    //         'restaurantTotalAmounts' => $restaurantTotalAmounts,
    //         'restaurantTotalSums' => $restaurantTotalSums,
    //         'restaurantNames' => $restaurantNames,
    //         'finalTotal' => $finalTotal,
    //         'isGuest' => auth()->guest(),
    //         'user' => auth()->user(),
    //         'customer' => null,
    //     ]);
    // }


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




}
