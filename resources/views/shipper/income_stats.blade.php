<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê thu nhập</title>

    <link rel="stylesheet" href="{{ asset('css/Shipper/income_stats.css') }}">
</head>
<body>
    @include('layout.header')

    <h1>📊 Thống kê thu nhập của Shipper</h1>

    <div class="stats">
        <div class="card">
            <h2>{{ $totalOrders }}</h2>
            <h3>Đơn giao thành công</h3>
        </div>
        <div class="card">
            <h2>{{ number_format($totalIncome) }}₫</h2>
            <h3>Tổng thu nhập</h3>
        </div>
    </div>

    <h2>Thu nhập theo tháng ({{ now()->year }})</h2>
    @if($monthlyIncome->isEmpty())
        <p style="text-align: center; color: #666;">Chưa có dữ liệu thu nhập trong năm nay.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Tháng</th>
                    <th>Số đơn giao</th>
                    <th>Tổng thu nhập (₫)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyIncome as $item)
                    <tr>
                        <td>Tháng {{ $item->month }}</td>
                        <td>{{ $item->order_count }}</td>
                        <td>{{ number_format($item->total_fee) }}₫</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    
</body>
</html>
