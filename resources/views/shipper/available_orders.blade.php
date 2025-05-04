<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Đơn hàng chờ nhận</title>
</head>
<body>
    <h1>📋 Đơn hàng chờ nhận</h1>

    @if($orders->isEmpty())
        <p>Không có đơn hàng nào đang chờ shipper.</p>
    @else
        <ul>
            @foreach($orders as $order)
                <li>
                    <strong>Đơn #{{ $order->id }}</strong><br>
                    Người nhận: {{ $order->receiver_name }} ({{ $order->receiver_phone }})<br>
                    Địa chỉ: {{ $order->receiver_address }}<br>
                    Tổng tiền: {{ number_format($order->total) }}₫<br>
                    <form method="POST" action="{{ route('shipper.orders.accept', $order->id) }}">
                        @csrf
                        <button type="submit">Nhận đơn</button>
                    </form>
                </li>
                <hr>
            @endforeach
        </ul>
    @endif
</body>
</html>