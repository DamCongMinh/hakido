<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = [
        'user_id', 'name_restaurant', 'email', 'password', 'phone',
        'avata', 'address', 'time_open', 'time_close', 'is_approved', 'is_active'
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

    public function getNameAttribute()
    {
        return $this->name_restaurant;
    }

    public function getAvatarAttribute()
    {
        return $this->avata;
    }

}
