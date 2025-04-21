<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class AdminStatisticController extends Controller
{
    public function index(Request $request)
    {
        // Lấy năm từ request, mặc định là năm hiện tại
        $year = $request->input('year', now()->year);

        $totalOrders = Order::whereYear('created_at', $year)->count();

        $totalRevenue = Order::whereYear('created_at', $year)
            ->whereIn('status', ['completed', 'hoàn thành'])
            ->sum('total');

        // Truy vấn thống kê theo tháng
        $monthlyStats = Order::selectRaw('MONTH(created_at) as month, SUM(total) as revenue, COUNT(*) as orders')
            ->whereYear('created_at', $year)
            ->whereIn('status', ['completed', 'hoàn thành'])
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        // Đổi số tháng thành 'Tháng x'
        $monthlyStats = $monthlyStats->map(function ($item) {
            $item->month = 'Tháng ' . $item->month;
            return $item;
        });

        return view('Admin.statistics.statistics', compact('totalRevenue', 'totalOrders', 'monthlyStats', 'year'));
    }
}
