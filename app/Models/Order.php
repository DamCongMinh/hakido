<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'restaurant_id',
        'shipper_id',
        'status',
        'total',
        'receiver_name',
        'receiver_phone',
        'receiver_address',
        'payment_method',
        'note',
    ];
    // Mối quan hệ với khách hàng (customer)
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Mối quan hệ với nhà hàng (restaurant)
    public function restaurant()
    {
        return $this->belongsTo(User::class, 'restaurant_id');
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

}
