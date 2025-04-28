<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quản lý sản phẩm</title>

    <link rel="stylesheet" href="{{ asset('css/Restaurant/products/home_product.css') }}">
</head>
<body>
    @include('layout.header')

    <h2>Danh sách Đồ Ăn</h2>
    <a class="btn" href="{{ route('restaurant.products.create') }}">+ Thêm sản phẩm</a>

    @if(session('success'))
        <div style="color: green">{{ session('success') }}</div>
    @endif

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Hình</th>
            <th>Tên</th>
            <th>Danh mục</th>
            <th>Giá</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        @foreach($foods as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td><img src="{{ asset('storage/' . $item->image) }}" width="60"></td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->category?->name ?? 'Không có' }}</td>
                <td>
                    {{ number_format($item->old_price * (1 - ($item->discount_percent / 100))) }}đ
                </td>
                <td>
                    <span class="{{ $item->status === 'approved' ? 'status-approved' : ($item->status === 'pending' ? 'status-pending' : 'status-rejected') }}">
                        {{ $item->status === 'approved' ? 'Đã duyệt' : ($item->status === 'pending' ? 'Chờ duyệt' : 'Bị từ chối') }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('restaurant.products.edit', $item->id) }}" class="btn btn-success">Sửa</a> |
                    <form action="{{ route('restaurant.products.destroy', $item->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>

    <h2>Danh sách Đồ Uống</h2>
    <a class="btn" href="{{ route('restaurant.products.create') }}?type=beverage">+ Thêm đồ uống</a>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Hình</th>
            <th>Tên</th>
            <th>Danh mục</th>
            <th>Giá</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
            @foreach($beverages as $beverage)
            <tr>
                <td>{{ $beverage->id }}</td>
                <td><img src="{{ asset('storage/' . $beverage->image) }}" width="60"></td>
                <td>{{ $beverage->name }}</td>
                <td>{{ $beverage->category?->name ?? 'Không có' }}</td>
                <td>
                    @php
                        $minSize = $beverage->beverageSizes->sortBy(function($size) {
                            return $size->old_price * (1 - $size->discount_percent / 100);
                        })->first();
                    @endphp

                    @if($minSize)
                        {{ number_format($minSize->old_price * (1 - ($minSize->discount_percent / 100))) }}đ
                    @else
                        Chưa có giá
                    @endif
                </td>
                <td>
                    <span class="{{ $beverage->status === 'approved' ? 'status-approved' : ($beverage->status === 'pending' ? 'status-pending' : 'status-rejected') }}">
                        {{ $beverage->status === 'approved' ? 'Đã duyệt' : ($beverage->status === 'pending' ? 'Chờ duyệt' : 'Bị từ chối') }}
                    </span>
                </td>
                <td>
                    <a class="btn btn-success" href="{{ route('restaurant.products.edit', $beverage->id) }}">Sửa</a> |
                    <form action="{{ route('restaurant.products.destroy', $beverage->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" onclick="return confirm('Xóa?')">Xóa</button>
                    </form>
                </td>
            </tr>
        @endforeach

    </table>
</body>
</html>
