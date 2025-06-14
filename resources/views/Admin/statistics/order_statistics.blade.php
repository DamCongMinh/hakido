<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Thống kê đơn hàng theo từng nhà hàng</title>
    <link rel="stylesheet" href="{{ asset('/css/Admin/statistics/orderstatistic.css') }}">
</head>
<body>
    @include('layout.header')
    <div class="container">
        <h2 class="h2">Bảng thống kê số lượng đơn hàng trong năm</h2>
        <div class="totalOrders"><p><strong>Tổng số đơn hàng:</strong> {{ $totalOrders }}</p></div>
        {{-- <form id="statistic_form" method="GET" action="{{ route('admin.statistics') }}">
            <label for="year"><strong>Chọn năm:</strong></label>
            <select name="year" id="year" onchange="this.form.submit()">
                @for ($y = now()->year - 5; $y <= now()->year + 1; $y++)
                    <option value="{{ $y }}" {{ $y == request('year', now()->year) ? 'selected' : '' }}>Năm {{ $y }}</option>
                @endfor
            </select>
        </form>  --}}
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Tên nhà hàng</th>
                    <th>Tháng</th>
                    <th>Số đơn hàng</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($restaurants as $restaurant)
                    @if (isset($statsByRestaurant[$restaurant->id]))
                        @foreach ($statsByRestaurant[$restaurant->id] as $stat)
                            <tr>
                                <td>{{ $restaurant->name }}</td>
                                <td>{{ $stat['month'] }}</td>
                                <td>{{ $stat['orders'] }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>{{ $restaurant->name }}</td>
                            <td>Không có</td>
                            <td>0</td>
                        </tr>
                    @endif
                @endforeach
            </tbody> 
        </table>
    </div>
    @include('layout.footer')
    
</body>
</html>