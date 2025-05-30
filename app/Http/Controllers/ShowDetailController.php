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

            // Lấy đánh giá món ăn
            $reviews = \App\Models\FoodReview::with('customer')
                        ->where('food_id', $id)
                        ->latest()
                        ->get();

        } elseif ($type === 'beverage') {
            $product = Beverage::with([
                'restaurant' => function ($query) {
                    $query->withCount(['foods', 'beverages']);
                },
                'beverageSizes'
            ])->findOrFail($id);

            // Lấy đánh giá thức uống
            $reviews = \App\Models\BeverageReview::with('customer')
                        ->where('beverage_id', $id)
                        ->latest()
                        ->get();

        } else {
            abort(404);
        }

        $filters = [
            'all' => $reviews->count(),
            '5'   => $reviews->where('rating', 5)->count(),
            '4'   => $reviews->where('rating', 4)->count(),
            '3'   => $reviews->where('rating', 3)->count(),
            '2'   => $reviews->where('rating', 2)->count(),
            '1'   => $reviews->where('rating', 1)->count(),
            'with_media'   => $reviews->filter(fn($r) => $r->media && count($r->media))->count(),
            'with_comment' => $reviews->filter(fn($r) => trim($r->comment))->count(),
        ];

        $productAvgRating = $reviews->count()
        ? round($reviews->avg('rating'), 1)
        : null;

        $totalProducts = $product->restaurant->foods->count() + $product->restaurant->beverages->count();
        $restaurant = $product->restaurant;

        $restaurantStats = [
            'rating_count' => $restaurant->rating_count ?? 0,
            'reply_rate' => $restaurant->reply_rate ?? 'Đang cập nhật',
            'reply_time' => $restaurant->reply_time ?? 'Đang cập nhật',
            'joined' => $restaurant->created_at->diffForHumans(),
            'follower_count' => $restaurant->follower_count ?? 0,
        ];

        $restaurantId = $product->restaurant->id;

        // Lấy danh sách order_id thuộc về nhà hàng này
        $orderIds = \App\Models\Order::where('restaurant_id', $restaurantId)->pluck('id');

        // Đếm số đánh giá từ cả hai bảng
        $foodReviewCount = \App\Models\FoodReview::whereIn('order_id', $orderIds)->count();
        $beverageReviewCount = \App\Models\BeverageReview::whereIn('order_id', $orderIds)->count();

        $totalRestaurantReviews = $foodReviewCount + $beverageReviewCount;

        $foodImages = \App\Models\Food::where('restaurant_id', $restaurantId)
                ->whereNotNull('image')
                ->pluck('image');

        $beverageImages = \App\Models\Beverage::where('restaurant_id', $restaurantId)
                            ->whereNotNull('image')
                            ->pluck('image');

        $productImages = $foodImages->merge($beverageImages);
        // tất cả thông tin sản phẩm
        $foods = \App\Models\Food::where('restaurant_id', $restaurantId)->get();
        $beverages = \App\Models\Beverage::with('beverageSizes')->where('restaurant_id', $restaurantId)->get();

        $allProducts = $foods->map(function($item) {
            return [
                'id' => $item->id,
                'type' => 'food',
                'name' => $item->name,
                'description' => $item->description,
                'image' => asset('storage/' . $item->image),
                'old_price' => $item->old_price,
                'discount_percent' => $item->discount_percent,
                'price' => $item->old_price * (100 - $item->discount_percent) / 100,
                'quantity' => $item->quantity,
            ];
        })->merge(
            $beverages->map(function($item) {
                return [
                    'id' => $item->id,
                    'type' => 'beverage',
                    'name' => $item->name,
                    'description' => $item->description,
                    'image' => asset('storage/' . $item->image),
                    'sizes' => $item->beverageSizes->map(function ($s) {
                        return [
                            'size' => $s->size,
                            'old_price' => $s->old_price,
                            'discount_percent' => $s->discount_percent,
                            'quantity' => $s->quantity,
                            'price' => $s->old_price * (100 - $s->discount_percent) / 100,
                        ];
                    }),
                ];
            })
        );

        $user = auth()->user();
        if ($user && $user->role === 'restaurant' && $user->restaurant) {
            if (
                !$user->restaurant->last_active_at ||
                $user->restaurant->last_active_at->lt(now()->subMinute())
            ) {
                $user->restaurant->update(['last_active_at' => now()]);
            }
        }

        // Truyền thêm $reviews xuống view
        return view('web.detail_product', compact('product', 'type', 'reviews',
        'filters', 'productAvgRating', 'restaurantStats', 'totalProducts',
        'totalRestaurantReviews', 'productImages', 'allProducts'));
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
            return back()->with('success', 'Đặt hàng thành công!');
        }

        return back()->with('error', 'Dữ liệu không hợp lệ.');
    }


}

