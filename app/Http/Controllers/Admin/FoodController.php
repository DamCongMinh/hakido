<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Food;
use App\Models\Beverage;


class FoodController extends Controller
{
    public function index()
    {
        $foodsApproved = Food::with('restaurant')->where('status', 'approved')->get();
        $foodsPending = Food::with('restaurant')->where('status', 'pending')->get();

        $beveragesApproved = Beverage::with('restaurant')->where('status', 'approved')->get();
        $beveragesPending = Beverage::with('restaurant')->where('status', 'pending')->get();

        return view('products.product_control_management', compact(
            'foodsApproved',
            'foodsPending',
            'beveragesApproved',
            'beveragesPending'
        ));
    }   

    public function edit($id)
    {
        $food = Food::findOrFail($id);

        return view('products.edit', [
            'item' => $food,
            'type' => 'food',
            'updateRoute' => route('foods.update', $food->id),
            'indexRoute' => route('foods.index'),
        ]);
    }


    public function update(Request $request, $id)
    {
        $food = Food::findOrFail($id);
        $data = $request->only('name', 'description', 'price', 'status');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('foods', 'public');
        }

        $food->update($data);

        return redirect()->route('foods.index')->with('success', 'Cập nhật đồ ăn thành công.');
    }


    public function approve($id)
    {
        $food = Food::findOrFail($id);
        $food->is_approved = true;
        $food->status = 'approved'; // <--- thêm dòng này
        $food->save();

        return redirect()->back()->with('success', 'Món ăn đã được duyệt!');
    }



    public function destroy($id)
    {
        $food = Food::findOrFail($id);
        $food->delete();

        return redirect()->route('foods.index')->with('success', 'Xóa đồ ăn thành công.');
    }
}
