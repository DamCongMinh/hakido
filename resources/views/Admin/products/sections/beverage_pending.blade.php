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

    <div id="beverage-pending" class="product-section">
        <h2 class="h2">Đồ Uống - Chờ Duyệt</h2>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Hình ảnh</th>
                    <th>Tên</th>
                    <th>Nhà hàng</th>
                    <th>Size</th>
                    <th>Giá</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($beveragesPending as $beverage)
                    @php $rowspan = count($beverage->beverageSizes); @endphp
                    @foreach($beverage->beverageSizes as $index => $size)
                        <tr>
                            @if ($index === 0)
                                <td rowspan="{{ $rowspan }}">{{ $beverage->id }}</td>
                                <td rowspan="{{ $rowspan }}"><img src="{{ asset('storage/' . $beverage->image) }}" width="70"></td>
                                <td rowspan="{{ $rowspan }}">{{ $beverage->name }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $beverage->restaurant->name ?? 'Không có' }}</td>
                            @endif
                            <td>{{ $size->size }}</td>
                            <td>{{ number_format($size->new_price) }}₫</td>
                            @if ($index === 0)
                                <td rowspan="{{ $rowspan }}">
                                    <form action="{{ route('admin.beverages.approve', $beverage->id) }}" method="POST" style="display:inline">
                                        @csrf
                                        <button class="btn" type="submit">Duyệt</button>
                                    </form>
                                    |
                                    <form action="{{ route('admin.beverages.reject', $beverage->id) }}" method="POST" style="display:inline">
                                        @csrf
                                        <button class="btn-reject" type="submit" onclick="return confirm('Bạn có chắc muốn từ chối?')">Từ chối</button>
                                    </form>
                                    |
                                    <form action="{{ route('admin.beverages.destroy', $beverage->id) }}" method="POST" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-danger" type="submit" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @empty
                    <tr><td colspan="7" style="text-align: center; color: gray;">Không có sản phẩm nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @include('layout.footer')
</body>
</html>