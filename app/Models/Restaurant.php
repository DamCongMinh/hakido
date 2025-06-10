<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Restaurant extends Model
{
    use Notifiable;
    protected $casts = [
        'last_active_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id', 'name', 'phone', 'avatar', 'date_of_birth', 'address' , 'last_active_at','latitude' ,'longitude'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function foods()
    {
        return $this->hasMany(Food::class);
    }

    public function beverages()
    {
        return $this->hasMany(Beverage::class);
    }

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class, 'restaurant_id', 'id');
    }

    
}
