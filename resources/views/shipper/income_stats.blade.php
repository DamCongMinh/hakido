<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê thu nhập</title>

    <link rel="stylesheet" href="{{ asset('css/Shipper/income_stats.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    <!-- Chọn năm cho thống kê tháng -->
    <form method="GET" action="{{ route('shipper.orders.shipper.income.stats') }}" style="margin: 20px 0; text-align: center;">
        <label for="year">Chọn năm:</label>
        <select name="year" id="year" onchange="this.form.submit()">
            @for ($y = now()->year; $y >= now()->year - 5; $y--)
                <option value="{{ $y }}" {{ ($y == ($selectedYear ?? now()->year)) ? 'selected' : '' }}>Năm {{ $y }}</option>
            @endfor
        </select>
    </form>

    <h2>Thu nhập theo tháng ({{ $selectedYear ?? now()->year }})</h2>

    @if($monthlyIncome->isEmpty())
        <p style="text-align: center; color: #666;">Chưa có dữ liệu thu nhập trong năm nay.</p>
    @else
        <!-- Bảng dữ liệu -->
        <h3>Bảng thống kê thu nhập</h3>
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

        <!-- Biểu đồ -->
        <h3>Biểu đồ thống kê theo tháng</h3>
        <canvas id="incomeChart" style="max-width: 100%; margin-top: 30px;"></canvas>

        <script>
            const ctx = document.getElementById('incomeChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($monthlyIncome->pluck('month')->map(fn($m) => 'Tháng ' . $m)) !!},
                    datasets: [{
                        label: 'Tổng thu nhập (₫)',
                        data: {!! json_encode($monthlyIncome->pluck('total_fee')) !!},
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        categoryPercentage: 0.1,
                        barPercentage: 0.7
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat().format(value) + '₫';
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.parsed.y;
                                    return 'Thu nhập: ' + new Intl.NumberFormat().format(value) + '₫';
                                }
                            }
                        }
                    }
                }
            });
        </script>
    @endif

    <!-- Chọn tháng/năm cho thống kê ngày -->
    <h2 style="margin-top: 50px;">Thống kê theo ngày trong tháng</h2>
    <form method="GET" action="{{ route('shipper.orders.shipper.income.stats') }}" style="margin: 20px 0; text-align: center;">
        <label for="month">Tháng:</label>
        <select name="month" id="month">
            @for ($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ (isset($selectedMonth) && $selectedMonth == $m) ? 'selected' : '' }}>Tháng {{ $m }}</option>
            @endfor
        </select>

        <label for="year">Năm:</label>
        <select name="year" id="year">
            @for ($y = now()->year; $y >= now()->year - 5; $y--)
                <option value="{{ $y }}" {{ (isset($selectedYear) && $selectedYear == $y) ? 'selected' : '' }}>Năm {{ $y }}</option>
            @endfor
        </select>

        <button type="submit">Xem thống kê</button>
    </form>

    @if(!empty($dailyIncome) && $dailyIncome->isNotEmpty())
        <!-- Bảng dữ liệu theo ngày -->
        <h3>Bảng thống kê thu nhập theo ngày (Tháng {{ $selectedMonth }}/{{ $selectedYear }})</h3>
        <table>
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Số đơn giao</th>
                    <th>Tổng thu nhập (₫)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dailyIncome as $item)
                    <tr>
                        <td>{{ $item->day }}/{{ $selectedMonth }}</td>
                        <td>{{ $item->order_count }}</td>
                        <td>{{ number_format($item->total_fee) }}₫</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Biểu đồ -->
        <h3>Biểu đồ thống kê theo ngày</h3>
        <canvas id="dailyIncomeChart" style="max-width: 100%; margin-top: 30px;"></canvas>

        <script>
            const ctxDaily = document.getElementById('dailyIncomeChart').getContext('2d');
            new Chart(ctxDaily, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($dailyIncome->pluck('day')->map(fn($d) => 'Ngày ' . $d)) !!},
                    datasets: [{
                        label: 'Tổng thu nhập (₫)',
                        data: {!! json_encode($dailyIncome->pluck('total_fee')) !!},
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        categoryPercentage: 0.1,
                        barPercentage: 0.7
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat().format(value) + '₫';
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.parsed.y;
                                    return 'Thu nhập: ' + new Intl.NumberFormat().format(value) + '₫';
                                }
                            }
                        }
                    }
                }
            });
        </script>
    @elseif(isset($selectedMonth))
        <p style="text-align: center; color: #666;">Chưa có dữ liệu thu nhập cho Tháng {{ $selectedMonth }}/{{ $selectedYear }}.</p>
    @endif

    @include('layout.footer')
</body>
</html>
