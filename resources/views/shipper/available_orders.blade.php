<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng chờ nhận</title>

    <link rel="stylesheet" href="{{ asset('css/Shipper/shipperOrder.css') }}">
</head>
<body>
    @include('layout.header')


    <h1>📋 Đơn hàng chờ nhận</h1>

    @if($orders->isEmpty())
        <p>Không có đơn hàng nào đang chờ shipper.</p>
    @else
        @foreach($orders as $order)
            <div class="order-box">
                <h3>Đơn hàng #{{ $order->id }}</h3>
                <p>Người nhận: {{ $order->receiver_name }} ({{ $order->receiver_phone }})</p>
                <p>Địa chỉ: {{ $order->receiver_address }}</p>
                <p>Phí ship: {{ $order->shipping_fee }}₫</p>
                <p>Tổng tiền: {{ number_format($order->actual_income) }}₫</p>

                <h4>Sản phẩm trong đơn:</h4>
                <table border="1" cellpadding="5">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Loại</th>
                            <th>Số lượng</th>
                            <th>Giá</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->product_type }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price) }}₫</td>
                                <td>{{ number_format($item->total_price) }}₫</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <form method="POST" action="{{ route('shipper.orders.accept', $order->id) }}">
                    @csrf
                    <button type="submit">Nhận đơn</button>
                </form>
            </div>
            <hr>
        @endforeach
    @endif

    @include('layout.footer')

</body>
</html>
