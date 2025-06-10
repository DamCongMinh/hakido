<?php

// app/Notifications/OrderStatusNotification.php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class OrderStatusNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $message;

    public function __construct(Order $order, $message)
    {
        $this->order = $order;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $orderId = $this->order->id;
        $role = $notifiable->role;
        $status = $this->order->status;

        switch ($role) {
            case 'customer':
                $url = route('orders.items', $orderId);
                break;

            case 'restaurant':
                $url = route('restaurant.statistics.index');
                break;

            case 'shipper':
                if ($status === 'processing') {
                    $url = route('shipper.orders.available');
                } elseif ($status === 'delivering') {
                    $url = route('shipper.orders.current');
                } elseif ($status === 'delivered') {
                    $url = route('shipper.orders.history');
                } else {
                    $url = '#';
                }
                break;

            default:
                $url = '#';
                break;
        }

        return new DatabaseMessage([
            'message' => $this->message,
            'url' => $url,
            'order_id' => $orderId,
        ]);
    }



}

