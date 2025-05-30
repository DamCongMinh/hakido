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

    <h1>🛵 Đơn hàng đang giao</h1>

    @if($orders->isEmpty())
        <p>Bạn chưa nhận đơn hàng nào.</p>
    @else
        @foreach($orders as $order)

            <div class="order-card">
                <h3>Đơn #{{ $order->id }}</h3>
                <p><strong>Khách:</strong> {{ $order->receiver_name }} - {{ $order->receiver_phone }}</p>
                <p><strong>Phí ship:</strong> {{ $order->shipping_fee }}₫</p>
                <p><strong>Địa chỉ giao:</strong> {{ $order->receiver_address }}</p>
                <p><strong>Tổng tiền:</strong> {{ number_format($order->actual_income) }}₫</p>
                <p><strong>Thanh toán:</strong> {{ strtoupper($order->payment_method) }}</p>
            
                <form method="POST" class="delivery-form" action="{{ route('shipper.orders.updateStatus', $order->id) }}">
                    @csrf
                    <label>Trạng thái:</label>
                    <select name="status" required>
                        <option>🛵 Đang giao hàng</option>
                        <option value="delivered">✅ Giao hàng thành công</option>
                        <option value="failed">❌ Giao thất bại</option>
                    </select>
            
                    <label>Ghi chú:</label>
                    <input class="note" type="text" name="note" placeholder="Nhập ghi chú (nếu có)">
            
                    <button type="submit">Cập nhật</button>
                </form>
            </div>
        
        @endforeach
    @endif

    @include('layout.footer')
</body>
</html>