<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sản phẩm đã đặt</title>
    <link rel="stylesheet" href="{{ asset('css/order_items.css') }}">
</head>
<body>
    @include('layout.header')

    <div class="container mt-5">
        <h2 class="mb-4">Sản phẩm đã đặt</h2>

        @if ($orders->isEmpty())
            <p>Bạn chưa đặt sản phẩm nào.</p>
        @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#Đơn hàng</th>
                    <th>Nhà hàng</th>
                    <th>Sản phẩm</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->restaurantProfile->name ?? 'Không rõ' }}</td>
                        <td>
                            <ul>
                                @foreach ($order->orderItems as $item)
                                <li>
                                    <span><strong>+ Tên:</strong> {{ $item->product_name }} ({{ $item->product_type }})</span>&nbsp;
                                    <span><strong>+ SL:</strong> {{ $item->quantity }}</span>&nbsp;
                                    <span><strong>+ Giá:</strong> {{ number_format($item->price) }}₫</span>&nbsp;
                                    <span><strong>+ Thành tiền:</strong> {{ number_format($item->total_price) }}₫</span>
                                </li>                                
                                
                                @endforeach
                            </ul>
                            <strong id="sum">Tổng: {{ number_format($order->total) }}₫ (Phí ship: {{ number_format($order->shipping_fee) }}₫)</strong>
                        </td>
                        <td>{{ \App\Models\Order::statusMapping()[$order->status] ?? ucfirst($order->status) }}</td>
                        <td>
                            @if ($order->status === 'pending')
                                <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn này không?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm">Hủy đơn</button>
                                </form>
                            @else
                                
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        @endif

        <a href="{{ route('home') }}" class="btn btn-primary mt-3">Quay về trang chủ</a>
    </div>

    @include('layout.footer')
</body>
</html>
