<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeverageReview extends Model
{
    protected $fillable = ['customer_id', 'beverage_id', 'order_id', 'rating', 'comment'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function beverage()
    {
        return $this->belongsTo(Beverage::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
