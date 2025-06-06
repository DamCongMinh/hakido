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
        $query = [
            ['is_active', '=', 1],
            ['is_approved', '=', 1]
        ];
    
        if ($type === 'food') {
            $product = Food::with(['restaurant' => function ($query) {
                $query->withCount(['foods', 'beverages']);
            }])->where($query)->findOrFail($id);
    
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
            ])->where($query)->findOrFail($id);
    
            $reviews = \App\Models\BeverageReview::with('customer')
                        ->where('beverage_id', $id)
                        ->latest()
                        ->get();
        } else {
            abort(404);
        }
        
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
        dd($request->all());
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

    public function checkout(Request $request)
    {
        
        $validated = $request->validate([
            'type' => 'required|in:food,beverage',
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string'
        ]);

        if ($validated['type'] === 'food') {
            $product = Food::findOrFail($validated['product_id']);
        } else {
            $product = Beverage::with('beverageSizes')->findOrFail($validated['product_id']);
            $selectedSize = $product->beverageSizes->firstWhere('size', $validated['size']);
            if (!$selectedSize) {
                return back()->with('error', 'Không tìm thấy size đã chọn.');
            }
        }
        

        // Chuyển dữ liệu tới view checkout
        return view('web.checkout', [
            'product' => $product,
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'size' => $validated['size'] ?? null
        ]);
    }

    public function processCheckout(Request $request)
    {
        // dd($request->all());
        $items = $request->input('selected_items', []);
        $groupedItems = [];
    
        $customer = auth()->user()?->customer;
        if (!$customer) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để tiếp tục');
        }
    
        $restaurantShippingFees = [];
        $restaurantNames = [];
        $restaurantDistances = [];
        $restaurantTotalAmounts = [];
        $restaurantTotalSums = [];
    
        foreach ($items as $item) {
            $productId = $item['product_id'];
            $type = $item['product_type'];
            $quantity = $item['quantity'] ?? 1;
            $size = $item['size'] ?? null;
    
            if ($type === 'food') {
                $product = Food::findOrFail($productId);
                $unitPrice = $product->old_price * (100 - $product->discount_percent) / 100;
                $restaurant = $product->restaurant;
            } else {
                $product = Beverage::with('beverageSizes')->findOrFail($productId);
                $sizeObj = $product->beverageSizes->firstWhere('size', $size);
                if (!$sizeObj) return back()->with('error', 'Không tìm thấy size');
                $unitPrice = $sizeObj->old_price * (100 - $sizeObj->discount_percent) / 100;
                $restaurant = $product->restaurant;
            }
    
            $restaurantId = $restaurant->id ?? null;
            if (!$restaurantId) continue;
    
            $restaurantNames[$restaurantId] = $restaurant->name;
    
            $groupedItems[$restaurantId][] = [
                'product_id' => $productId,
                'product_type' => $type,
                'size' => $size,
                'name' => $product->name . ($size ? " (Size {$size})" : ''),
                'image' => $product->image,
                'price' => $unitPrice,
                'quantity' => $quantity,
                'total' => $unitPrice * $quantity,
            ];
    
            if (!isset($restaurantShippingFees[$restaurantId])) {
                if ($restaurant->latitude && $restaurant->longitude &&
                    $customer->latitude && $customer->longitude) {
    
                    $distance = $this->haversineDistance(
                        $restaurant->latitude, $restaurant->longitude,
                        $customer->latitude, $customer->longitude
                    );
    
                    $restaurantDistances[$restaurantId] = $distance;
                    $shippingFee = $restaurant->province === $customer->province ? 30000 : min(100000, max(15000, round($distance * 1000)));
                    $restaurantShippingFees[$restaurantId] = $shippingFee;
                } else {
                    $restaurantShippingFees[$restaurantId] = 0;
                }
            }
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

