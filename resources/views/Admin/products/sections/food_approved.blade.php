<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <link rel="stylesheet" href="{{ asset('css/Admin/products/product_control_management.css') }}">
</head>
<body>
    @include('layout.header')

    <div class="list-btn">
        <form method="GET" type="foods_pending" action="{{ route('admin.foods.index') }}" style="display: inline;">
            <input type="hidden" name="view" value="food_pending">
            <button class="header_btn" type="submit" class="{{ request('view', 'food_pending') === 'food_pending' ? 'active' : '' }}">
                Đồ Ăn - Chờ Duyệt
            </button>
        </form>
    
        <form method="GET" type="foods_approved" action="{{ route('admin.foods.index') }}" style="display: inline;">
            <input type="hidden" name="view" value="food_approved">
            <button class="header_btn"  type="submit" class="{{ request('view') === 'food_approved' ? 'active' : '' }}">
                Đồ Ăn - Đã Duyệt
            </button>
        </form>

        <form method="GET" type="beverages_approved" action="{{ route('admin.beverages.index') }}" style="display: inline;">
            <input type="hidden" name="view" value="beverage_approved">
            <button class="header_btn" type="submit" class="{{ request('view') === 'beverage_approved' ? 'active' : '' }}">
                Đồ Uống - Đã Duyệt
            </button>
        </form>
    
        <form method="GET" type="beverages_pending" action="{{ route('admin.beverages.index') }}" style="display: inline;">
            <input type="hidden" name="view" value="beverage_pending">
            <button class="header_btn" type="submit" class="{{ request('view') === 'beverage_pending' ? 'active' : '' }}">
                Đồ Uống - Chờ Duyệt
            </button>
        </form>
    
        
    </div>
    
    <div id="food-approved" class="product-section">
        <h2 class="h2">Đồ Ăn - Đã Duyệt</h2>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Hình ảnh</th>
                    <th>Tên</th>
                    <th>Nhà hàng</th>
                    <th>Giá</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($foodsApproved as $food)
                    <tr>
                        <td>{{ $food->id }}</td>
                        <td><img src="{{ asset('storage/' . $food->image) }}" alt="Hình" width="70"></td>
                        <td>{{ $food->name }}</td>
                        <td>{{ $food->restaurant->name ?? 'Không có' }}</td>
                        <td>{{ number_format($food->new_price) }}đ</td>
                        <td>
                            <form action="{{ route('admin.foods.destroy', $food->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn-danger" type="submit" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align: center; color: gray;">Không có sản phẩm nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('layout.footer')
</body>
</html>
