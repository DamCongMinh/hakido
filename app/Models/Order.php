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
    
    
    // Mối quan hệ với khách hàng (customer)
    // public function customer()
    // {
    //     return $this->belongsTo(User::class, 'customer_id');
    // }

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class, 'customer_id');
    }


    // Mối quan hệ với nhà hàng (restaurant)
    public function restaurant()
    {
        return $this->belongsTo(User::class, 'restaurant_id');
    }

    public function restaurantProfile()
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

}
