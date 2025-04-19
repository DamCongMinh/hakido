<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Beverage;

class BeverageController extends Controller
{
    public function index()
    {
        $beveragesApproved = Beverage::with('restaurant')->where('status', 'approved')->get();
        $beveragesPending = Beverage::with('restaurant')->where('status', 'pending')->get();

        return view('admin.beverages.index', compact('beveragesApproved', 'beveragesPending'));
    }


    public function edit($id)
    {
        $beverage = Beverage::findOrFail($id);
        return view('admin.beverages.edit', compact('beverage'));
    }

    public function update(Request $request, $id)
    {
        $beverage = Beverage::findOrFail($id);

        $beverage->update($request->only('name', 'description', 'price'));

        return redirect()->route('beverages.index')->with('success', 'Cập nhật đồ uống thành công.');
    }

    public function approve($id)
    {
        $beverage = Beverage::findOrFail($id);
        $beverage->status = 'approved';
        $beverage->save();

        return redirect()->route('beverages.index')->with('success', 'Duyệt đồ uống thành công.');
    }

    public function destroy($id)
    {
        $beverage = Beverage::findOrFail($id);
        $beverage->delete();

        return redirect()->route('beverages.index')->with('success', 'Xóa đồ uống thành công.');
    }
}
