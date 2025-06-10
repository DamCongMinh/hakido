<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class NotificationController extends Controller
{
    public function updateStatus(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        $newStatus = $request->input('status');

        // Kiểm tra nếu status hợp lệ
        if (!array_key_exists($newStatus, Order::statusMapping())) {
            return back()->with('error', 'Trạng thái không hợp lệ');
        }

        // Cập nhật status — observer sẽ tự xử lý thông báo
        $order->update([
            'status' => $newStatus,
        ]);

        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng và gửi thông báo');
    }



}
