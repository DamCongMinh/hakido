<!-- resources/views/web/vnpay.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Thanh toán VNPAY</title>
</head>
<body>
    <h2>Thanh toán qua VNPAY</h2>

    @php
        $checkoutData = session('checkout_data');
        $amount = $checkoutData['finalTotal'] ?? 0;
    @endphp

    <p>Tổng tiền cần thanh toán: <strong>{{ number_format($amount, 0, ',', '.') }} VNĐ</strong></p>

    <form action="{{ route('vnpay.payment') }}" method="GET">
        @csrf
        <input type="hidden" name="amount" value="{{ $amount }}">
        <button type="submit">Thanh toán qua VNPAY</button>
    </form>
</body>
</html>
