<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Food;
use App\Models\Beverage;
use Illuminate\Http\Request;
use App\Models\Voucher;
use Illuminate\Support\Facades\Session;

class ShowDetailController extends Controller
{
    public function show($type, $id)
    {
        $queryConditions = [
            ['is_active', '=', 1],
            ['is_approved', '=', 1]
        ];

        if (!in_array($type, ['food', 'beverage'])) {
            abort(404);
        }

        // Lấy sản phẩm và đánh giá
        if ($type === 'food') {
            $product = Food::with(['restaurant' => function ($query) {
                $query->withCount(['foods', 'beverages']);
            }])->where($queryConditions)->findOrFail($id);

            $reviews = \App\Models\FoodReview::with('customer')
                        ->where('food_id', $id)
                        ->latest()
                        ->get();
        } else {
            $product = Beverage::with([
                'restaurant' => function ($query) {
                    $query->withCount(['foods', 'beverages']);
                },
                'beverageSizes' => function($query) {
                    $query->orderBy('old_price');
                }
            ])->where($queryConditions)->findOrFail($id);

            if ($product->beverageSizes->isNotEmpty()) {
                $product->min_price = $product->beverageSizes->min(function($size) {
                    return $size->old_price * (1 - $size->discount_percent / 100);
                });
                $product->max_price = $product->beverageSizes->max(function($size) {
                    return $size->old_price * (1 - $size->discount_percent / 100);
                });

                $product->beverageSizes->each(function($size) {
                    $size->new_price = $size->old_price * (1 - $size->discount_percent / 100);
                });
            } else {
                $product->min_price = 0;
                $product->max_price = 0;
            }

            $reviews = \App\Models\BeverageReview::with('customer')
                        ->where('beverage_id', $id)
                        ->latest()
                        ->get();
        }

        // 🎟️ Thêm voucher theo restaurant_id
        $restaurantId = $product->restaurant->id;
        $vouchers = Voucher::where('restaurant_id', $restaurantId)
            ->where('is_active', 1)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->get();

        // Bộ lọc đánh giá
        $filters = [
            'all' => $reviews->count(),
            '5' => $reviews->where('rating', 5)->count(),
            '4' => $reviews->where('rating', 4)->count(),
            '3' => $reviews->where('rating', 3)->count(),
            '2' => $reviews->where('rating', 2)->count(),
            '1' => $reviews->where('rating', 1)->count(),
            'with_media' => $reviews->filter(fn($r) => $r->media && count($r->media))->count(),
            'with_comment' => $reviews->filter(fn($r) => trim($r->comment))->count(),
        ];

        $productAvgRating = $reviews->count() ? round($reviews->avg('rating'), 1) : null;

        $totalProducts = $product->restaurant->foods_count + $product->restaurant->beverages_count;

        $restaurantStats = [
            'rating_count' => $product->restaurant->rating_count ?? 0,
            'reply_rate' => $product->restaurant->reply_rate ?? 'Đang cập nhật',
            'reply_time' => $product->restaurant->reply_time ?? 'Đang cập nhật',
            'joined' => $product->restaurant->created_at->diffForHumans(),
            'follower_count' => $product->restaurant->follower_count ?? 0,
        ];

        $orderIds = \App\Models\Order::where('restaurant_id', $restaurantId)->pluck('id');
        $totalRestaurantReviews = \App\Models\FoodReview::whereIn('order_id', $orderIds)->count()
                                + \App\Models\BeverageReview::whereIn('order_id', $orderIds)->count();

        $foodImages = \App\Models\Food::where('restaurant_id', $restaurantId)
                        ->whereNotNull('image')
                        ->pluck('image');
        $beverageImages = \App\Models\Beverage::where('restaurant_id', $restaurantId)
                            ->whereNotNull('image')
                            ->pluck('image');
        $productImages = $foodImages->merge($beverageImages);

        $foods = \App\Models\Food::where('restaurant_id', $restaurantId)->get();
        $beverages = \App\Models\Beverage::with(['beverageSizes' => function($query) {
            $query->orderBy('old_price');
        }])->where('restaurant_id', $restaurantId)->get();

        $allProducts = $foods->map(function ($item) {
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
        })->concat(
            $beverages->map(function ($item) {
                $sizes = $item->beverageSizes->map(function ($s) {
                    return [
                        'size' => $s->size,
                        'old_price' => $s->old_price,
                        'discount_percent' => $s->discount_percent,
                        'quantity' => $s->quantity,
                        'price' => $s->old_price * (100 - $s->discount_percent) / 100,
                    ];
                });

                return [
                    'id' => $item->id,
                    'type' => 'beverage',
                    'name' => $item->name,
                    'description' => $item->description,
                    'image' => asset('storage/' . $item->image),
                    'sizes' => $sizes,
                    'min_price' => $sizes->min('price'),
                    'max_price' => $sizes->max('price')
                ];
            })
        )->all();

        // Cập nhật hoạt động nếu là nhà hàng
        $user = auth()->user();
        if ($user && $user->role === 'restaurant' && $user->restaurant) {
            if (!$user->restaurant->last_active_at || $user->restaurant->last_active_at->lt(now()->subMinute())) {
                $user->restaurant->update(['last_active_at' => now()]);
            }
        }

        return view('web.detail_product', compact(
            'product',
            'type',
            'reviews',
            'filters',
            'productAvgRating',
            'restaurantStats',
            'totalProducts',
            'totalRestaurantReviews',
            'productImages',
            'allProducts',
            'vouchers' 
        ));
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
        $firstRestaurantId = null;

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

        foreach ($groupedItems as $restaurantId => $items) {
            $totalItems = collect($items)->sum('total');
            $shipping = $restaurantShippingFees[$restaurantId] ?? 0;

            $restaurantTotalAmounts[$restaurantId] = $totalItems;
            $restaurantTotalSums[$restaurantId] = $totalItems + $shipping;
        }

        $totalAmount = array_sum($restaurantTotalAmounts);
        $totalShippingFee = array_sum($restaurantShippingFees);
        $finalTotal = array_sum($restaurantTotalSums);

        $restaurantId = array_key_first($groupedItems);

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
            ]
        ]);

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

