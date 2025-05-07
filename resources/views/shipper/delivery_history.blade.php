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

    <!-- Form lọc -->
    <form method="GET" action="{{ route('shipper.orders.history') }}" class="filter-form">
        <label for="filter">Hiển thị theo:</label>
        <select name="filter" id="filter" onchange="handleFilterChange()">
            <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>Tất cả</option>
            <option value="day" {{ request('filter') == 'day' ? 'selected' : '' }}>Ngày</option>
            <option value="month" {{ request('filter') == 'month' ? 'selected' : '' }}>Tháng</option>
            <option value="year" {{ request('filter') == 'year' ? 'selected' : '' }}>Năm</option>
        </select>

        <input type="date" name="day" id="dayInput" style="display: none;" value="{{ request('day') }}">
        <input type="month" name="month" id="monthInput" style="display: none;" value="{{ request('month') }}">
        <input type="number" name="year" id="yearInput" style="display: none; width: 100px;" min="2000" max="2100" value="{{ request('year') }}">

        <button type="submit">Lọc</button>
    </form>

    <h2>✅ Đơn hàng giao thành công</h2>
    @if($successfulOrders->isEmpty())
        <p class="text">Không có đơn hàng thành công nào.</p>
    @else
        <table>
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
        <table>
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

    <script>
        function handleFilterChange() {
            var filter = document.getElementById('filter').value;
            document.getElementById('dayInput').style.display = (filter === 'day') ? 'inline-block' : 'none';
            document.getElementById('monthInput').style.display = (filter === 'month') ? 'inline-block' : 'none';
            document.getElementById('yearInput').style.display = (filter === 'year') ? 'inline-block' : 'none';
        }
        window.onload = handleFilterChange;
    </script>
</body>
</html>
