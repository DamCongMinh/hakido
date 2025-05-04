<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'phone', 'avatar', 'date_of_birth', 'address' ,'latitude' ,'longitude'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
