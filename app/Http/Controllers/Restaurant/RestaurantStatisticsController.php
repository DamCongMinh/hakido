<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\Order;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RestaurantStatisticsController extends Controller
{
    public function home(Request $request)
    {
        $restaurant = Auth::user()->restaurant;
        if (!$restaurant) {
            abort(403, 'Bạn không phải là nhà hàng hoặc chưa được gán nhà hàng.');
        }

        $restaurantId = $restaurant->id;
        $year = $request->input('year', now()->year);
        $today = now()->toDateString();

        // Lấy thống kê doanh thu theo tháng
        $rawStats = DB::table('orders')
            ->selectRaw('MONTH(created_at) as month, SUM(total) as revenue, COUNT(*) as orders')
            ->whereYear('created_at', $year)
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'completed')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get()
            ->keyBy('month');

        // Đảm bảo đủ 12 tháng
        $monthlyStats = collect(range(1, 12))->map(function ($month) use ($rawStats) {
            return (object) [
                'month' => $month,
                'revenue' => $rawStats[$month]->revenue ?? 0,
                'orders' => $rawStats[$month]->orders ?? 0,
            ];
        });

        // Tổng doanh thu trong năm
        $totalRevenue = DB::table('orders')
            ->whereYear('created_at', $year)
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'completed')
            ->sum('total');

        // Tổng số đơn trong năm
        $totalOrders = DB::table('orders')
            ->whereYear('created_at', $year)
            ->where('restaurant_id', $restaurantId)
            ->count();

        // Các thống kê trong ngày
        $todayOrders = DB::table('orders')
            ->whereDate('created_at', $today)
            ->where('restaurant_id', $restaurantId)
            ->count();

        $processingOrders = DB::table('orders')
            ->whereDate('created_at', $today)
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'processing')
            ->count();

        $shippingOrders = DB::table('orders')
            ->whereDate('created_at', $today)
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'delivering')
            ->count();

        $canceledOrders = DB::table('orders')
            ->whereDate('created_at', $today)
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'canceled')
            ->count();

        $completedTodayOrders = DB::table('orders')
            ->whereDate('created_at', $today)
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'completed')
            ->count();

        return view('restaurant.statistics.home_statistics', compact(
            'year', 'monthlyStats', 'totalRevenue', 'totalOrders',
            'todayOrders', 'processingOrders', 'shippingOrders',
            'canceledOrders', 'completedTodayOrders'
        ));
    }




    public function index(Request $request)
    {
        $restaurant = Auth::user()->restaurant;
        if (!$restaurant) {
            abort(403, 'Bạn không phải là nhà hàng hoặc chưa được gán nhà hàng.');
        }

        $restaurantId = $restaurant->id;
        $year = $request->input('year', now()->year);

        $orders = Order::where('restaurant_id', $restaurantId)
            ->whereYear('created_at', $year)
            ->where('status', 'completed')->get();

        $totalRevenue = $orders->sum('total');
        $totalOrders = $orders->count();

        $monthlyStats = collect(range(1, 12))->map(function ($month) use ($orders) {
            $ordersInMonth = $orders->filter(function ($order) use ($month) {
                return $order->created_at->month == $month;
            });
        
            return (object)[
                'month' => $month,
                'revenue' => $ordersInMonth->sum('total'),
                'orders' => $ordersInMonth->count(),
            ];
        });
        
        

        $today = now()->toDateString();

        $todayOrders = Order::where('restaurant_id', $restaurantId)
            ->whereDate('created_at', $today)->count();

        $processingOrders = Order::where('restaurant_id', $restaurantId)
            ->whereIn('status', ['pending', 'processing'])->count();

        $shippingOrders = Order::where('restaurant_id', $restaurantId)
            ->where('status', 'delivering')->count();

        $canceledOrders = Order::where('restaurant_id', $restaurantId)
            ->where('status', 'canceled')->count();

        $completedTodayOrders = Order::where('restaurant_id', $restaurantId)
            ->where('status', 'completed')
            ->whereDate('created_at', $today)->count();

        $recentOrders = Order::where('restaurant_id', $restaurantId)
            ->orderBy('created_at', 'desc')->take(5)->get();

        return view('restaurant.restaurant', compact(
            'totalRevenue', 'totalOrders', 'monthlyStats',
            'todayOrders', 'processingOrders', 'recentOrders',
            'shippingOrders', 'canceledOrders', 'completedTodayOrders'
        ));
    }

    public function approveOrder($id): RedirectResponse
    {
        $order = Order::findOrFail($id);

        switch ($order->status) {
            case 'pending':
                $order->status = 'processing';
                break;
            case 'processing':
                $order->status = 'delivering';
                break;
            case 'delivering':
                $order->status = 'completed';
                break;
        }

        $order->save();

        return redirect()->back()->with('success', 'Trạng thái đơn hàng đã được cập nhật.');
    }

    public function cancelOrder($id): RedirectResponse
    {
        $order = Order::findOrFail($id);

        if (in_array($order->status, ['pending', 'processing', 'delivering'])) {
            $order->status = 'canceled';
            $order->save();
        }

        return redirect()->back()->with('success', 'Đơn hàng đã được hủy.');
    }
}

