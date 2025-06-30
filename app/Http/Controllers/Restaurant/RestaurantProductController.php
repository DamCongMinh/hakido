<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Food;
use App\Models\Beverage;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class RestaurantProductController extends Controller
{
    public function home()
    {
        $restaurantId = Auth::user()->restaurant->id;
        $foods = Food::where('restaurant_id', $restaurantId)->get();
        $beverages = Beverage::where('restaurant_id', $restaurantId)->get();

        return view('restaurant.products.home_product', compact('foods', 'beverages'));
    }

    public function create(Request $request)
    {
        $type = $request->query('type', 'food');
        $categories = Category::all();

        return view('restaurant.products.create', compact('type', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:food,beverage',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image',
        ]);

        $restaurantId = Auth::user()->restaurant->id;

        $data = [
            'restaurant_id' => $restaurantId,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'status' => 'pending',
            'image' => $request->file('image')->store('products', 'public'),
            'is_active' => 1,
        ];

        if ($request->type === 'food') {
            $data['old_price'] = $request->old_price ?? 0;
            $data['discount_percent'] = $request->discount_percent ?? 0;
            $data['quantity'] = $request->quantity ?? 0;

            $discounted = $data['old_price'] * (1 - $data['discount_percent'] / 100);
            $data['min_price'] = $discounted;
            $data['max_price'] = $discounted;

            Food::create($data);
        } else {
            $beverage = Beverage::create($data);

            $prices = [];

            if ($request->has('sizes')) {
                foreach ($request->sizes as $size => $info) {
                    if (!empty($info['old_price'])) {
                        $discount = $info['discount_percent'] ?? 0;
                        $new_price = $info['old_price'] * (1 - $discount / 100);
                        $prices[] = $new_price;

                        $beverage->beverageSizes()->create([
                            'size' => $size,
                            'old_price' => $info['old_price'],
                            'discount_percent' => $discount,
                            'quantity' => $info['quantity'] ?? 0,
                        ]);
                    }
                }
            }

            $beverage->min_price = collect($prices)->min() ?? 0;
            $beverage->max_price = collect($prices)->max() ?? 0;
            $beverage->save();
        }

        return redirect()->route('restaurant.products.home')->with('success', 'Thêm sản phẩm thành công, chờ admin duyệt!');
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'type' => 'required|in:food,beverage',
    //         'category_id' => 'required|exists:categories,id',
    //         'name' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'image' => 'required|image',
    //     ]);

    //     $restaurantId = Auth::user()->restaurant->id;

    //     // Lưu file ảnh, gán vào biến $imagePath
    //     if ($request->hasFile('image')) {
    //         $imagePath = $request->file('image')->store('products', 'public');
    //         logger('✅ Đã lưu ảnh tại:', [$imagePath]);
    //     } else {
    //         return back()->withErrors(['image' => 'Không nhận được file ảnh.']);
    //     }

    //     // Log ra tên ảnh lưu lại (có thể bật debug)
    //     // logger('Image path:', [$imagePath]);

    //     $data = [
    //         'restaurant_id' => $restaurantId,
    //         'category_id' => $request->category_id,
    //         'name' => $request->name,
    //         'description' => $request->description,
    //         'status' => 'pending',
    //         'image' => $imagePath,
    //         'is_active' => 1,
    //     ];

    //     if ($request->type === 'food') {
    //         $data['old_price'] = $request->old_price ?? 0;
    //         $data['discount_percent'] = $request->discount_percent ?? 0;
    //         $data['quantity'] = $request->quantity ?? 0;

    //         $discounted = $data['old_price'] * (1 - $data['discount_percent'] / 100);
    //         $data['min_price'] = $discounted;
    //         $data['max_price'] = $discounted;

    //         Food::create($data);
    //     } else {
    //         $beverage = Beverage::create($data);

    //         $prices = [];

    //         if ($request->has('sizes')) {
    //             foreach ($request->sizes as $size => $info) {
    //                 if (!empty($info['old_price'])) {
    //                     $discount = $info['discount_percent'] ?? 0;
    //                     $new_price = $info['old_price'] * (1 - $discount / 100);
    //                     $prices[] = $new_price;

    //                     $beverage->beverageSizes()->create([
    //                         'size' => $size,
    //                         'old_price' => $info['old_price'],
    //                         'discount_percent' => $discount,
    //                         'quantity' => $info['quantity'] ?? 0,
    //                     ]);
    //                 }
    //             }
    //         }

    //         $beverage->min_price = collect($prices)->min() ?? 0;
    //         $beverage->max_price = collect($prices)->max() ?? 0;
    //         $beverage->save();
    //     }

    //     return redirect()->route('restaurant.products.home')->with('success', 'Thêm sản phẩm thành công, chờ admin duyệt!');
    // }


    public function edit($id)
    {
        $restaurantId = Auth::user()->restaurant->id;

        $food = Food::where('id', $id)->where('restaurant_id', $restaurantId)->first();
        $beverage = Beverage::where('id', $id)->where('restaurant_id', $restaurantId)->with('beverageSizes')->first();

        $item = $food ?? $beverage;
        $type = $food ? 'food' : 'beverage';

        if (!$item) {
            abort(404);
        }

        if ($type === 'beverage') {
            $sizes = [];
            foreach ($item->beverageSizes as $size) {
                $sizes[$size->size] = [
                    'old_price' => $size->old_price,
                    'discount_percent' => $size->discount_percent,
                    'quantity' => $size->quantity,
                ];
            }
            $item->sizes = $sizes;
        }

        $categories = Category::all();

        return view('restaurant.products.edit', compact('item', 'type', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:food,beverage',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image',
            'old_price' => 'nullable|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $restaurantId = Auth::user()->restaurant->id;

        $food = Food::where('id', $id)->where('restaurant_id', $restaurantId)->first();
        $beverage = Beverage::where('id', $id)->where('restaurant_id', $restaurantId)->first();
        $item = $food ?? $beverage;
        $type = $request->input('type', 'food');

        if (!$item) {
            abort(404);
        }

        $item->name = $request->name;
        $item->description = $request->description;
        $item->category_id = $request->category_id;
        $item->is_active = $request->is_active ?? 1;
        $item->status = 'pending';
        $item->is_approved = 0;

        if ($request->hasFile('image')) {
            $item->image = $request->file('image')->store('products', 'public');
        }

        if ($type === 'food') {
            $item->old_price = $request->old_price ?? 0;
            $item->discount_percent = $request->discount_percent ?? 0;
            $item->quantity = $request->quantity ?? 0;

            $discounted = $item->old_price * (1 - $item->discount_percent / 100);
            $item->min_price = $discounted;
            $item->max_price = $discounted;
        }

        $item->save();

        if ($type === 'beverage' && $request->has('sizes')) {
            $item->beverageSizes()->delete();

            $prices = [];

            foreach ($request->sizes as $size => $info) {
                if (!empty($info['old_price'])) {
                    $discount = $info['discount_percent'] ?? 0;
                    $new_price = $info['old_price'] * (1 - $discount / 100);
                    $prices[] = $new_price;

                    $item->beverageSizes()->create([
                        'size' => $size,
                        'old_price' => $info['old_price'],
                        'discount_percent' => $discount,
                        'quantity' => $info['quantity'] ?? 0,
                    ]);
                }
            }

            $item->min_price = collect($prices)->min() ?? 0;
            $item->max_price = collect($prices)->max() ?? 0;
            $item->save();
        }

        return redirect()->route('restaurant.products.home')->with('success', 'Đã cập nhật sản phẩm, vui lòng chờ admin duyệt!');
    }

    public function destroy($id)
    {
        $restaurantId = Auth::user()->restaurant->id;

        $food = Food::where('id', $id)->where('restaurant_id', $restaurantId)->first();
        $beverage = Beverage::where('id', $id)->where('restaurant_id', $restaurantId)->first();

        $item = $food ?? $beverage;

        if (!$item) {
            abort(404);
        }

        $item->delete();

        return redirect()->route('restaurant.products.home')->with('success', 'Xóa sản phẩm thành công!');
    }
}
