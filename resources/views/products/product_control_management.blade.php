<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
    <link rel="stylesheet" href="{{ asset('css/Admin/products/product_control_management.css') }}">
</head>
<body>
    @include('layout.header') {{-- Sử dụng header dùng chung --}}
    @if(session('success'))
    <div style="color: green; font-weight: bold;">
        {{ session('success') }}
    </div>
    @endif


    <!-- Đồ Ăn - Đã Duyệt -->
    <h2>Đồ Ăn - Đã Duyệt</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Hình ảnh</th>
                <th>Tên</th>
                <th>Nhà hàng</th>
                <th>Giá</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($foodsApproved as $food)
                <tr>
                    <td>{{ $food->id }}</td>
                    <td><img src="{{ asset('storage/' . $food->image) }}" alt="Hình" width="70"></td>
                    <td>{{ $food->name }}</td>
                    <td>{{ $food->restaurant->restaurant_name ?? 'Không có' }}</td>
                    <td>{{ number_format($food->price) }}đ</td>
                    <td>
                        <a href="{{ route('foods.edit', $food->id) }}">Sửa</a> | 
                        <form action="{{ route('foods.destroy', $food->id) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Đồ Ăn - Chờ Duyệt</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Hình ảnh</th>
                <th>Tên</th>
                <th>Nhà hàng</th>
                <th>Giá</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($foodsPending as $food)
                <tr>
                    <td>{{ $food->id }}</td>
                    <td><img src="{{ asset('storage/' . $food->image) }}" alt="Hình" width="70"></td>
                    <td>{{ $food->name }}</td>
                    <td>{{ $food->restaurant->restaurant_name ?? 'Không có' }}</td>
                    <td>{{ number_format($food->price) }}đ</td>
                    <td>
                        <form action="{{ route('foods.approve', $food->id) }}" method="POST" style="display:inline">
                            @csrf
                            <button type="submit">Duyệt</button>
                        </form>
                        |
                        <a href="{{ route('foods.edit', $food->id) }}">Sửa</a> | 
                        <form action="{{ route('foods.destroy', $food->id) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Đồ Uống - Đã Duyệt -->
    <h2>Đồ Uống - Đã Duyệt</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Hình ảnh</th>
                <th>Tên</th>
                <th>Nhà hàng</th>
                <th>Giá</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($beveragesApproved as $beverage)
                <tr>
                    <td>{{ $beverage->id }}</td>
                    <td><img src="{{ asset('storage/' . $beverage->image) }}" alt="Hình" width="70"></td>
                    <td>{{ $beverage->name }}</td>
                    <td>{{ $beverage->restaurant->restaurant_name ?? 'Không có' }}</td>
                    <td>{{ number_format($beverage->price) }}đ</td>
                    <td>
                        <a href="{{ route('beverages.edit', $beverage->id) }}">Sửa</a> | 
                        <form action="{{ route('beverages.destroy', $beverage->id) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Đồ Uống - Chờ Duyệt</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Hình ảnh</th>
                <th>Tên</th>
                <th>Nhà hàng</th>
                <th>Giá</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($beveragesPending as $beverage)
                <tr>
                    <td>{{ $beverage->id }}</td>
                    <td><img src="{{ asset('storage/' . $beverage->image) }}" alt="Hình" width="70"></td>
                    <td>{{ $beverage->name }}</td>
                    <td>{{ $beverage->restaurant->restaurant_name ?? 'Không có' }}</td>
                    <td>{{ number_format($beverage->price) }}đ</td>
                    <td>
                        <form action="{{ route('beverages.approve', $beverage->id) }}" method="POST" style="display:inline">
                            @csrf
                            <button type="submit">Duyệt</button>
                        </form>
                        |
                        <a href="{{ route('beverages.edit', $beverage->id) }}">Sửa</a> | 
                        <form action="{{ route('beverages.destroy', $beverage->id) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('admin.dashboard') }}">← Quay lại trang Admin</a>

</body>
</html>