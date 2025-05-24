<?php

return [
    'tmn_code' => env('VNPAY_TMN_CODE', 'demo'),
    'hash_secret' => env('VNPAY_HASH_SECRET', ''),
    'return_url' => env('VNPAY_RETURN_URL', 'https://mile-addition-spas-specifications.trycloudflare.com'),
    'payment_url' => env('VNPAY_PAYMENT_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
];
