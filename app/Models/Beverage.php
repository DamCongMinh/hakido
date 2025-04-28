<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beverage extends Model
{
    protected $table = 'beverages';

    protected $fillable = ['name', 'image', 'category_id', 'description', 'status', 'restaurant_id', 'is_active'];

    // protected $casts = [
    //     'is_active' => 'boolean',
    // ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function beverageSizes()
    {
        return $this->hasMany(BeverageSize::class, 'beverage_id');
    }

    // Thêm accessor để lấy giá nhỏ nhất từ beverage_sizes
    public function getMinPriceAttribute()
    {
        $size = $this->beverageSizes->sortBy('old_price')->first();
        if ($size) {
            $discount = $size->discount_percent ?? 0;
            $discounted_price = $size->old_price * (1 - $discount / 100);
            return round($discounted_price, 2);
        }
        return null;
    }
}
