<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Đơn hàng</title>
    <link rel="stylesheet" href="{{ asset('css/Admin/orders/admin_order.css') }}">
</head>
<body>
    @include('layout.header')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <h2>Danh sách Đơn hàng</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Mã đơn</th>
                <th>Khách hàng</th>
                <th>Nhà hàng</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Shipper</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->customer->name }}</td>
                    <td>{{ $order->restaurant->name }}</td>
                    <td>{{ number_format($order->total, 0, ',', '.') }}đ</td>
                    <td>{{ $order->status }}</td>
                    <td>
                        {{ $order->shipper ? $order->shipper->name : 'Chưa có' }}
                    </td>
                    <td>
                        {{-- Gán shipper --}}
                        @if(!$order->shipper)
                            <form action="{{ route('admin.orders.assignShipper', $order->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <select name="shipper_id" required>
                                    @foreach($shippers as $shipper)
                                        <option value="{{ $shipper->id }}">{{ $shipper->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit">Gán shipper</button>
                            </form>
                        @endif

                        {{-- Cập nhật trạng thái --}}
                        @if(in_array($order->status, ['chờ xác nhận', 'đang xử lý']))
                            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <select name="status" required>
                                    <option value="đang giao">Đang giao</option>
                                    <option value="hoàn thành">Hoàn thành</option>
                                </select>
                                <button type="submit">Cập nhật</button>
                            </form>
                        @endif

                        {{-- Hủy đơn --}}
                        @if($order->status !== 'đã hủy' && $order->status !== 'hoàn thành')
                            <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn này?')">
                                @csrf
                                <button type="submit">Hủy đơn</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a class="back" href="{{ route('admin.dashboard') }}">← Quay lại trang Admin</a>
</body>
</html>
