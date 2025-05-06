<?php

namespace App\Http\Controllers\Shipper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ShipperOrderController extends Controller
{
    
    public function availableOrders()
    {
        $orders = Order::with('orderItems')
            ->where('status', 'processing')
            ->whereNull('shipper_id')
            ->latest()
            ->get();
        return view('shipper.available_orders', compact('orders'));
    }


    
    public function acceptOrder($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status !== 'processing' || $order->shipper_id !== null) {
            return back()->with('error', 'Đơn hàng đã được nhận hoặc không hợp lệ.');
        }

        $order->shipper_id = Auth::id();
        $order->status = 'delivering';
        $order->save();

        return redirect()->route('shipper.orders.current')->with('success', 'Bạn đã nhận đơn hàng.');
    }

    // 3. Hiển thị các đơn đang giao của shipper hiện tại
    public function currentDelivery()
    {
        $orders = Order::where('shipper_id', Auth::id())->where('status', 'delivering')->get();
        return view('shipper.current_delivery', compact('orders'));
    }

    // 4. Cập nhật trạng thái giao hàng
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:delivered,failed',
            'note'   => 'nullable|string|max:255',
        ]);

        $order = Order::where('shipper_id', Auth::id())->where('status', 'delivering')->findOrFail($id);
        $order->status = $request->status === 'delivered' ? 'completed' : 'canceled';
        $order->note = $request->note;
        $order->save();

        return redirect()->route('shipper.orders.history')->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }

    // 5. Hiển thị lịch sử đơn hàng của shipper
    public function deliveryHistory()
    {
        $successfulOrders = Order::where('shipper_id', Auth::id())
                                ->where('status', 'completed')
                                ->latest()
                                ->get();

        $failedOrders = Order::where('shipper_id', Auth::id())
                            ->where('status', 'canceled')
                            ->latest()
                            ->get();

        return view('shipper.delivery_history', compact('successfulOrders', 'failedOrders'));
    }

    public function incomeStats()
    {
        $shipperId = Auth::id();

        // Lấy tất cả đơn hàng giao thành công
        $successfulOrders = Order::where('shipper_id', $shipperId)
            ->where('status', 'completed')
            ->get();

        // Tổng đơn và tổng thu nhập
        $totalOrders = $successfulOrders->count();
        $totalIncome = $successfulOrders->sum('shipping_fee');

        // Doanh thu theo tháng (năm hiện tại)
        $monthlyIncome = Order::where('shipper_id', $shipperId)
            ->where('status', 'completed')
            ->whereYear('updated_at', now()->year)
            ->selectRaw('MONTH(updated_at) as month, SUM(shipping_fee) as total_fee, COUNT(*) as order_count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('shipper.income_stats', compact('totalOrders', 'totalIncome', 'monthlyIncome'));
    }


}
