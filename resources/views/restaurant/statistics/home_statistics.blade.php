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
        <h1>📊 Thống kê Doanh thu & Đơn hàng của Nhà hàng</h1>

        <div class="summary">
            <p><strong>Tổng doanh thu:</strong> {{ number_format($totalRevenue, 0, ',', '.') }} VND</p>
            <p><strong>Tổng số đơn hàng:</strong> {{ $totalOrders }}</p>
        </div>

        <div class="filter">
            <form method="GET" action="{{ route('restaurant.statistics.home') }}">
                <label for="year"><strong>Chọn năm:</strong></label>
                <select name="year" id="year" onchange="this.form.submit()">
                    @for ($y = now()->year - 5; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $y == request('year', now()->year) ? 'selected' : '' }}>
                            Năm {{ $y }}
                        </option>
                    @endfor
                </select>
            </form>
        </div>

        <h2>📅 Thống kê theo tháng</h2>
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
                        <td>Tháng {{ $stat->month }}</td>
                        <td>{{ number_format($stat->revenue, 0, ',', '.') }}</td>
                        <td>{{ $stat->orders }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h2>Biểu đồ doanh thu</h2>
        <div class="chart-container">
            <canvas id="monthlyChart"></canvas>
        </div>    

        <h2>Biểu đồ số lượng đơn hàng</h2>
        <div class="chart-container">
            <canvas id="orderChart"></canvas>
        </div>
    </div>

    <a href="{{ route('restaurant') }}">← Quay lại trang Nhà hàng</a>

    @include('layout.footer')

    <script>
        const monthlyLabels = {!! json_encode($monthlyStats->map(fn($s) => 'Tháng ' . $s->month)) !!};
        const revenueData = {!! json_encode($monthlyStats->map(fn($s) => $s->revenue)) !!};
        const orderData = {!! json_encode($monthlyStats->map(fn($s) => $s->orders)) !!};

        // Biểu đồ doanh thu
        const revenueCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Doanh thu theo tháng (VND)',
                    data: revenueData,
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
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Doanh thu (VND)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tháng'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });

        // Biểu đồ đơn hàng
        
        const orderCtx = document.getElementById('orderChart').getContext('2d');
        new Chart(orderCtx, {
            type: 'line',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Số đơn hàng theo tháng',
                    data: orderData,
                    backgroundColor: 'rgba(255, 99, 132, 0.3)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số đơn hàng'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tháng'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });
    </script>
</body>
</html>
