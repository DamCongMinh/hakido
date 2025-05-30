<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingPayment extends Model
{
    protected $fillable = [
        'txn_ref',
        'customer_id',
        'checkout_data',
        'cart_data',
    ];

    protected $casts = [
        'checkout_data' => 'array',
        'cart_data' => 'array',
    ];
}
