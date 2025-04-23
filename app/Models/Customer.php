<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name_customer', 'email', 'password', 'avata', 'phone', 'date_of_birth', 'address'];

    // Quan hệ ngược với User
    public function user()
    {
        return $this->hasOne(User::class, 'customer_id', 'id');
    }

    public function getNameAttribute()
    {
        return $this->name_customer;
    }

    public function getAvatarAttribute()
    {
        return $this->avata;
    }


}
