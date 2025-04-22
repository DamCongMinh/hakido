<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thống Kê Nhà Hàng</title>
    <link rel="stylesheet" href="{{ asset('css/restaurant/statistics/home_statistics.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    @include('layout.header')

    <div class="container">
        <h1>Thống kê Doanh thu & Đơn hàng của Nhà hàng</h1>

        <div class="summary">
            <p><strong>Tổng doanh thu:</strong> {{ number_format($totalRevenue, 0, ',', '.') }} VND</p>
            <p><strong>Tổng số đơn hàng:</strong> {{ $totalOrders }}</p>
        </div>

        <div class="filter">
            <form method="GET" action="{{ route('restaurant.statistics.home') }}">
                <label for="year"><strong>Chọn năm:</strong></label>
                <select name="year" id="year" onchange="this.form.submit()">
                    @for ($y = now()->year - 5; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $y == request('year', now()->year) ? 'selected' : '' }}>Năm {{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>

        <table class="stats-table">
            <thead>
                <tr>
                    <th>Tháng</th>
                    <th>Doanh thu (VND)</th>
                    <th>Số đơn hàng</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($monthlyStats as $stat)
                    <tr>
                        <td>{{ $stat->month }}</td>
                        <td>{{ number_format($stat->revenue, 0, ',', '.') }}</td>
                        <td>{{ $stat->orders }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="chart-container">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <a href="{{ route('restaurant') }}">← Quay lại trang Nhà hàng</a>

    <script>
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthlyStats->pluck('month')) !!},
                datasets: [{
                    label: 'Doanh thu theo tháng (VND)',
                    data: {!! json_encode($monthlyStats->pluck('revenue')) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + ' VND';
                            }
                        },
                        beginAtZero: true
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tháng'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
