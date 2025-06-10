<?php

// app/Observers/OrderObserver.php

namespace App\Observers;

use App\Models\Order;
use App\Notifications\OrderStatusNotification;

class OrderObserver
{
    public function created(Order $order)
    {
        $message = "Đơn hàng của bạn đang chờ xác nhận.";
        $this->notifyAll($order, $message);
    }

    public function updated(Order $order)
    {
        if ($order->isDirty('status')) {
            $statusText = Order::statusMapping()[$order->status] ?? $order->status;
            $message = "Đơn hàng #{$order->id}: {$statusText}";
            $this->notifyAll($order, $message);
        }
    }

    protected function notifyAll(Order $order, $baseMessage)
    {
        if ($order->user) {
            $message = $baseMessage;
            $order->user->notify(new OrderStatusNotification($order, $message));
        }

        if ($order->restaurant && $order->restaurant->user) {
            $message = $baseMessage;
            $order->restaurant->user->notify(new OrderStatusNotification($order, $message));
        }

        if ($order->status === 'processing') {
            // Gửi cho tất cả shipper (đã đăng ký vai trò shipper)
            $shippers = \App\Models\User::where('role', 'shipper')->get();
            foreach ($shippers as $shipper) {
                $message = "Có đơn hàng mới #{$order->id} đang chờ nhận.";
                $shipper->notify(new OrderStatusNotification($order, $message));
            }
        } elseif ($order->shipper) {
            // Gửi cho shipper đã nhận đơn
            $message = $baseMessage;
            $order->shipper->notify(new OrderStatusNotification($order, $message));
        }
    }


}

