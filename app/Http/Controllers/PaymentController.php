<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function bank(Order $order)
    {
        return view('payments.bank', compact('order'));
    }

    public function vnpay(Order $order)
    {
        return view('payments.vnpay', compact('order'));
    }

    public function vnpayReturn(Request $request)
    {
        return view('payments.vnpay_return');
    }
}
