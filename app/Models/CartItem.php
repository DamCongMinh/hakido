<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{

    protected $fillable = [
        'product_id',
        'product_type',
        'size',
        'quantity',
        'unit_price',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class, 'product_id');
    }

    public function beverage()
    {
        return $this->belongsTo(Beverage::class, 'product_id');
    }


}
