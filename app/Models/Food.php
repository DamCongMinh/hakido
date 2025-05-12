<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    protected $table = 'foods';

    protected $fillable = [
        'restaurant_id',
        'category_id',
        'name',
        'description',
        'old_price',
        'discount_percent',
        'quantity',
        'image',
        'status',
        'is_approved',
        'is_active',
        'is_rejected',
        'min_price', 
        'max_price',
        'rejection_reason',
    ];

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

    // Accessor để tự động có new_price
    public function getNewPriceAttribute()
    {
        $discount = $this->discount_percent ?? 0;
        return round($this->old_price * (1 - $discount / 100), 2);
    }

    protected $appends = ['type'];

    public function getTypeAttribute()
    {
        return 'food';
    }
}
