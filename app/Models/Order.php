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
        'voucher_id',
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
        return $this->hasMany(OrderItem::class);
    }


}
