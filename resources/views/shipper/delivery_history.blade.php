<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lịch sử giao hàng</title>

    <link rel="stylesheet" href="{{ asset('css/Shipper/delivery_history.css') }}">
</head>
<body>
    @include('layout.header')
    
    <h1>📜 Lịch sử giao hàng</h1>

    <h2>✅ Đơn hàng giao thành công</h2>
    @if($successfulOrders->isEmpty())
        <p class="text">Không có đơn hàng thành công nào.</p>
    @else
        <table border="1" cellpadding="5">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Thời gian</th>
                    <th>Tổng tiền</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                @foreach($successfulOrders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->receiver_name }}</td>
                        <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                        <td>{{ number_format($order->total) }}₫</td>
                        <td>{{ $order->note }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2 style="margin-top:40px;">❌ Đơn hàng giao thất bại</h2>
    @if($failedOrders->isEmpty())
        <p class="text">Không có đơn hàng thất bại nào.</p>
    @else
        <table border="1" cellpadding="5">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Thời gian</th>
                    <th>Tổng tiền</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                @foreach($failedOrders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->receiver_name }}</td>
                        <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                        <td>{{ number_format($order->total) }}₫</td>
                        <td>{{ $order->note }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
