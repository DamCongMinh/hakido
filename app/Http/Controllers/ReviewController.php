<?php

namespace App\Http\Controllers;

use App\Models\ShippingReview;
use App\Models\FoodReview;
use App\Models\BeverageReview;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Food;
use App\Models\Beverage;
use App\Models\BeverageSize;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function showOrderReviewForm($orderId)
    {
        $order = Order::with(['shipper'])->findOrFail($orderId);

        // Lấy danh sách orderItems và phân loại
        $orderItems = $order->orderItems;

        $foodItems = $orderItems->filter(fn($item) => $item->product_type === 'food');
        $beverageItems = $orderItems->filter(fn($item) => $item->product_type === 'beverage');

        // Gán tên và ảnh cho món ăn
        foreach ($foodItems as $item) {
            $food = \App\Models\Food::select('id', 'name', 'image')->find($item->product_id);
            $item->product_name = $food?->name ?? 'Không rõ';
            $item->image = $food?->image ?? null;
        }

        // Gán tên và ảnh cho đồ uống (ảnh từ BeverageSize)
        foreach ($beverageItems as $item) {
            // Lấy thông tin đồ uống
            $beverage = \App\Models\Beverage::select('id', 'name', 'image')->find($item->product_id);
        
            // Gán thông tin cần thiết để hiển thị trong view
            $item->product_name = $beverage?->name ?? 'Không rõ';
            $item->image = $beverage?->image ?? null;
        }        
        

        $customerId = auth()->user()->customer->id;

        // Lấy các đánh giá món ăn
        $foodReviews = FoodReview::where('order_id', $orderId)
                        ->where('customer_id', $customerId)
                        ->get()
                        ->keyBy('food_id');

        // Lấy các đánh giá đồ uống
        $beverageReviews = BeverageReview::where('order_id', $orderId)
                            ->where('customer_id', $customerId)
                            ->get()
                            ->keyBy('beverage_id');

        $shippingReview = ShippingReview::where('order_id', $orderId)
                            ->where('customer_id', $customerId)
                            ->first();

        return view('review.reviews', compact(
            'order',
            'foodItems',
            'beverageItems',
            'foodReviews',
            'beverageReviews',
            'shippingReview'
        ));
    }


    public function FoodReview(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'food_id' => 'required|exists:foods,id',
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        // Kiểm tra xem đã đánh giá món này trong đơn này chưa
        $existing = \App\Models\FoodReview::where([
            'customer_id' => $validated['customer_id'],
            'food_id' => $validated['food_id'],
            'order_id' => $validated['order_id'],
        ])->first();

        if ($existing) {
            return redirect()->back()->with('warning', 'Bạn đã đánh giá món này rồi.');
        }

        \App\Models\FoodReview::create($validated);

        return redirect()->back()->with('success', 'Cảm ơn bạn đã đánh giá món ăn!');
    }

    public function BeverageReview(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'beverage_id' => 'required|exists:beverages,id',
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $existing = \App\Models\BeverageReview::where([
            'customer_id' => $validated['customer_id'],
            'beverage_id' => $validated['beverage_id'],
            'order_id' => $validated['order_id'],
        ])->first();

        if ($existing) {
            return redirect()->back()->with('warning', 'Bạn đã đánh giá đồ uống này rồi.');
        }

        \App\Models\BeverageReview::create($validated);

        return redirect()->back()->with('success', 'Cảm ơn bạn đã đánh giá đồ uống!');
    }

    public function ShippingReview(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'customer_id' => 'required|exists:customers,id',
            'shipping_rating' => 'required|integer|min:1|max:5',
            'shipping_comment' => 'nullable|string|max:1000',
        ]);
    
        // Tìm shipper từ user_id (bạn gửi lên là user_id)
        $shipper = \App\Models\Shipper::where('user_id', $request->shipper_id)->first();
    
        if (!$shipper) {
            return back()->withErrors(['shipper_id' => 'Người giao hàng không tồn tại.']);
        }
    
        // Kiểm tra nếu người dùng đã đánh giá shipper trong đơn hàng này
        $existingReview = ShippingReview::where('order_id', $request->order_id)
                            ->where('customer_id', $request->customer_id)
                            ->first();
    
        if ($existingReview) {
            return back()->with('warning', 'Bạn đã đánh giá dịch vụ vận chuyển cho đơn hàng này rồi.');
        }
    
        ShippingReview::create([
            'order_id' => $request->order_id,
            'shipper_id' => $shipper->id, // sử dụng id thật từ bảng shippers
            'customer_id' => $request->customer_id,
            'rating' => $request->shipping_rating,
            'comment' => $request->shipping_comment,
        ]);
    
        return back()->with('shipping_success', 'Cảm ơn bạn đã đánh giá dịch vụ vận chuyển!');
    }
    

}

