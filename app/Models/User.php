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
        'google_id',
        'avatar',
        'role',
        'is_active',
        'is_approved',
        'password',
    ];
    

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

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

    public function getProfileInfo()
    {
        $profile = match ($this->role) {
            'customer' => $this->customer,
            'restaurant' => $this->restaurant,
            'shipper' => $this->shipper,
            default => null,
        };

        if ($profile) {
            // Gộp thêm các thông tin từ bảng users
            foreach (['email', 'name', 'password'] as $field) {
                $profile->$field = $this->$field;
            }
        }

        return $profile;
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function getResolvedAvatarAttribute()
    {
        return match ($this->role) {
            'customer' => $this->customer->avatar ?? asset('img/shiper_avt.jpg'),
            'shipper' => $this->shipper->avatar ?? asset('img/shiper_avt.jpg'),
            'restaurant' => $this->restaurant->avatar ?? asset('img/shiper_avt.jpg'),
            default => $this->avatar ?? asset('img/shiper_avt.jpg'),
        };
    }



}
