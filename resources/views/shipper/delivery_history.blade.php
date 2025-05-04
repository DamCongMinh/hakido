<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lịch sử giao hàng</title>
</head>
<body>
    <h1>📜 Lịch sử giao hàng</h1>

    @if($orders->isEmpty())
        <p>Chưa có đơn hàng nào trong lịch sử.</p>
    @else
        <table border="1" cellpadding="5">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Thời gian</th>
                    <th>Trạng thái</th>
                    <th>Tổng tiền</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->receiver_name }}</td>
                        <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($order->status == 'delivered')
                                ✅ Đã giao
                            @elseif($order->status == 'failed')
                                ❌ Thất bại
                            @endif
                        </td>
                        <td>{{ number_format($order->total) }}₫</td>
                        <td>{{ $order->note }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>