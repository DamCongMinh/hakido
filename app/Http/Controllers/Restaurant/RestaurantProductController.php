<?php

namespace App\Http\Controllers\Restaurant;

use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Food;
use App\Models\Beverage;
use Illuminate\Support\Facades\Auth;

class RestaurantProductController extends Controller
{
    public function home()
    {
        $restaurantId = Auth::user()->id;

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
            'image' => 'required|image',
        ]);

        $data = [
            'restaurant_id' => Auth::id(),
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'status' => 'pending',
            'image' => $request->file('image')->store('products', 'public'),
            'is_active' => 1,
        ];

        if ($request->type === 'food') {
            $data['old_price'] = $request->old_price;
            $data['discount_percent'] = $request->discount_percent ?? 0;
            Food::create($data);
        } else {
            $beverage = Beverage::create($data);

            // Xử lý sizes nếu có
            if ($request->has('sizes')) {
                foreach ($request->sizes as $size => $info) {
                    if (!empty($info['old_price'])) {
                        $beverage->beverageSizes()->create([
                            'size' => $size,
                            'old_price' => $info['old_price'],
                            'discount_percent' => $info['discount_percent'] ?? 0,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('restaurant.products.home')->with('success', 'Thêm sản phẩm thành công, chờ admin duyệt!');
    }
    
    public function edit($id)
    {
        $food = Food::where('id', $id)->where('restaurant_id', Auth::id())->first();
        $beverage = Beverage::where('id', $id)->where('restaurant_id', Auth::id())->with('beverageSizes')->first();

        $item = $food ?? $beverage;
        $type = $food ? 'food' : 'beverage';

        if (!$item) abort(404);

        // Nếu là beverage thì chuẩn bị dữ liệu sizes để truyền ra view
        if ($type === 'beverage') {
            $sizes = [];
            foreach ($item->beverageSizes as $size) {
                $sizes[$size->size] = [
                    'old_price' => $size->old_price,
                    'discount_percent' => $size->discount_percent,
                ];
            }
            $item->sizes = $sizes;
        }

        $categories = Category::all();
        return view('restaurant.products.edit', compact('item', 'type', 'categories'));
    }


    public function update(Request $request, $id)
    {
        $type = $request->input('type', 'food');

        $validateRules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image',
        ];

        if ($type === 'food') {
            $validateRules['old_price'] = 'required|numeric|min:0';
            $validateRules['discount_percent'] = 'nullable|numeric|min:0|max:100';
        }

        $request->validate($validateRules);

        $food = Food::where('id', $id)->where('restaurant_id', Auth::id())->first();
        $beverage = Beverage::where('id', $id)->where('restaurant_id', Auth::id())->first();

        $item = $food ?? $beverage;

        if (!$item) {
            abort(404);
        }

        $item->name = $request->name;
        $item->description = $request->description;
        $item->category_id = $request->category_id;
        $item->is_active = $request->is_active;
        $item->status = 'pending'; // reset lại pending khi chỉnh sửa
        $item->is_approved = 0;

        if ($request->hasFile('image')) {
            $item->image = $request->file('image')->store('products', 'public');
        }

        if ($type === 'food') {
            $item->old_price = $request->old_price;
            $item->discount_percent = $request->discount_percent ?? 0;
        }

        $item->save();

        if ($type === 'beverage') {
            // Cập nhật lại beverage_sizes
            if ($request->has('sizes')) {
                // Xóa size cũ
                $item->beverageSizes()->delete();

                // Thêm size mới
                foreach ($request->sizes as $size => $info) {
                    if (!empty($info['old_price'])) {
                        $item->beverageSizes()->create([
                            'size' => $size,
                            'old_price' => $info['old_price'],
                            'discount_percent' => $info['discount_percent'] ?? 0,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('restaurant.products.home')->with('success', 'Đã cập nhật sản phẩm. Vui lòng chờ admin duyệt!');
    }

    public function destroy($id)
    {
        $food = Food::where('id', $id)->where('restaurant_id', Auth::id())->first();
        $beverage = Beverage::where('id', $id)->where('restaurant_id', Auth::id())->first();

        $item = $food ?? $beverage;
        if (!$item) abort(404);

        $item->delete();

        return redirect()->route('restaurant.products.home')->with('success', 'Xóa sản phẩm thành công');
    }
}
