<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
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
        
                        $rowCount = 0;
                        if ($totalFoodQuantity > 0) $rowCount++;
                        if ($totalBeverageQuantity > 0) $rowCount++;
                    @endphp
        
                    @if ($totalFoodQuantity > 0 || $totalBeverageQuantity > 0)
                        <tr>
                            <td rowspan="{{ $rowCount }}">{{ $restaurant->name }}</td>
                            <td>Món ăn</td>
                            <td>{{ $soldFoodQuantity }}</td>
                            <td>{{ $totalFoodQuantity }}</td>
                        </tr>
        
                        @if ($totalBeverageQuantity > 0)
                            <tr>
                                <td>Đồ uống</td>
                                <td>{{ $soldBeverageQuantity }}</td>
                                <td>{{ $totalBeverageQuantity }}</td>
                            </tr>
                        @endif
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
    
    
    
</body>
</html>