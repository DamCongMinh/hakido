<?php
namespace App\Http\Controllers\Restaurant;

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
        return view('restaurant.products.create', compact('type'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:food,beverage',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image',
        ]);

        $data = $request->only('name', 'description', 'price');
        $data['restaurant_id'] = Auth::id();
        $data['status'] = 'pending';
        $data['image'] = $request->file('image')->store('products', 'public');

        if ($request->type === 'food') {
            Food::create($data);
        } else {
            Beverage::create($data);
        }

        return redirect()->route('restaurant.products.home')->with('success', 'Thêm sản phẩm thành công, chờ admin duyệt!');
    }

    public function edit($id)
    {
        $food = Food::where('id', $id)->where('restaurant_id', Auth::id())->first();
        $beverage = Beverage::where('id', $id)->where('restaurant_id', Auth::id())->first();

        $item = $food ?? $beverage;
        $type = $food ? 'food' : 'beverage';

        if (!$item) abort(404);

        return view('restaurant.products.edit', compact('item', 'type'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image',
        ]);

        $food = Food::where('id', $id)->where('restaurant_id', Auth::id())->first();
        $beverage = Beverage::where('id', $id)->where('restaurant_id', Auth::id())->first();

        $item = $food ?? $beverage;

        if (!$item) abort(404);

        $item->name = $request->name;
        $item->description = $request->description;
        $item->price = $request->price;
        $item->status = 'pending'; 
        if ($request->hasFile('image')) {
            $item->image = $request->file('image')->store('products', 'public');
        }

        $item->save();

        return redirect()->route('restaurant.products.home')->with('success', 'Đã thêm sản phẩm thành công. Vui lòng chờ admin duyệt!');
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
