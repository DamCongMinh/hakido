<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Shipper;
use App\Models\Restaurant;
use App\Models\Order;
use App\Models\Food;
use App\Models\Beverage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminStatisticController extends Controller
{
    

    public function revenueStatistics(Request $request)
    {
        $restaurants = Restaurant::all();

        $year = $request->input('year', now()->year);

        $totalRevenue = Order::whereYear('created_at', $year)
            ->whereIn('status', ['completed', 'hoàn thành'])
            ->sum('total');

        $monthlyStats = Order::selectRaw('MONTH(created_at) as month, SUM(total) as revenue')
            ->whereYear('created_at', $year)
            ->whereIn('status', ['completed', 'hoàn thành'])
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get()
            ->map(function ($item) {
                $item->month = 'Tháng ' . $item->month;
                return $item;
            });

        return view('Admin.statistics.statistics', compact('totalRevenue', 'monthlyStats', 'year', 'restaurants'));
    }

    public function orderStatistics(Request $request)
    {

        $restaurants = Restaurant::all();
        // dd($restaurants->toArray());
        $year = $request->input('year', now()->year);

        $totalOrders = Order::whereYear('created_at', $year)->count();

        $monthlyStats = Order::selectRaw('MONTH(created_at) as month, COUNT(*) as orders')
            ->whereYear('created_at', $year)
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get()
            ->map(function ($item) {
                $item->month = 'Tháng ' . $item->month;
                return $item;
            });

        return view('Admin.statistics.order_statistics', compact('totalOrders', 'monthlyStats', 'year', 'restaurants'));
    }



    public function showHomeStatistics(Request $request)
    {
        return $this->handleHomeStatistics($request);
    }

    
    private function handleHomeStatistics(Request $request)
    {
        $year = $request->input('year', now()->year);

        // Tổng số đơn trong năm
        $totalOrders = Order::whereYear('created_at', $year)->count();

        // Tổng doanh thu trong năm (chỉ lấy đơn hoàn thành)
        $totalRevenue = Order::whereYear('created_at', $year)
            ->whereIn('status', ['completed', 'hoàn thành'])
            ->sum('total');

        // Doanh thu hôm nay
        $today = Carbon::today();
        $todayRevenue = Order::whereDate('created_at', $today)
            ->whereIn('status', ['completed', 'hoàn thành'])
            ->sum('total');

        // Đơn hàng hôm nay
        $todayOrders = Order::whereDate('created_at', $today)->count();

        // Doanh thu theo ngày (trong tháng hiện tại)
        $dailyRevenue = Order::selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereIn('status', ['completed', 'hoàn thành'])
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get();

        $revenueDates = $dailyRevenue->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d/m'))->toArray();
        $revenueValues = $dailyRevenue->pluck('revenue')->toArray();

        $customerCount = Customer::count();
        $shipperCount = Shipper::count();
        $restaurantCount = Restaurant::count();


        // Count foods & beverages
        $foodCount = Food::count();
        $beverageCount = Beverage::count();

        // Orders per restaurant
        $ordersPerRestaurant = Order::selectRaw('restaurant_id, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->groupBy('restaurant_id')
            ->get();

        $restaurantNames = [];
        $restaurantOrderCounts = [];

        foreach ($ordersPerRestaurant as $item) {
            $restaurant = Restaurant::find($item->restaurant_id);
            if ($restaurant) {
                $restaurantNames[] = $restaurant->name;
                $restaurantOrderCounts[] = $item->count;
            }
        }

        return view('Admin.statistics.home_statistics', [
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'todayRevenue' => $todayRevenue,
            'todayOrders' => $todayOrders,
            'revenueDates' => $revenueDates,
            'revenueValues' => $revenueValues,
            'customerCount' => $customerCount,
            'shipperCount' => $shipperCount,
            'restaurantCount' => $restaurantCount,
            'foodsCount' => $foodCount,
            'beveragesCount' => $beverageCount,
            'restaurantNames' => $restaurantNames,
            'restaurantOrderCounts' => $restaurantOrderCounts,
            'year' => $year,
        ]);
    }

    public function inventoryStatistics()
    {
        $restaurants = Restaurant::with(['foods', 'beverages.beverageSizes'])->get();

        // Tổng số lượng món ăn đã bán
        $soldFoods = DB::table('order_items')
            ->where('product_type', 'food')
            ->select('product_id', DB::raw('SUM(quantity) as sold_quantity'))
            ->groupBy('product_id')
            ->pluck('sold_quantity', 'product_id');
    
        // Tổng số lượng đồ uống đã bán (theo size)
        $soldBeverageSizes = DB::table('order_items')
            ->where('product_type', 'beverage')
            ->select('size_id', DB::raw('SUM(quantity) as sold_quantity'))
            ->groupBy('size_id')
            ->pluck('sold_quantity', 'size_id');

        return view('Admin.statistics.inventory_statistics', compact('restaurants', 'soldFoods', 'soldBeverageSizes'));
    }



    
}
