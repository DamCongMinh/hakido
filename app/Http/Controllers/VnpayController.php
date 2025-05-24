<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VnpayController extends Controller
{
    public function createPayment(Request $request)
    {
        $vnp_TmnCode = config('vnpay.tmn_code');
        $vnp_HashSecret = config('vnpay.hash_secret');
        $vnp_Url = config('vnpay.payment_url');
        $vnp_Returnurl = config('vnpay.return_url');
        $vnp_TxnRef = time();
        $vnp_OrderInfo = "Thanh toan don hang";
        $vnp_Amount = 338108 * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = request()->header('CF-Connecting-IP') ?? request()->ip();
        
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
            "vnp_OrderType" => "billpayment",
            "vnp_Returnurl" => $vnp_Returnurl, // ✅ Bổ sung
            "vnp_TxnRef" => $vnp_TxnRef,
            
        );
    
        // Tạo chuỗi hash và query đúng chuẩn
        ksort($inputData);
        $hashdataArr = [];
        $queryArr = [];
    
        foreach ($inputData as $key => $value) {
            $hashdataArr[] = urlencode($key) . "=" . urlencode($value);
            $queryArr[] = urlencode($key) . "=" . urlencode($value);
        }
    
        $hashdata = implode('&', $hashdataArr);
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $queryArr[] = 'vnp_SecureHashType=SHA512';
        $queryArr[] = 'vnp_SecureHash=' . $vnpSecureHash;
        $vnp_Url = $vnp_Url . '?' . implode('&', $queryArr);
        Log::info("IP Address: " . $vnp_IpAddr);
        Log::info("Return URL: " . $vnp_Returnurl);
        Log::info("TxnRef: " . $vnp_TxnRef);
        Log::info("Amount: " . $vnp_Amount);

        // Log hash data
        Log::info("Hash Data: " . $hashdata);

        // Log secure hash
        Log::info("Generated Secure Hash: " . $vnpSecureHash);

        // Log full redirect URL
        Log::info("Redirect URL: " . $vnp_Url);

    
        return redirect($vnp_Url);
    }
    


    public function vnpayReturn(Request $request)
    {
        Log::info('🟢 Đã vào hàm vnpayReturn');
        
        $inputData = $request->all();
    
        // Kiểm tra bắt buộc tham số
        if (!isset($inputData['vnp_SecureHash'])) {
            Log::warning('⚠️ vnp_SecureHash is missing in return URL');
            return view('payments.vnpayfailed');
        }
    
        $vnp_HashSecret = "57ON81SV4TSHSLDQE3Z225GZVWPGKHMK"; // hash secret từ VNPAY
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
    
        // Bỏ các key không cần hash
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);
    
        // Sắp xếp và tạo chuỗi hash
        ksort($inputData);
        $hashData = '';
        foreach ($inputData as $key => $value) {
            $hashData .= $key . '=' . $value . '&';
        }
        $hashData = rtrim($hashData, '&');
    
        $secureHash = hash_hmac('SHA512', $hashData, $vnp_HashSecret);
    
        Log::info('✅ VNPAY return raw data:', $inputData);
        Log::info('✅ VNPAY return hashData:', ['hashData' => $hashData]);
        Log::info('✅ Generated secureHash:', ['secureHash' => $secureHash]);
        Log::info('✅ Received secureHash:', ['vnp_SecureHash' => $vnp_SecureHash]);
    
        if ($secureHash === $vnp_SecureHash) {
            Log::info('✅ Checksum hợp lệ');
    
            if ($request->vnp_ResponseCode == '00') {
                Log::info('🎉 Thanh toán thành công', ['order' => $request->vnp_TxnRef]);
                // TODO: Cập nhật trạng thái đơn hàng trong DB tại đây
                return view('payments.vnpaysuccess');
            } else {
                Log::warning('❌ Thanh toán thất bại', ['code' => $request->vnp_ResponseCode]);
                return view('payments.vnpayfailed');
            }
        } else {
            Log::error('❌ Checksum không hợp lệ');
            return view('payments.vnpayfailed');
        }
    }
    


    public function handleIpn(Request $request)
    {
        \Log::info('VNPAY IPN RETURN CALLED', $request->all());

        $vnp_HashSecret = config('vnpay.hash_secret');
        $inputData = $request->all();

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? null;
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        ksort($inputData);
        $hashData = urldecode(http_build_query($inputData));
        $secureHash = hash_hmac('SHA512', $hashData, $vnp_HashSecret);

        if ($secureHash === $vnp_SecureHash) {
            if ($inputData['vnp_ResponseCode'] == '00') {
                // TODO: Cập nhật trạng thái đơn hàng ở đây
                return response('OK', 200);
            } else {
                return response('FAILED', 200);
            }
        } else {
            return response('INVALID CHECKSUM', 400);
        }
    }
}
