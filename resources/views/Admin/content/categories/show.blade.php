<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hiển thị danh mục</title>
</head>
<body>
    @extends('layouts.app')

    @section('content')
        <h2>Danh mục: {{ $category->name }}</h2>

        @if($foods->count())
            <h3>🍽 Món ăn</h3>
            <ul>
                @foreach($foods as $food)
                    <li>{{ $food->name }} - {{ $food->price }}đ</li>
                @endforeach
            </ul>
        @endif

        @if($beverages->count())
            <h3>🥤 Đồ uống</h3>
            <ul>
                @foreach($beverages as $bev)
                    <li>{{ $bev->name }} - {{ $bev->price }}đ</li>
                @endforeach
            </ul>
        @endif

        @if(!$foods->count() && !$beverages->count())
            <p>Không có món nào trong danh mục này.</p>
        @endif
    @endsection

</body>
</html>