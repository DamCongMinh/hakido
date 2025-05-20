<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VnpayController extends Controller
{
    public function createPayment(Request $request)
    {
        // Lấy thông tin thanh toán từ session
        $checkoutData = session('checkout_data');

        if (!$checkoutData) {
            return redirect()->route('cart.show')->with('error', 'Phiên thanh toán đã hết hạn.');
        }

        $finalTotal = $checkoutData['finalTotal'];

        // Thông tin cấu hình VNPAY
        $vnp_Url = env('VNPAY_PAYMENT_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $vnp_Returnurl = env('VNPAY_RETURN_URL');
        $vnp_TmnCode = env('VNPAY_TMN_CODE');
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');

        // Thông tin giao dịch
        $vnp_TxnRef = uniqid();
        $vnp_OrderInfo = "Thanh toán đơn hàng";
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $finalTotal * 100;
        $vnp_Locale = "vn";
        $vnp_IpAddr = $request->ip();

        $inputData = [
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
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        // Sắp xếp theo thứ tự A-Z key
        ksort($inputData);

        // Tạo chuỗi dữ liệu để hash (KHÔNG dùng http_build_query)
        $hashData = '';
        foreach ($inputData as $key => $value) {
            $hashData .= $key . "=" . $value . "&";
        }
        $hashData = rtrim($hashData, '&');

        // Tạo chữ ký SHA512
        $vnp_SecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $inputData['vnp_SecureHash'] = $vnp_SecureHash;

        // Tạo URL chuyển hướng
        $vnp_Url .= '?' . http_build_query($inputData);

        return redirect($vnp_Url);
    }

    public function handleReturn(Request $request)
    {
        \Log::info('VNPAY RETURN CALLED', $request->all());
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $inputData = $request->all();

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? null;
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        // Sắp xếp các tham số theo thứ tự alphabet
        ksort($inputData);

        // Tạo chuỗi hash để kiểm tra chữ ký
        $hashData = '';
        foreach ($inputData as $key => $value) {
            $hashData .= $key . "=" . $value . "&";
        }
        $hashData = rtrim($hashData, '&');

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        dd([
            'hashData' => $hashData,
            'secureHash (calculated)' => $secureHash,
            'vnp_SecureHash (from URL)' => $vnp_SecureHash,
        ]);

        if ($secureHash === $vnp_SecureHash) {
            if ($inputData['vnp_ResponseCode'] == '00') {
                // Giao dịch thành công
                return view('vnpay.success', ['data' => $inputData]);
            } else {
                // Giao dịch thất bại
                return view('vnpay.failed', ['data' => $inputData]);
            }
        } else {
            // Sai chữ ký xác thực
            return response("Sai chữ ký xác thực!", 400);
        }
        
    }

    public function handleIpn(Request $request)
    {
        \Log::info('VNPAY IPN RETURN CALLED', $request->all());

        // Xử lý y hệt như handleReturn nhưng trả JSON 200 OK
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $inputData = $request->all();

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? null;
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);
        ksort($inputData);

        $hashData = '';
        foreach ($inputData as $key => $value) {
            $hashData .= $key . "=" . $value . "&";
        }
        $hashData = rtrim($hashData, '&');

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash === $vnp_SecureHash) {
            if ($inputData['vnp_ResponseCode'] == '00') {
                // Thành công → xử lý đơn hàng tại đây
                return response('OK', 200);
            } else {
                // Thất bại → có thể xử lý log đơn hàng
                return response('FAILED', 200);
            }
        } else {
            return response('INVALID CHECKSUM', 400);
        }
    }

}
