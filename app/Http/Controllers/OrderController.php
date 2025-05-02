<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Food;
use App\Models\Beverage;
use App\Models\BeverageSize;

use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $items = json_decode($request->input('items'), true);

        DB::beginTransaction();

        try {
            if (!$items || !is_array($items) || count($items) === 0) {
                return response()->json(['error' => 'Giỏ hàng trống hoặc dữ liệu không hợp lệ'], 400);
            }

            // Lấy tất cả restaurant_id từ các sản phẩm
            $restaurantIds = [];

            foreach ($items as $item) {
                $id = null;

                if ($item['product_type'] === 'food') {
                    $id = DB::table('foods')->where('id', $item['product_id'])->value('restaurant_id');
                } elseif ($item['product_type'] === 'beverage') {
                    $id = DB::table('beverages')->where('id', $item['product_id'])->value('restaurant_id');
                }

                if (!$id) {
                    throw new \Exception('Không tìm thấy restaurant_id cho sản phẩm');
                }

                $restaurantIds[] = $id;
            }

            $uniqueRestaurantIds = array_unique($restaurantIds);

            if (count($uniqueRestaurantIds) > 1) {
                return response()->json(['error' => 'Giỏ hàng chứa sản phẩm từ nhiều nhà hàng. Vui lòng đặt hàng riêng cho từng nhà hàng.'], 400);
            }

            $restaurantId = $uniqueRestaurantIds[0];

            // Tạo đơn hàng
            $order = Order::create([
                'customer_id' => auth()->id(),
                'restaurant_id' => $restaurantId,
                'receiver_name' => $request->input('receiver_name'),
                'receiver_phone' => $request->input('receiver_phone'),
                'receiver_address' => $request->input('receiver_address'),
                'payment_method' => $request->input('payment_method'),
                'note' => $request->input('note'),
                'status' => 'pending',
            ]);

            foreach ($items as $item) {
                $price = 0;
                $productName = '';
                $sizeId = null;

                if ($item['product_type'] === 'beverage') {
                    $size = DB::table('beverage_sizes')
                        ->select('old_price', 'discount_percent', 'id')
                        ->where('beverage_id', $item['product_id'])
                        ->where('size', $item['size'])
                        ->first();

                    if (!$size || !isset($size->old_price)) {
                        throw new \Exception('Không tìm thấy thông tin giá cho size đồ uống');
                    }

                    $discount = $size->discount_percent ?? 0;
                    $price = $size->old_price * (1 - ($discount / 100));
                    $sizeId = $size->id;

                    $product = DB::table('beverages')->where('id', $item['product_id'])->first();
                    $productName = ($product->name ?? 'Đồ uống') . ' (Size ' . $item['size'] . ')';

                } elseif ($item['product_type'] === 'food') {
                    $food = DB::table('foods')
                        ->select('old_price', 'discount_percent', 'name')
                        ->where('id', $item['product_id'])
                        ->first();

                    if (!$food || !isset($food->old_price)) {
                        throw new \Exception('Dữ liệu món ăn không hợp lệ');
                    }

                    $discount = $food->discount_percent ?? 0;
                    $price = $food->old_price * (1 - ($discount / 100));
                    $productName = $food->name ?? 'Món ăn';
                }

                if ($price === null) {
                    throw new \Exception('Không tìm thấy giá sản phẩm');
                }

                $quantity = $item['quantity'] ?? 1;
                $totalPrice = $price * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_type' => $item['product_type'],
                    'product_name' => $productName,
                    'size_id' => $sizeId,
                    'size' => $item['size'] ?? null,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total_price' => $totalPrice,
                    'options' => $item['options'] ?? null,
                ]);
            }

            $total = OrderItem::where('order_id', $order->id)->sum('total_price');
            $order->update(['total' => $total]);

            DB::commit();

            return redirect()->route('order.success', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi lưu đơn hàng: ' . $e->getMessage());
            return response()->json(['error' => 'Đã có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    public function success($id)
    {
        $order = Order::with('orderItems')->findOrFail($id);
        return view('web.order_success', compact('order'));
    }
}
