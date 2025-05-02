<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_type',
        'product_id',
        'product_name',
        'size_id',
        'size',
        'quantity',
        'price',
        'total_price',
        'options',
        'note',
    ];

    // Mối quan hệ với Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Option: nếu bạn muốn truy vấn sản phẩm
    public function product()
    {
        if ($this->product_type === 'food') {
            return $this->belongsTo(Food::class, 'product_id');
        } elseif ($this->product_type === 'beverage') {
            return $this->belongsTo(Beverage::class, 'product_id');
        }
        return null;
    }

    // Nếu cần quan hệ với size (nếu size là một bảng riêng, ví dụ beverage_sizes)
    public function sizeRelation()
    {
        return $this->belongsTo(BeverageSize::class, 'size_id');
    }
}

