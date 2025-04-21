<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Food;
use App\Models\Beverage;

class ProductController extends Controller
{

    public function index()
    {
        $foodsApproved = Food::with('restaurant')->where('status', 'approved')->get();
        $foodsPending = Food::with('restaurant')->where('status', 'pending')->get();

        $beveragesApproved = Beverage::with('restaurant')->where('status', 'approved')->get();
        $beveragesPending = Beverage::with('restaurant')->where('status', 'pending')->get();

        return view('products.product_control_management', compact(
            'foodsApproved', 'foodsPending',
            'beveragesApproved', 'beveragesPending'
        ));
    }

    public function uploadImage(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');

            $product->image_path = 'storage/' . $path;
            $product->save();

            return response()->json([
                'success' => true,
                'image_url' => asset('storage/' . $path),
            ]);
        }

        return response()->json(['success' => false], 400);
    }


}
