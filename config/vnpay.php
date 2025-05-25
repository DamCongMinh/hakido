<?php

return [
    'tmn_code' => env('VNPAY_TMN_CODE'),
    'hash_secret' => env('VNPAY_HASH_SECRET'),
    'payment_url' => 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
    'return_url' => env('VNPAY_RETURN_URL'),
    'inp_url' => env('VNPAY_IPN_UR')
];

