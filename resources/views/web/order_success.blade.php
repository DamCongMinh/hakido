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
    @include('layout.header')

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="order-success">
        <h2>Đặt hàng thành công!</h2>
        <p>Cảm ơn bạn đã đặt hàng. Dưới đây là các đơn hàng của bạn:</p>
        {{-- <pre>{{ print_r($orders, true) }}</pre> --}}
        @foreach ($orders as $order)
            <section class="restaurant-order">
                <h4>Đơn hàng #{{ $order->id }} từ nhà hàng {{ $order->restaurantProfile->name ?? 'Không rõ' }}</h4>

                </h3>
                <p><strong>Đơn hàng:</strong> #{{ $order->id }}</p>

                <ul>
                    <li><strong>Người nhận:</strong> {{ $order->receiver_name }}</li>
                    <li><strong>Địa chỉ:</strong> {{ $order->receiver_address }}</li>
                </ul>

                <table>
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Giá</th>
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

                <p><strong>Phí ship:</strong> {{ number_format($order->shipping_fee) }}₫</p>
                <p><strong>Tổng tiền:</strong> {{ number_format($order->total) }}₫</p>
                <hr>
            </section>
        @endforeach


        <br>
        <a href="{{ route('home') }}">Trở về trang chủ</a>
    </div>

    @include('layout.footer')
</body>
</html>