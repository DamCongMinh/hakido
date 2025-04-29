<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Danh sách nhà hàng</title>

    <link rel="stylesheet" href="{{ asset('css/restaurant/search') }}">
</head>
<body>
   

    <h2>Kết quả cho từ khóa: "{{ $keyword }}"</h2>

        <h3>🍽️ Nhà hàng</h3>
        @if($restaurants->count())
            @foreach($restaurants as $restaurant)
                <div>
                    <strong>{{ $restaurant->name }}</strong><br>
                    Địa chỉ: {{ $restaurant->address }}
                </div>
            @endforeach
        @else
            <p>Không tìm thấy nhà hàng phù hợp.</p>
        @endif

        <h3>🥤 Sản phẩm</h3>
        @if($products->count())
            @foreach($products as $product)
                <div>
                    <strong>{{ $product->name }}</strong><br>
                    Loại: {{ $product instanceof \App\Models\Food ? 'Món ăn' : 'Đồ uống' }}
                </div>
            @endforeach
        @else
            <p>Không tìm thấy sản phẩm phù hợp.</p>
        @endif

</body>
</html>