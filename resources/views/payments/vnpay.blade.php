<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>VNPay</title>
</head>
<body>
    <h2>VNPAY (Demo)</h2>
    <p>Đây là trang mô phỏng thanh toán qua VNPAY.</p>

    <p>Mã đơn hàng: #{{ $order->id }}</p>
    <p>Số tiền: <strong>{{ number_format($order->total_amount) }}₫</strong></p>

    <form action="{{ route('order.success', ['id' => $order->id]) }}" method="GET">
        <button type="submit">Giả lập thanh toán thành công</button>
    </form>

</body>
</html>