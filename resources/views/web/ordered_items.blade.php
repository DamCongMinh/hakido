<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sản phẩm đã đặt</title>
    <link rel="stylesheet" href="{{ asset('css/order_items.css') }}">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Sản phẩm đã đặt</h2>

    @if ($items->isEmpty())
        <p>Bạn chưa đặt sản phẩm nào.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#Đơn hàng</th>
                    <th>Sản phẩm</th>
                    <th>Loại</th>
                    <th>Số lượng</th>
                    <th>Giá</th>
                    <th>Thành tiền</th>
                    <th>Nhà hàng</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->order->id }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->product_type }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price) }}₫</td>
                        <td>{{ number_format($item->total_price) }}₫</td>
                        <td>{{ $item->order->restaurant->name ?? 'Không rõ' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <a href="{{ route('home') }}" class="btn btn-primary mt-3">Quay về trang chủ</a>
</div>
</body>
</html>
