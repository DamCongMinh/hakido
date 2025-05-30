<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/Restaurant/statistics/product_sales.css') }}">
</head>
<body>
    @include('layout.header')

    <div class="stat-buttons">
        <form method="GET" action="{{ route('restaurant.statistics.home') }}" style="display: inline;">
            <input type="hidden" name="type" value="revenue">
            <button type="submit" class="{{ request('type', 'revenue') === 'revenue' ? 'active' : '' }}">Doanh thu & Đơn hàng</button>
        </form>
    
        <form method="GET" action="{{ route('restaurant.statistics.home') }}" style="display: inline;">
            <input type="hidden" name="type" value="inventory">
            <button type="submit" class="{{ request('type') === 'inventory' ? 'active' : '' }}">Kho hàng</button>
        </form>
    
        <form method="GET" action="{{ route('restaurant.statistics.home') }}" style="display: inline;">
            <input type="hidden" name="type" value="product_sales">
            <button type="submit" class="{{ request('type') === 'product_sales' ? 'active' : '' }}">Sản phẩm bán ra</button>
        </form>
    </div>

    <h2 class="h2">🛒 Thống kê Số lượng sản phẩm bán ra</h2>
    <table class="stats-table">
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Số lượng bán</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productSales as $product)
                <tr>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->total_sold }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2 class="h2">🔥 Top sản phẩm bán chạy</h2>
    <table class="stats-table">
        <thead>
            <tr>
                <th>Tên sản phẩm</th>
                <th>Loại</th>
                <th>Số lượng đã bán</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topSellingItems as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->type }}</td>
                    <td>{{ $item->total_sold }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Không có dữ liệu</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('layout.footer')

</body>
</html>