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

    @php
        $statusMapping = \App\Models\Order::statusMapping();
    @endphp


    <h2>Danh sách Đơn hàng</h2>
    <form method="GET" action="{{ route('admin.orders.index') }}" class="order-filter-form">
        <label for="filter_type">Lọc theo:</label>
        <select name="filter_type" id="filter_type">
            <option value="">-- Chọn --</option>
            <option value="day" {{ request('filter_type') == 'day' ? 'selected' : '' }}>Hôm nay</option>
            <option value="month" {{ request('filter_type') == 'month' ? 'selected' : '' }}>Tháng này</option>
            <option value="year" {{ request('filter_type') == 'year' ? 'selected' : '' }}>Năm nay</option>
        </select>
    
        <label for="date">Hoặc chọn ngày:</label>
        <input type="date" name="date" id="date" value="{{ request('date') }}">
    
        <button class="btn" type="submit">Lọc</button>
        <a class="btn-danger" href="{{ route('admin.orders.index') }}">Xóa lọc</a>
    </form>
    

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
                    <td>
                        {{ $order->receiver_name ?? ($order->user->name ?? 'Không có tên khách') }}
                    </td>                    
                    <td>
                        {{ $order->restaurantProfile->name ?? ($order->restaurant->name ?? 'Không có tên nhà hàng') }}
                    </td>                    
                    <td>{{ number_format($order->total, 0, ',', '.') }}đ</td>
                    <td>{{ $statusMapping[$order->status] ?? $order->status }}</td>
                    <td>
                        {{ $order->shipper ? $order->shipper->name : 'Chưa có' }}
                    </td>
                    <td>

                        {{-- Cập nhật trạng thái --}}
                        @if(in_array($order->status, ['chờ xác nhận', 'đang xử lý']))
                            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <select name="status" required>
                                    <option value="delivering">Đang giao</option>
                                    <option value="completed">Hoàn thành</option>
                                </select>                                
                                <button class="btn" type="submit">Cập nhật</button>
                            </form>
                        @endif

                        {{-- Hủy đơn --}}
                        @if($order->status !== 'đã hủy' && $order->status !== 'hoàn thành')
                            <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn này?')">
                                @csrf
                                <button class="btn-danger" type="submit">Hủy đơn</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @include('layout.footer')
</body>
</html>
