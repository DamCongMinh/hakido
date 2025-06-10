<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    public static function statusMapping(): array
    {
        return [
            'pending'    => 'Chờ xác nhận',
            'processing' => 'Chờ Shipper nhận đơn',
            'delivering' => 'Đang giao',
            'completed'  => 'Hoàn thành',
            'canceled'   => 'Đã hủy',
        ];
    }

    protected $fillable = [
        'customer_id',
        'restaurant_id',
        'shipper_id',
        'status',
        'total',
        'shipping_fee',
        'receiver_name',
        'receiver_phone',
        'receiver_address',
        'payment_method',
        'note',
        'vnp_TxnRef',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }


    // Mối quan hệ với nhà hàng (restaurant)
    public function restaurant()
    {
        return $this->belongsTo(\App\Models\Restaurant::class, 'restaurant_id', 'id');
    }

    // Mối quan hệ với shipper
    public function shipper()
    {
        return $this->belongsTo(User::class, 'shipper_id');
    }

    // Mối quan hệ với chi tiết đơn hàng (nếu có)
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class); // Đây là quan hệ tới bảng order_items
    }

    // public function updateStatus($newStatus, $triggeredBy = null)
    // {
    //     $oldStatus = $this->status;
    //     $this->status = $newStatus;
    //     $this->save();

    //     // Gửi thông báo cho các vai trò
    //     $this->sendStatusNotifications($oldStatus, $newStatus, $triggeredBy);
    // }

    // protected function sendStatusNotifications($oldStatus, $newStatus, $triggeredBy)
    // {
    //     // Không gửi nếu trạng thái không thay đổi
    //     if ($oldStatus === $newStatus) return;

    //     $rolesToNotify = $this->getRolesToNotify($newStatus, $triggeredBy);

    //     foreach ($rolesToNotify as $role => $users) {
    //         foreach ($users as $user) {
    //             $user->notify(new OrderStatusNotification($this, $newStatus, $role));
    //         }
    //     }
    // }

    // protected function getRolesToNotify($newStatus, $triggeredBy)
    // {
    //     $roles = [];

    //     switch ($newStatus) {
    //         case 'pending':
    //             $roles['restaurant'] = [$this->restaurant->user];
    //             $roles['admin'] = User::where('role', 'admin')->get();
    //             break;
                
    //         case 'processing':
    //             $roles['restaurant'] = [$this->restaurant->user];
    //             $roles['shipper'] = User::where('role', 'shipper')->get();
    //             $roles['customer'] = [$this->customer];
    //             break;
                
    //         case 'delivering':
    //             $roles['customer'] = [$this->customer];
    //             $roles['restaurant'] = [$this->restaurant->user];
    //             $roles['shipper'] = [$this->shipper];
    //             break;
                
    //         case 'completed':
    //             $roles['customer'] = [$this->customer];
    //             $roles['restaurant'] = [$this->restaurant->user];
    //             $roles['shipper'] = [$this->shipper];
    //             $roles['admin'] = User::where('role', 'admin')->get();
    //             break;
                
    //         case 'canceled':
    //             $allRoles = ['customer', 'restaurant', 'shipper', 'admin'];
    //             foreach ($allRoles as $role) {
    //                 if ($role === $triggeredBy) continue; // Không gửi cho người hủy
                    
    //                 if ($role === 'customer' && $this->customer) {
    //                     $roles[$role] = [$this->customer];
    //                 } elseif ($role === 'restaurant' && $this->restaurant) {
    //                     $roles[$role] = [$this->restaurant->user];
    //                 } elseif ($role === 'shipper' && $this->shipper) {
    //                     $roles[$role] = [$this->shipper];
    //                 } elseif ($role === 'admin') {
    //                     $roles[$role] = User::where('role', 'admin')->get();
    //                 }
    //             }
    //             break;
    //     }

    //     return $roles;
    // }

}
