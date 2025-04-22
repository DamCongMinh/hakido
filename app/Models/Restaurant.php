<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = ['user_id', 'restaurant_name', 'address', 'phone', 'description', 'logo'];

    public function foods()
    {
        return $this->hasMany(Food::class);
    }

    public function beverages()
    {
        return $this->hasMany(Beverage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
