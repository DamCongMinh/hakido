<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thống Kê Doanh Thu</title>
    <link rel="stylesheet" href="{{ asset('css/Admin/statistics/statistics.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>
    @include('layout.header')

    <h1>Thống kê Doanh thu & Đơn hàng</h1>

    <p><strong>Tổng doanh thu:</strong> {{ number_format($totalRevenue, 0, ',', '.') }} VND</p>
    <p><strong>Tổng số đơn hàng:</strong> {{ $totalOrders }}</p>


    <h2>Biểu đồ thống kê theo năm</h2>
    <form method="GET" action="{{ route('admin.statistics') }}">
        <label for="year"><strong>Chọn năm:</strong></label>
        <select name="year" id="year" onchange="this.form.submit()">
            @for ($y = now()->year - 5; $y <= now()->year + 1; $y++)
                <option value="{{ $y }}" {{ $y == request('year', now()->year) ? 'selected' : '' }}>Năm {{ $y }}</option>
            @endfor
        </select>
    </form> 
        <table border="1" cellpadding="10" cellspacing="0">
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

        <div style="width: 80%; margin: auto;">
            <canvas id="monthlyChart"></canvas>
        </div>
        

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
                            categoryPercentage: 0.5,
                            barPercentage: 0.5,  
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
