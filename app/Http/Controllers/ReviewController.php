<?php

use App\Models\FoodReview;
use App\Models\BeverageReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            // Chỉ cần 1 trong 2: food_id hoặc beverage_id
            'food_id' => 'nullable|exists:foods,id',
            'beverage_id' => 'nullable|exists:beverages,id',
        ]);

        $customerId = Auth::guard('customer')->id();

        if (!$customerId) {
            return redirect()->back()->with('error', 'Bạn phải đăng nhập để đánh giá.');
        }

        if ($request->filled('food_id')) {
            FoodReview::create([
                'customer_id' => $customerId,
                'food_id' => $request->food_id,
                'order_id' => $request->order_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
        } elseif ($request->filled('beverage_id')) {
            BeverageReview::create([
                'customer_id' => $customerId,
                'beverage_id' => $request->beverage_id,
                'order_id' => $request->order_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
        } else {
            return redirect()->back()->with('error', 'Phải chọn món ăn hoặc đồ uống để đánh giá.');
        }

        return redirect()->back()->with('success', 'Cảm ơn bạn đã đánh giá!');
    }
}

