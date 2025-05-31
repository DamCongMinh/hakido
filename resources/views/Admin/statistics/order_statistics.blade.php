<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div><p><strong>Tổng số đơn hàng:</strong> {{ $totalOrders }}</p></div>

    <h2 class="h2">Bảng thống kê số lượng đơn hàng trong năm</h2>
    <form id="statistic_form" method="GET" action="{{ route('admin.statistics') }}">
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
                <th>Tên nhà hàng</th>
                <th>Tháng</th>
                <th>Số đơn hàng</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($restaurants as $index => $restaurant)
                @if (isset($monthlyStats[$index]))
                    <tr>
                        <td>{{ $restaurant->name }}</td>
                        <td>{{ $monthlyStats[$index]->month }}</td>
                        <td>{{ $monthlyStats[$index]->orders }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    
</body>
</html>