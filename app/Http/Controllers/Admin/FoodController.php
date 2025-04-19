<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Food;

class FoodController extends Controller
{
    public function index()
    {
        $foodsApproved = Food::where('status', 'approved')->get();
        $foodsPending = Food::where('status', 'pending')->get();

        return view('admin.foods.index', compact('foodsApproved', 'foodsPending'));
    }

    public function edit($id)
    {
        $food = Food::findOrFail($id);
        return view('admin.foods.edit', compact('food'));
    }

    public function update(Request $request, $id)
    {
        $food = Food::findOrFail($id);

        $food->update($request->only('name', 'price', 'description'));

        return redirect()->route('foods.index')->with('success', 'Cập nhật đồ ăn thành công.');
    }

    public function approve($id)
    {
        $food = Food::findOrFail($id);
        $food->status = 'approved';
        $food->save();

        return redirect()->route('foods.index')->with('success', 'Duyệt đồ ăn thành công.');
    }

    public function destroy($id)
    {
        $food = Food::findOrFail($id);
        $food->delete();

        return redirect()->route('foods.index')->with('success', 'Xóa đồ ăn thành công.');
    }
}
