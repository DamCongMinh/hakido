<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Media;

class FoodReview extends Model
{
    protected $fillable = ['customer_id', 'food_id', 'order_id', 'rating', 'comment'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
