<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Food;
use App\Models\Beverage;


class FoodController extends Controller
{
    // public function index()
    // {
    //     $foodsApproved = Food::with('restaurant')->where('status', 'approved')->get();
    //     $foodsPending = Food::with('restaurant')->where('status', 'pending')->get();

    //     $beveragesApproved = Beverage::with('restaurant')->where('status', 'approved')->get();
    //     $beveragesPending = Beverage::with('restaurant')->where('status', 'pending')->get();

    //     return view('products.product_control_management', compact(
    //         'foodsApproved',
    //         'foodsPending',
    //         'beveragesApproved',
    //         'beveragesPending'
    //     ));
    // }  
    
    public function index(Request $request)
    {
        $view = $request->input('view', 'food_pending');

        switch ($view) {
            case 'food_pending':
                $foodsPending = Food::where('status', 'pending')->get();
                return view('Admin.products.sections.food_pending', compact('foodsPending'));

            case 'food_approved':
                $foodsApproved = Food::where('status', 'approved')->get();
                return view('Admin.products.sections.food_approved', compact('foodsApproved'));

            default:
                abort(404);
        }
    }


    public function edit($id)
    {
        $food = Food::findOrFail($id);

        return view('products.edit', [
            'item' => $food,
            'type' => 'food',
            'updateRoute' => route('admin.foods.update', $food->id),
            'indexRoute' => route('admin.foods.index'),
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

    public function reject($id)
    {
        $food = Food::findOrFail($id);
        $food->status = 'rejected';
        $food->is_approved = false;
        $food->save();

        return redirect()->back()->with('success', 'Món ăn đã bị từ chối.');
    }




    public function destroy($id)
    {
        $food = Food::findOrFail($id);
        $food->delete();

        return redirect()->route('foods.index')->with('success', 'Xóa đồ ăn thành công.');
    }
}
