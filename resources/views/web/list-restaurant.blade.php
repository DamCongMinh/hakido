<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div class="restaurant-search-results">
        <h3>Kết quả tìm kiếm nhà hàng</h3>
        @if($restaurants->count())
            <ul>
                @foreach ($restaurants as $restaurant)
                    <li>
                        <strong>{{ $restaurant->name }}</strong><br>
                        Địa chỉ: {{ $restaurant->address }}<br>
                        Điện thoại: {{ $restaurant->phone }}<br>
                        <a href="{{ route('restaurant.show', $restaurant->id) }}">Xem chi tiết</a>
                    </li>
                @endforeach
            </ul>
    
            <!-- Phân trang -->
            {{ $restaurants->links() }}
        @else
            <p>Không tìm thấy nhà hàng nào phù hợp.</p>
        @endif
    </div>
    
</body>
</html>