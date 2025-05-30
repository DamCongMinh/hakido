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
            <th>Số lượng</th>
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
                <td>{{ $item->quantity }}</td> 
                <td>
                    {{ number_format($item->old_price * (1 - ($item->discount_percent / 100))) }}đ
                </td>
                <td>
                    <span class="{{ $item->status === 'approved' ? 'status-approved' : ($item->status === 'pending' ? 'status-pending' : 'status-rejected') }}">
                        {{ $item->status === 'approved' ? 'Đã duyệt' : ($item->status === 'pending' ? 'Chờ duyệt' : 'Bị từ chối') }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('restaurant.products.edit', $item->id) }}" class="btn btn-success">Sửa</a> 
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
        <thead>
            <tr>
                <th>ID</th>
                <th>Hình</th>
                <th>Tên</th>
                <th>Danh mục</th>
                <th>Size</th>
                <th>Giá sau giảm</th>
                <th>Số lượng</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($beverages as $beverage)
                @php $rowspan = count($beverage->beverageSizes) ?: 1; @endphp
    
                @foreach($beverage->beverageSizes as $index => $size)
                    <tr>
                        @if($index === 0)
                            <td rowspan="{{ $rowspan }}">{{ $beverage->id }}</td>
                            <td rowspan="{{ $rowspan }}"><img src="{{ asset('storage/' . $beverage->image) }}" width="60"></td>
                            <td rowspan="{{ $rowspan }}">{{ $beverage->name }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $beverage->category?->name ?? 'Không có' }}</td>
                        @endif
    
                        <td><strong>{{ strtoupper($size->size) }}</strong></td>
                        <td>{{ number_format($size->old_price * (1 - $size->discount_percent / 100)) }}đ</td>
                        <td>{{ $size->quantity }}</td>
    
                        @if($index === 0)
                            <td rowspan="{{ $rowspan }}">
                                <span class="{{ 
                                    $beverage->status === 'approved' ? 'status-approved' :
                                    ($beverage->status === 'pending' ? 'status-pending' : 'status-rejected') 
                                }}">
                                    {{ 
                                        $beverage->status === 'approved' ? 'Đã duyệt' :
                                        ($beverage->status === 'pending' ? 'Chờ duyệt' : 'Bị từ chối') 
                                    }}
                                </span>
                            </td>
                            <td rowspan="{{ $rowspan }}">
                                <a class="btn btn-success" href="{{ route('restaurant.products.edit', $beverage->id) }}">Sửa</a> 
                                <form action="{{ route('restaurant.products.destroy', $beverage->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" onclick="return confirm('Xóa?')">Xóa</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @endforeach
    
                {{-- Nếu không có size nào --}}
                @if($beverage->beverageSizes->isEmpty())
                    <tr>
                        <td>{{ $beverage->id }}</td>
                        <td><img src="{{ asset('storage/' . $beverage->image) }}" width="60"></td>
                        <td>{{ $beverage->name }}</td>
                        <td>{{ $beverage->category?->name ?? 'Không có' }}</td>
                        <td colspan="3" style="text-align: center;">Chưa có size</td>
                        <td>
                            <span class="{{ $beverage->status === 'approved' ? 'status-approved' : ($beverage->status === 'pending' ? 'status-pending' : 'status-rejected') }}">
                                {{ $beverage->status === 'approved' ? 'Đã duyệt' : ($beverage->status === 'pending' ? 'Chờ duyệt' : 'Bị từ chối') }}
                            </span>
                        </td>
                        <td>
                            <a class="btn btn-success" href="{{ route('restaurant.products.edit', $beverage->id) }}">Sửa</a> 
                            <form action="{{ route('restaurant.products.destroy', $beverage->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" onclick="return confirm('Xóa?')">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    

    @include('layout.footer')
</body>
</html>
