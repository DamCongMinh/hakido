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

}
