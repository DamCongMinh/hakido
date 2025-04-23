<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipper extends Model
{
    use HasFactory;

    protected $fillable = ['name_shipper', 'email', 'password', 'avata', 'area', 'phone'];

    public function user()
    {
        return $this->hasOne(User::class, 'shipper_id', 'id');
    }

    public function getNameAttribute()
    {
        return $this->name_shipper;
    }

    public function getAvatarAttribute()
    {
        return $this->avata;
    }

}
