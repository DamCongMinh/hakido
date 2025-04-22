<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class RestaurantStatisticsController extends Controller
{
    public function home(Request $request)
    {
        $restaurantId = Auth::id();

        $year = $request->input('year', now()->year);
        $orders = Order::where('restaurant_id', $restaurantId)
            ->whereYear('created_at', $year)
            ->where('status', 'hoàn thành')
            ->get();

        $totalRevenue = $orders->sum('total');
        $totalOrders = $orders->count();

        $monthlyStats = $orders->groupBy(function ($order) {
            return $order->created_at->format('m');
        })->map(function ($ordersInMonth, $month) {
            return (object)[
                'month' => (int) $month,
                'revenue' => $ordersInMonth->sum('total'),
                'orders' => $ordersInMonth->count()
            ];
        })->sortKeys()->values();

        $today = now()->toDateString();

        $todayOrders = Order::where('restaurant_id', $restaurantId)
            ->whereDate('created_at', $today)
            ->count();

        $processingOrders = Order::where('restaurant_id', $restaurantId)
            ->whereIn('status', ['chờ xác nhận', 'đang xử lý'])
            ->count();

        $recentOrders = Order::where('restaurant_id', $restaurantId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('restaurant.statistics.home_statistics', compact(
            'totalRevenue', 'totalOrders', 'monthlyStats',
            'todayOrders', 'processingOrders', 'recentOrders'
        ));
    }


    public function index(Request $request)
    {
        $restaurantId = Auth::id();

        // Đơn hoàn thành trong năm để tính doanh thu (đã có sẵn)
        $year = $request->input('year', now()->year);
        $orders = Order::where('restaurant_id', $restaurantId)
            ->whereYear('created_at', $year)
            ->where('status', 'hoàn thành')
            ->get();

        $totalRevenue = $orders->sum('total');
        $totalOrders = $orders->count();

        $monthlyStats = $orders->groupBy(function ($order) {
            return $order->created_at->format('m');
        })->map(function ($ordersInMonth, $month) {
            return (object)[
                'month' => (int) $month,
                'revenue' => $ordersInMonth->sum('total'),
                'orders' => $ordersInMonth->count()
            ];
        })->sortKeys()->values();

        // 🎯 THÊM DỮ LIỆU CẦN THIẾT:
        $today = now()->toDateString();

        $todayOrders = Order::where('restaurant_id', $restaurantId)
            ->whereDate('created_at', $today)
            ->count(); // Đếm đơn hôm nay

        $processingOrders = Order::where('restaurant_id', $restaurantId)
            ->whereIn('status', ['chờ xác nhận', 'đang xử lý'])
            ->count(); // Đếm đơn đang xử lý

        $recentOrders = Order::where('restaurant_id', $restaurantId)
            ->orderBy('created_at', 'desc')
            ->take(5) // lấy 5 đơn gần nhất
            ->get();
            $shippingOrders = Order::where('restaurant_id', $restaurantId)
            ->where('status', 'đang giao')
            ->count();
        
        $canceledOrders = Order::where('restaurant_id', $restaurantId)
            ->where('status', 'đã hủy')
            ->count();
        
        $completedTodayOrders = Order::where('restaurant_id', $restaurantId)
            ->where('status', 'hoàn thành')
            ->whereDate('created_at', $today)
            ->count();
           

        return view('restaurant.restaurant', compact(
            'totalRevenue', 'totalOrders', 'monthlyStats',
            'todayOrders', 'processingOrders', 'recentOrders',
            'shippingOrders', 'canceledOrders', 'completedTodayOrders'
        ));
            
    }

}
