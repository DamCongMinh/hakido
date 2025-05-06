<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Food;
use App\Models\Beverage;
use App\Models\BeverageSize;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'required|string|max:20',
            'receiver_address' => 'required|string',
            'payment_method' => 'required|in:cod,bank,vnpay',
            'items' => 'required|string',
            'shipping_fees' => 'required|string',
            'distances' => 'required|string',
            'restaurantTotalAmounts' => 'required|string',
        ]);

        $user = auth()->user();
        $customer = $user->customer;
        if (!$customer) {
            throw new \Exception('Không tìm thấy thông tin khách hàng.');
        }
        

        // Decode các dữ liệu JSON
        $groupedItems = json_decode($request->input('items'), true);
        $shippingFees = json_decode($request->input('shipping_fees'), true);
        $distances = json_decode($request->input('distances'), true);
        $restaurantTotalAmounts = json_decode($request->input('restaurantTotalAmounts'), true);
        $restaurantTotalSums = json_decode($request->input('restaurantTotalSums'), true);
        $orders = []; 
        $createdOrders = [];
        // dd($groupedItems);


        DB::beginTransaction();

        try {
            foreach ($groupedItems as $restaurantId => $items) {
                $totalAmount = 0;

                // Tạo đơn hàng
                $order = Order::create([
                    'customer_id' => $customer->id,
                    'restaurant_id' => $restaurantId,
                    'receiver_name' => $request->receiver_name,
                    'receiver_phone' => $request->receiver_phone,
                    'receiver_address' => $request->receiver_address,
                    // 'distance' => $distances[$restaurantId] ?? 0,
                    'shipping_fee' => $shippingFees[$restaurantId] ?? 0,
                    'total' => $restaurantTotalSums[$restaurantId] ?? 0,
                    'status' => 'pending',
                    'payment_method' => $request->payment_method,
                ]);

                // Tạo chi tiết đơn hàng
                foreach ($items as $item) {
                    $itemTotal = $item['price'] * $item['quantity'];
                    $totalAmount += $itemTotal;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'product_type' => $item['product_type'],
                        'product_name' => $item['name'],
                        'price' => $item['price'], 
                        'total_price' => $item['price'] * $item['quantity'],
                        'quantity' => $item['quantity'],
                        'size' => $item['size'] ?? null,
                    ]);
                    
                }
                
                // ✅ Load các quan hệ để dùng ở view
                $order->load('orderItems', 'restaurantProfile');
                

                // ✅ Thêm vào danh sách để hiển thị
                $createdOrders[] = $order;
                
            }
            // ✅ Xoá giỏ hàng sau khi tạo đơn xong
            $cart = $user->cart;
                if ($cart) {
                    foreach ($groupedItems as $restaurantId => $items) {
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

    public function orderedItems()
    {
        $customer = auth()->user()->customer;

        if (!$customer) {
            return redirect()->route('home')->with('error', 'Không tìm thấy khách hàng.');
        }

        // Lấy danh sách đơn hàng kèm items và nhà hàng
        $orders = \App\Models\Order::with(['orderItems', 'restaurantProfile'])
            ->where('customer_id', $customer->id)
            ->latest()
            ->get();

        return view('web.ordered_items', compact('orders'));
    }


    public function cancel(Order $order)
    {
        $customer = auth()->user()->customer;

        // Kiểm tra quyền hủy đơn (đảm bảo đơn thuộc về khách hàng hiện tại)
        if ($order->customer_id !== $customer->id) {
            return back()->with('error', 'Bạn không có quyền hủy đơn hàng này.');
        }

        // Chỉ cho phép hủy nếu đang ở trạng thái pending
        if ($order->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể hủy đơn hàng đang chờ xác nhận.');
        }

        // Cập nhật trạng thái
        $order->status = 'canceled';
        $order->save();

        return back()->with('success', 'Đơn hàng đã được hủy thành công.');
    }



}
