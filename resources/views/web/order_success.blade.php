<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Đặt hàng thành công</title>
    <link rel="stylesheet" href="{{ asset('css/order_success.css') }}">
</head>
<body>
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="order-success">
        <h2>Đặt hàng thành công!</h2>

        <p>Cảm ơn bạn đã mua sắm tại cửa hàng của chúng tôi. Dưới đây là thông tin đơn hàng của bạn:</p>

        <h4>Mã đơn hàng: {{ $order->id }}</h4>

        <h4>Thông tin người nhận</h4>
        <ul>
            <li><strong>Họ tên:</strong> {{ $order->receiver_name }}</li>
            <li><strong>Số điện thoại:</strong> {{ $order->receiver_phone }}</li>
            <li><strong>Địa chỉ:</strong> {{ $order->receiver_address }}</li>
        </ul>

        <h4>Chi tiết sản phẩm</h4>
        <table>
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Tổng</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderItems as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price) }}₫</td>
                        <td>{{ number_format($item->total_price) }}₫</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p><strong>Tổng tiền:</strong> {{ number_format($order->total) }}₫</p>
        <p><strong>Phương thức thanh toán:</strong> {{ ucfirst($order->payment_method) }}</p>

        <a href="{{ route('home') }}">Trở về trang chủ</a>
    </div>
</body>
</html>