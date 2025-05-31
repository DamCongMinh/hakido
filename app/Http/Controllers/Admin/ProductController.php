<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Food;
use App\Models\Beverage;

class ProductController extends Controller
{

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

            case 'beverage_pending':
                $beveragesPending = Beverage::where('status', 'pending')->get();
                return view('Admin.products.sections.beverage_pending', compact('beveragesPending'));

            case 'beverage_approved':
                $beveragesApproved = Beverage::where('status', 'approved')->get();
                return view('Admin.products.sections.beverages_pending', compact('beveragesApproved'));

            default:
                abort(404, 'Không tìm thấy loại dữ liệu phù hợp');
        }
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

    // ProductController.php



}
