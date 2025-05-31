<!DOCTYPE html>
<html>
<head>
    <title>Trang quản trị - Tổng quan</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/Admin/statistics/home_statistics.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    @include('layout.header')
    <div class="container mt-4">

        <div class="top">
            <div class="top-statistics">
                <h2>Tổng quan doanh thu và đơn hàng</h2>
                <div class="list_card row mb-4">
                    <div class="card col-md-4">
                        <div class="card-items text-white bg-primary">
                            <div class="card-body">
                                <h5>Tổng doanh thu</h5>
                                <p>{{ number_format($totalRevenue) }} VND</p>
                            </div>
                        </div>
                    </div>
                    <div class="card col-md-4">
                        <div class="card-items text-white bg-primary">
                            <div class="card-body">
                                <h5>Doanh thu hôm nay</h5>
                                <p>{{ number_format($todayRevenue) }} VND</p>
                            </div>
                        </div>
                    </div>
                    <div class="card col-md-4">
                        <div class="card-items text-white bg-success">
                            <div class="card-body">
                                <h5>Đơn hàng hôm nay</h5>
                                <p>{{ $todayOrders }} đơn</p>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="list-group">
                    <a href="{{ route('admin.orderstatistics') }}" class="list-group-item list-group-item-action">Chi tiết đơn hàng</a>
                    <a href="{{ route('admin.inventoryStatistics') }}" class="list-group-item list-group-item-action">Tồn kho nhà hàng</a>
                    <a href="{{ route('admin.statistics') }}" class="list-group-item list-group-item-action">Thống kê doanh thu</a>
                </div> 
            </div>
    
            <div class="list_chart">
                <!-- Biểu đồ tròn người dùng -->
                <div class="col-md-4">
                    <div class="chart-container">
                        <h3>Thống kê người dùng</h3>
                        <canvas id="userPieChart"></canvas>
                    </div>
                </div>    
    
                <!-- Biểu đồ tròn -->
                <div class="col-md-4">
                    <div class="chart-container">
                        <h3>Phân bố sản phẩm (Beverages vs Foods)</h3>
                        <canvas id="productPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="list_chart-bottom">

            <!-- Biểu đồ cột -->
            <div class="col-md-4">
                <div class="chart-container">
                    <h3>Đơn hàng theo nhà hàng</h5>
                    <canvas id="orderBarChart"></canvas>
                </div>
            </div>

            <!-- Biểu đồ đường -->
            <div class="col-md-4">
                <div class="chart-container">
                    <h3>Doanh thu theo ngày</h3>
                    <canvas id="revenueLineChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>

        // Biểu đồ tròn: thống kê người dùng
        const userCtx = document.getElementById('userPieChart').getContext('2d');
        new Chart(userCtx, {
            type: 'pie',
            data: {
                labels: ['Khách hàng', 'Shipper', 'Nhà hàng'],
                datasets: [{
                    data: [{{ $customerCount }}, {{ $shipperCount }}, {{ $restaurantCount }}],
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
                }]
            }
        });
        // Biểu đồ tròn: beverages vs foods
        const pieCtx = document.getElementById('productPieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['Beverages', 'Foods'],
                datasets: [{
                    data: [{{ $beveragesCount }}, {{ $foodsCount }}],
                    backgroundColor: ['#36A2EB', '#FF6384']
                }]
            }
        });

        // Biểu đồ cột: đơn hàng theo nhà hàng
        const barCtx = document.getElementById('orderBarChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($restaurantNames) !!},
                datasets: [{
                    label: 'Số đơn hàng',
                    data: {!! json_encode($restaurantOrderCounts) !!},
                    backgroundColor: '#4BC0C0'
                }]
            }
        });

        // Biểu đồ đường: doanh thu theo ngày
        const lineCtx = document.getElementById('revenueLineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($revenueDates) !!},
                datasets: [{
                    label: 'Doanh thu (VND)',
                    data: {!! json_encode($revenueValues) !!},
                    fill: false,
                    borderColor: '#FF9F40',
                    tension: 0.1
                }]
            }
        });
    </script>
</body>
</html>
