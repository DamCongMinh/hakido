<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Đơn hàng đang giao</title>

    <link rel="stylesheet" href="{{ asset('css/Shipper/current_delivery.css') }}">

</head>
<body>
    @include('layout.header')

    <h1>🚚 Đơn hàng đang giao</h1>

    @if($orders->isEmpty())
        <p>Bạn chưa nhận đơn hàng nào.</p>
    @else
        @foreach($orders as $order)

            <div class="container" style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
                <p><strong>Đơn #{{ $order->id }}</strong></p>
                <p>Khách: {{ $order->receiver_name }} - {{ $order->receiver_phone }}</p>
                <p>Phí ship: {{ $order->shipping_fee }}₫</p>
                <p>Địa chỉ giao: {{ $order->receiver_address }}</p>
                <p>Tổng tiền: {{ number_format($order->actual_income) }}₫</p>
                <p>Thanh toán: {{ strtoupper($order->payment_method) }}</p>

                <form method="POST" id="current_delivery-form" action="{{ route('shipper.orders.updateStatus', $order->id) }}">
                    @csrf
                    <label>Trạng thái:</label>
                    <select name="status" required>
                        <option value="delivered">✅ Đã giao</option>
                        <option value="failed">❌ Giao thất bại</option>
                    </select><br>
                    <label>Ghi chú:</label>
                    <input class="note" type="text" name="note" placeholder="Nhập ghi chú (nếu có)">
                    <br>
                    <button type="submit">Cập nhật</button>
                </form>
            </div>
        @endforeach
    @endif

    @include('layout.footer')
</body>
</html>