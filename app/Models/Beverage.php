<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beverage extends Model
{
    protected $table = 'beverages';

    protected $fillable = ['name', 'image', 'price', 'description', 'status', 'restaurant_id'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
}
