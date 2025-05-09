<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Beverage;
use App\Models\Food;

class BeverageController extends Controller
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
        $beverage = Beverage::findOrFail($id);

        return view('products.edit',[
            'item' => $beverage,
            'type' => 'beverage',
            'updateRoute' => route('beverages.update', $beverage->id),
            'indexRoute' => route('beverages.index'),
        ]);
    }


    public function update(Request $request, $id)
    {
        $beverage = Beverage::findOrFail($id);

        $data = $request->only('name', 'description', 'price', 'status');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('beverages', 'public');
        }

        $beverage->update($data);

        return redirect()->route('beverages.index')->with('success', 'Cập nhật đồ uống thành công.');
    }


    public function approve($id)
    {
        $beverage = Beverage::findOrFail($id);
        $beverage->is_approved = true;
        $beverage->status = 'approved';
        $beverage->save();

        return redirect()->back()->with('success', 'Đồ uống đã được duyệt!');
    }

    public function reject($id)
    {
        $beverage = Beverage::findOrFail($id);
        $beverage->status = 'rejected';
        $beverage->is_approved = false;
        $beverage->save();

        return redirect()->back()->with('success', 'Đồ uống đã bị từ chối.');
    }




    public function destroy($id)
    {
        $beverage = Beverage::findOrFail($id);
        $beverage->delete();

        return redirect()->route('beverages.index')->with('success', 'Xóa đồ uống thành công.');
    }
}
