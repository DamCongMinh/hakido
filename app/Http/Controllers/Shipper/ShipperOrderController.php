<?php

namespace App\Http\Controllers\Shipper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ShipperOrderController extends Controller
{
    // 1. Hiển thị các đơn hàng đang chờ shipper nhận
    public function availableOrders()
    {
        $orders = Order::where('status', 'processing')->whereNull('shipper_id')->get();
        return view('shipper.available_orders', compact('orders'));
    }

    // 2. Shipper nhận đơn hàng
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
        $orders = Order::where('shipper_id', Auth::id())
                       ->whereIn('status', ['completed', 'canceled'])
                       ->latest()
                       ->get();

        return view('shipper.delivery_history', compact('orders'));
    }
}
