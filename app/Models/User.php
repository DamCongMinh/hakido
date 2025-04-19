<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //  Quan hệ với bảng phụ theo role
    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function restaurant()
    {
        return $this->hasOne(Restaurant::class);
    }

    public function shipper()
    {
        return $this->hasOne(Shipper::class);
    }

    // Gợi ý thêm nếu có đơn hàng (nếu user là customer)
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
}
