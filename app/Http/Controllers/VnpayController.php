<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PendingPayment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class VnpayController extends Controller
{
    
    public function PaymentVnpay() {
    
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_IpnUrl = config('vnpay.inp_url');
        $vnp_Returnurl = config('vnpay.return_url');
        $vnp_TmnCode = config('vnpay.tmn_code');
        $vnp_HashSecret = config('vnpay.hash_secret');

        $checkoutData = session('checkout_data');
        $finalTotal = $checkoutData['finalTotal'] ?? 0;
        
        $vnp_TxnRef = date('YmdHis'); 
        PendingPayment::create([
            'txn_ref' => $vnp_TxnRef,
            'customer_id' => auth()->user()?->customer?->id,
            'checkout_data' => $checkoutData,
            'cart_data' => $checkoutData['groupedItems'] ?? [],
        ]);
        $vnp_OrderInfo = 'thanh toan don hang';
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $finalTotal * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'NCB';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        
        );
        
        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }
        
        var_dump($inputData);
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        
        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        $returnData = array('code' => '00'
            , 'message' => 'success'
            , 'data' => $vnp_Url);
            if (isset($_POST['redirect'])) {
                header('Location: ' . $vnp_Url);
                die();
            } else {
                echo json_encode($returnData);
            }
            
        return redirect($vnp_Url);
    }


    public function vnpay_ipn(Request $request)
    {
        Log::info(' Đã vào vnpay_ipn');
    
        $vnp_HashSecret = 'SEA84YCMTR5FYNHWADFYENQLBLRO2HNY'; 
        $inputData = [];
    
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
    
        if (!isset($inputData['vnp_SecureHash'])) {
            Log::error(' Thiếu vnp_SecureHash trong dữ liệu IPN', $inputData);
            return response('{"RspCode":"97","Message":"Missing vnp_SecureHash"}', 200);
        }
    
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);
    
        ksort($inputData);
        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
    
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
    
        if ($secureHash === $vnp_SecureHash) {
            if ($inputData['vnp_ResponseCode'] === '00') {
                Log::info(' Xác thực thành công, giao dịch thành công', $inputData);
     
                return response('{"RspCode":"00","Message":"Confirm Success"}', 200);
            } else {
                Log::warning(' Giao dịch không thành công', $inputData);
                return response('{"RspCode":"01","Message":"Transaction Failed"}', 200);
            }
        } else {
            Log::error(' Chữ ký không hợp lệ', [
                'expected' => $vnp_SecureHash,
                'calculated' => $secureHash,
                'data' => $inputData
            ]);
            return response('{"RspCode":"97","Message":"Invalid Signature"}', 200);
        }
    }

    // 
    
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = "SEA84YCMTR5FYNHWADFYENQLBLRO2HNY";

        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';

        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        ksort($inputData);
        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Kiểm tra tính hợp lệ của phản hồi từ VNPAY
        if ($secureHash === $vnp_SecureHash) {
            $vnp_ResponseCode = $request->input('vnp_ResponseCode');
            $vnp_TransactionStatus = $request->input('vnp_TransactionStatus');

            if ($vnp_ResponseCode === '00' && $vnp_TransactionStatus === '00') {
                //Thành công
                $txnRef = $request->input('vnp_TxnRef');

                //Lấy dữ liệu từ pending_payments
                $pending = \App\Models\PendingPayment::where('txn_ref', $txnRef)->first();

                if ($pending) {
                    $checkoutData = $pending->checkout_data;
                    $cartData = $pending->cart_data;

                    //Tạo đơn hàng mới
                    $order = \App\Models\Order::create([
                        'customer_id' => $pending->customer_id,
                        // 'vnp_TxnRef' => $txnRef,
                        'restaurant_id' => $checkoutData['restaurant_id'] ?? null,
                        'status' => 'pending',
                        'total' => $checkoutData['finalTotal'] ?? 0,
                        'shipping_fee' => $checkoutData['restaurantShippingFees'][$checkoutData['restaurant_id']] ?? 0,
                        'receiver_name' => $checkoutData['receiver_name'] ?? '',
                        'receiver_phone' => $checkoutData['receiver_phone'] ?? '',
                        'receiver_address' => $checkoutData['receiver_address'] ?? '',
                        'payment_method' => 'vnpay',
                        'note' => $pending->note ?? '',
                    ]);
                    

                    //Tạo các order item từ cart_data
                    foreach ($cartData as $restaurantId => $items) {
                        foreach ($items as $item) {
                            \App\Models\OrderItem::create([
                                'order_id' => $order->id,
                                'product_type' => $item['product_type'],
                                'order_img' => $item['image'] ?? null,
                                'product_id' => $item['product_id'],
                                'product_name' => $item['name'],
                                'size_id' => $item['size_id'] ?? null,
                                'size' => $item['size'] ?? null,
                                'quantity' => $item['quantity'],
                                'price' => $item['price'],
                                'total_price' => $item['price'] * $item['quantity'],
                                'options' => $item['options'] ?? null,
                            ]);
                        }
                    }

                    
                    Payment::create([
                        'customer_id' => $pending->customer_id,
                        'order_id' => $order->id,
                        'txn_ref' => $txnRef,
                        'amount' => ($request->input('vnp_Amount') ?? 0) / 100,
                        'bank_code' => $request->input('vnp_BankCode'),
                        'status' => 'success',
                        'raw_data' => json_encode($request->all()),
                    ]);

                    $customer = \App\Models\Customer::find($pending->customer_id);
                    $user = $customer?->user;

                    if ($user && $user->cart) {
                        foreach ($cartData as $restaurantId => $items) {
                            foreach ($items as $item) {
                                $query = $user->cart->items()
                                    ->where('product_id', $item['product_id'])
                                    ->where('product_type', $item['product_type']);

                                if ($item['product_type'] === 'beverage') {
                                    $query->where('size', $item['size'] ?? null);
                                }

                                $query->delete();
                            }
                        }
                    }


                    // Xóa pending_payment sau khi lưu thành công
                    $pending->delete();
                }
                return view('payments.vnpaysuccess', ['data' => $request->all()]);
            } elseif ($vnp_ResponseCode === '24') {
                // Người dùng hủy
                return view('payments.vnpaycancel', ['data' => $request->all()]);
            } else {
                // Thất bại
                return view('payments.vnpayfailed', ['data' => $request->all()]);
            }
        } else {
            // Chữ ký không hợp lệ
            return view('payments.vnpayfailed', ['error' => 'Invalid signature']);
        }
    }



    public function handleIpn(Request $request)
    {
        Log::info(' IPN từ VNPAY', $request->all());

        $vnp_HashSecret = config('vnpay.hash_secret');
        $inputData = $request->all();

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? null;
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        ksort($inputData);
        $hashData = urldecode(http_build_query($inputData));
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash === $vnp_SecureHash) {
            if ($inputData['vnp_ResponseCode'] == '00') {
                // TODO: cập nhật đơn hàng
                return response('OK', 200);
            } else {
                return response('FAILED', 200);
            }
        } else {
            return response('INVALID CHECKSUM', 400);
        }
    }
}
