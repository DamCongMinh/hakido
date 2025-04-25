<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = [
        'user_id', 'name', 'phone', 'avatar', 'date_of_birth', 'address'
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
}
