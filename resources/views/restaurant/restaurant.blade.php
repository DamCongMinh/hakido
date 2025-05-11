<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant</title>
    <link rel="stylesheet" href="{{ asset('css/Restaurant/restaurant.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    @include('layout.header')

    <main class="content">
        <header class="main-header">
            <h1>Chào mừng, Nhà hàng!</h1>
            <p>Hôm nay là <strong>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</strong></p>
        </header>

        <section class="stats">
            <div class="card">
                <div class="card-content">
                    @if (isset($todayOrders))
                        <p>Hôm nay có {{ $todayOrders }} đơn hàng</p>
                    @endif
                </div>
            </div>

            <div class="card"><div class="card-content"><h3>Đơn đang giao</h3><p>{{ $shippingOrders ?? 0 }} đơn</p></div></div>
            <div class="card"><div class="card-content"><h3>Đơn đã huỷ</h3><p>{{ $canceledOrders ?? 0 }} đơn</p></div></div>
            <div class="card"><div class="card-content"><h3>Đơn hoàn thành</h3><p>{{ $totalOrders ?? 0 }} đơn</p></div></div>

            <div class="card">
                <div class="card-content">
                    <h3>Tỷ lệ hoàn thành</h3>
                    <p>
                        @php
                            $completionRate = $todayOrders > 0 ? round(($completedTodayOrders ?? 0) / $todayOrders * 100, 1) : 0;
                        @endphp
                        {{ $completionRate }}%
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-content">
                    @if (isset($processingOrders))
                        <p>Đơn đang xử lý: {{ $processingOrders }}</p>
                    @endif
                </div>
            </div>

            <a href="{{ route('restaurant.statistics.home') }}" class="card-link">
                <div class="card">
                    <div class="card-content">
                        <h3>Doanh thu</h3>
                        <p>{{ number_format($totalRevenue, 0, ',', '.') }}đ</p>
                    </div>
                    <div class="eye-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                </div>
            </a>
        </section>

        <section class="recent-orders">
            <h2>Đơn hàng gần đây</h2>
            <table>
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tình trạng</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->customer_name ?? 'Ẩn danh' }}</td>
                            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</td>
                            <td>
                                <span class="status {{ getStatusClass($order->status) }}">
                                    {{ getStatusLabel($order->status) }}
                                </span>
                            </td>
                            <td>
                                @if(in_array($order->status, ['pending', 'processing', 'delivering']))
                                    <form method="POST" action="{{ route('restaurant.order.approve', $order->id) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn-approve">
                                            @switch($order->status)
                                                @case('pending')
                                                    Duyệt
                                                    @break
                                                @case('processing')
                                                    Xác nhận giao
                                                    @break
                                                @case('delivering')
                                                    Hoàn tất
                                                    @break
                                            @endswitch
                                        </button>
                                    </form>
                                @endif


                                @if(in_array($order->status, ['pending', 'processing', 'delivering']))
                                    <form method="POST" action="{{ route('restaurant.order.cancel', $order->id) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn-cancel">Hủy</button>
                                    </form>
                                @endif


                            </td>
                            
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </main>
    
    @include('layout.footer')
</body>
</html>

@php
function getStatusClass($status) {
    return match (strtolower($status)) {
        'pending' => 'pending',
        'processing' => 'processing',
        'completed' => 'completed',
        'canceled' => 'cancelled',
        'delivering' => 'shipping',
        default => 'other',
    };
}

function getStatusLabel($status) {
    return match (strtolower($status)) {
        'pending' => 'Chờ xác nhận',
        'processing' => 'Chờ shipper nhận đơn',
        'completed' => 'Hoàn thành',
        'canceled' => 'Đã hủy',
        'delivering' => 'Đang giao',
        default => ucfirst($status),
    };
}
@endphp
