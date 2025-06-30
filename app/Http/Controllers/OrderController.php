<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Food;
use App\Models\Beverage;
use App\Models\BeverageSize;
use App\Models\Customer;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'required|string|max:20',
            'receiver_address' => 'required|string',
            'payment_method' => 'required|in:cod,bank,vnpay',
            'items' => 'required|string',
            'shipping_fees' => 'required|string',
            'distances' => 'required|string',
            'restaurantTotalAmounts' => 'required|string',
            'restaurantTotalSums' => 'required|string',
            'voucher_code' => 'nullable|string',
            'voucher_discount' => 'nullable|numeric',
            'voucher_id' => 'nullable|integer',
        ]);

        $user = auth()->user();
        $customer = $user->customer;

        if (!$customer) {
            throw new \Exception('Không tìm thấy thông tin khách hàng.');
        }

        // Parse dữ liệu JSON từ form
        $groupedItems = json_decode($request->input('items'), true);
        $shippingFees = json_decode($request->input('shipping_fees'), true);
        $distances = json_decode($request->input('distances'), true);
        $restaurantTotalAmounts = json_decode($request->input('restaurantTotalAmounts'), true); // Chỉ tiền món
        $restaurantTotalSums = json_decode($request->input('restaurantTotalSums'), true);       // Tổng cộng (gồm phí ship nếu có)

        // Thông tin voucher
        $voucherId = $request->input('voucher_id');
        $voucherCode = $request->input('voucher_code');
        $voucherDiscount = floatval($request->input('voucher_discount') ?? 0);
        $discountPerRestaurant = [];

        // Tổng tiền hàng của tất cả nhà hàng (dùng để chia tỷ lệ %)
        $totalAmount = array_sum($restaurantTotalAmounts);

        // Tìm voucher dựa trên ID hoặc mã
        $voucher = null;
        if ($voucherId) {
            $voucher = \App\Models\Voucher::find($voucherId);
        } elseif ($voucherCode) {
            $voucher = \App\Models\Voucher::where('code', $voucherCode)->first();
        }

        // Tính giảm giá cho từng nhà hàng nếu có voucher
        if ($voucher) {
            $voucherId = $voucher->id;

            if ($voucher->type === 'percent' && $totalAmount > 0 && $voucherDiscount > 0) {
                foreach ($restaurantTotalAmounts as $restaurantId => $amount) {
                    $discountPerRestaurant[$restaurantId] = round(($amount / $totalAmount) * $voucherDiscount, 0);
                }
            } elseif ($voucher->type === 'free_shipping') {
                foreach ($shippingFees as $restaurantId => $fee) {
                    $discountPerRestaurant[$restaurantId] = $fee;
                }
            }
        }

        DB::beginTransaction();

        try {
            $createdOrders = [];

            foreach ($groupedItems as $restaurantId => $items) {
                if (!isset($distances[$restaurantId])) {
                    throw new \Exception("Không có dữ liệu khoảng cách cho nhà hàng $restaurantId.");
                }

                $distance = $distances[$restaurantId];
                if ($distance > 20) {
                    throw new \Exception("Khoảng cách đến nhà hàng $restaurantId vượt quá giới hạn ($distance km).");
                }

                // Tính total = tiền món + ship - giảm giá
                $restaurantTotal = ($restaurantTotalSums[$restaurantId] ?? 0);
                $discount = $discountPerRestaurant[$restaurantId] ?? 0;
                $finalTotal = $restaurantTotal - $discount;

                $order = Order::create([
                    'customer_id' => $user->id,
                    'restaurant_id' => $restaurantId,
                    'receiver_name' => $request->receiver_name,
                    'receiver_phone' => $request->receiver_phone,
                    'receiver_address' => $request->receiver_address,
                    'shipping_fee' => $shippingFees[$restaurantId] ?? 0,
                    'total' => $finalTotal,
                    'voucher_id' => $voucherId,
                    'discount' => $discount,
                    'status' => 'pending',
                    'payment_method' => $request->payment_method,
                ]);

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'order_img' => $item['image'],
                        'product_id' => $item['product_id'],
                        'product_type' => $item['product_type'],
                        'product_name' => $item['name'],
                        'price' => $item['price'],
                        'total_price' => $item['price'] * $item['quantity'],
                        'quantity' => $item['quantity'],
                        'size' => $item['size'] ?? null,
                    ]);
                }

                $createdOrders[] = $order;
            }

            // Xoá các item khỏi giỏ hàng
            $cart = $user->cart;
            if ($cart) {
                foreach ($groupedItems as $items) {
                    foreach ($items as $item) {
                        $query = $cart->items()
                            ->where('product_id', $item['product_id'])
                            ->where('product_type', $item['product_type']);

                        if (!empty($item['size'])) {
                            $query->where('size', $item['size']);
                        } else {
                            $query->whereNull('size');
                        }

                        $query->delete();
                    }
                }
            }

            DB::commit();

            // Xoá session voucher sau khi đặt hàng
            session()->forget('voucher_code');
            session()->forget('voucher_data');

            return view('web.order_success', ['orders' => $createdOrders]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã xảy ra lỗi khi xử lý đơn hàng: ' . $e->getMessage());
        }
    }



    public function success(Request $request)
    {
        $ids = explode(',', $request->get('ids'));

        $orders = Order::with('orderItems', 'restaurant')
            ->whereIn('id', $ids)
            ->where('customer_id', auth()->user()->customer->id)
            ->get();

        return view('web.order_success', ['orders' => $orders]);
    }


    public function orderedItems(Request $request)
    {
        $user = auth()->user();
        $userId = $user->id;
        $customerId = $user->customer->id ?? null;
        $statusFilter = $request->input('status');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $ordersQuery = \App\Models\Order::with(['orderItems', 'restaurant'])
            ->where(function ($query) use ($userId, $customerId) {
                $query->where('customer_id', $userId);
                if ($customerId) {
                    $query->orWhere('customer_id', $customerId);
                }
            });

        if ($statusFilter) {
            $ordersQuery->where('status', $statusFilter);
        }

        if ($fromDate) {
            $ordersQuery->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $ordersQuery->whereDate('created_at', '<=', $toDate);
        }

        $orders = $ordersQuery->latest()->get();

        $foodIds = [];
        $beverageIds = [];

        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                if ($item->product_type === 'food') {
                    $foodIds[] = $item->product_id;
                } elseif ($item->product_type === 'beverage') {
                    $beverageIds[] = $item->product_id;
                }
            }
        }

        $foods = \App\Models\Food::whereIn('id', $foodIds)->get()->keyBy('id');
        $beverages = \App\Models\Beverage::whereIn('id', $beverageIds)->get()->keyBy('id');

        return view('web.ordered_items', compact('orders', 'foods', 'beverages', 'statusFilter', 'fromDate', 'toDate'));
    }

    public function cancel(Order $order)
    {
        $user = auth()->user();

        if ($order->customer_id !== $user->id) {
            return back()->with('error', 'Bạn không có quyền hủy đơn hàng này.');
        }

        if ($order->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể hủy đơn hàng đang chờ xác nhận.');
        }

        $order->status = 'canceled';
        $order->save();

        return back()->with('success', 'Đơn hàng đã được hủy thành công.');
    }

}