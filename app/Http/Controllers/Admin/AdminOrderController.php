<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    // Hiển thị danh sách đơn hàng
    public function index()
    {
        $orders = Order::with(['customer', 'restaurant', 'shipper'])->orderBy('created_at', 'desc')->get();
        $shippers = User::where('role', 'shipper')->where('is_active', true)->get();

        return view('Admin.orders.admin_order', compact('orders', 'shippers'));
    }

    // Hiển thị chi tiết 1 đơn hàng
    public function show(Order $order)
    {
        $order->load(['customer', 'restaurant', 'shipper', 'orderItems']);
        $shippers = User::where('role', 'shipper')->where('is_active', true)->get();

        return view('admin.orders.show', compact('order', 'shippers'));
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:chờ xác nhận,đang xử lý,đang giao,hoàn thành,đã hủy'
        ]);
        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công.');
    }


    // Gán shipper cho đơn hàng
    public function assignShipper(Request $request, Order $order)
    {
        $request->validate([
            'shipper_id' => 'required|exists:users,id'
        ]);

        $order->shipper_id = $request->shipper_id;
        $order->save();

        return redirect()->back()->with('success', 'Đã gán shipper thành công.');
    }

    // Hủy đơn hàng
    public function cancel(Order $order)
    {
        $order->status = 'cancelled';
        $order->save();

        return redirect()->back()->with('success', 'Đơn hàng đã được hủy.');
    }
}

 