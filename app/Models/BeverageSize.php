<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeverageSize extends Model
{
    protected $table = 'beverage_sizes';

    protected $fillable = [
        'beverage_id', 
        'size', 
        'old_price', 
        'discount_percent',
        'quantity',
    ];

    public function beverage()
    {
        return $this->belongsTo(Beverage::class, 'beverage_id');
    }

    public function getNewPriceAttribute()
    {
        $discount = $this->discount_percent ?? 0;
        return round($this->old_price * (1 - $discount / 100), 2);
    }
}
