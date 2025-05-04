<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bank</title>
</head>
<body>
    <h2>Chuyển khoản ngân hàng</h2>
    <p>Vui lòng chuyển tiền theo thông tin sau:</p>

    <ul>
        <li>Ngân hàng: Vietcombank</li>
        <li>Số tài khoản: 0123456789</li>
        <li>Chủ tài khoản: Nguyễn Văn A</li>
        <li>Nội dung chuyển khoản: <strong>Thanh toan don hang #{{ $order->id }}</strong></li>
        <li>Số tiền: <strong>{{ number_format($order->total_amount) }}₫</strong></li>
    </ul>

    <a href="/">Quay lại trang chủ</a>

</body>
</html>