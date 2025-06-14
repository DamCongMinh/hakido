<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Thống kê tồn kho</title>
    <link rel="stylesheet" href="{{ asset('/css/Admin/statistics/inventory_statistic.css') }}">
</head>
<body>
    @include('layout.header')
    <div class="container">
        <h2>Thống kê tồn kho các nhà hàng</h2>
    
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Tên nhà hàng</th>
                    <th>Loại</th>
                    <th>Tổng số lượng đã bán</th>
                    <th>Tổng số lượng tồn</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($restaurants as $restaurant)
                    @php
                        // Món ăn
                        $totalFoodQuantity = $restaurant->foods->sum('quantity');
                        $soldFoodQuantity = $restaurant->foods->sum(function ($food) use ($soldFoods) {
                            return $soldFoods[$food->id] ?? 0;
                        });

                        // Đồ uống
                        $totalBeverageQuantity = 0;
                        $soldBeverageQuantity = 0;
                        foreach ($restaurant->beverages as $beverage) {
                            foreach ($beverage->beverageSizes as $size) {
                                $totalBeverageQuantity += $size->quantity;
                                $soldBeverageQuantity += $soldBeverageSizes[$size->id] ?? 0;
                            }
                        }

                        $hasFood = $totalFoodQuantity > 0;
                        $hasBeverage = $totalBeverageQuantity > 0;
                    @endphp

                    @if ($hasFood && $hasBeverage)
                        <tr>
                            <td rowspan="2">{{ $restaurant->name }}</td>
                            <td>Món ăn</td>
                            <td>{{ $soldFoodQuantity }}</td>
                            <td>{{ $totalFoodQuantity }}</td>
                        </tr>
                        <tr>
                            <td>Đồ uống</td>
                            <td>{{ $soldBeverageQuantity }}</td>
                            <td>{{ $totalBeverageQuantity }}</td>
                        </tr>
                    @elseif ($hasFood)
                        <tr>
                            <td>{{ $restaurant->name }}</td>
                            <td>Món ăn</td>
                            <td>{{ $soldFoodQuantity }}</td>
                            <td>{{ $totalFoodQuantity }}</td>
                        </tr>
                    @elseif ($hasBeverage)
                        <tr>
                            <td>{{ $restaurant->name }}</td>
                            <td>Đồ uống</td>
                            <td>{{ $soldBeverageQuantity }}</td>
                            <td>{{ $totalBeverageQuantity }}</td>
                        </tr>
                    @else
                        <tr>
                            <td>{{ $restaurant->name }}</td>
                            <td colspan="3">Không có dữ liệu tồn kho.</td>
                        </tr>
                    @endif
                @endforeach

            </tbody>
        </table>
    </div>
    @include('layout.footer')
    
    
</body>
</html>