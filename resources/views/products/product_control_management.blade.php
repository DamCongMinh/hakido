<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
    <link rel="stylesheet" href="{{ asset('css/Admin/products/product_control_management.css') }}">
</head>
<body>
    @include('layout.header')

   <div class="container">
        @if(session('success'))
            <div style="color: green; font-weight: bold;">
                {{ session('success') }}
            </div>
        @endif

        <div class="list-btn">
            <button class="header_btn" onclick="showSection('food-approved')">Đồ Ăn - Đã Duyệt</button>
            <button class="header_btn" onclick="showSection('food-pending')">Đồ Ăn - Chờ Duyệt</button>
            <button class="header_btn" onclick="showSection('beverage-approved')">Đồ Uống - Đã Duyệt</button>
            <button class="header_btn" onclick="showSection('beverage-pending')">Đồ Uống - Chờ Duyệt</button>
        </div>


        <!-- Đồ Ăn - Đã Duyệt -->
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
                        <tr>
                            <td colspan="6" style="text-align: center; color: gray;">Không có sản phẩm nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>    

        <hr style="margin: 40px 0;">

        <!-- Đồ Ăn - Chờ Duyệt -->
        <div id="food-pending" class="product-section">
            <h2 class="h2">Đồ Ăn - Chờ Duyệt</h2>
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
                    @forelse($foodsPending as $food)
                        <tr>
                            <td>{{ $food->id }}</td>
                            <td><img src="{{ asset('storage/' . $food->image) }}" alt="Hình" width="70"></td>
                            <td>{{ $food->name }}</td>
                            <td>{{ $food->restaurant->name ?? 'Không có' }}</td>
                            <td>{{ number_format($food->new_price) }}đ</td>
                            <td>
                                <form action="{{ route('admin.foods.approve', $food->id) }}" method="POST" style="display:inline">
                                    @csrf
                                    <button class="btn" type="submit">Duyệt</button>
                                </form>
                                |
                                <form action="{{ route('admin.foods.reject', $food->id) }}" method="POST" style="display:inline">
                                    @csrf
                                    <button class="btn-reject" type="submit" onclick="return confirm('Bạn có chắc muốn từ chối?')">Từ chối</button>
                                </form>
                                |
                                <form action="{{ route('admin.foods.destroy', $food->id) }}" method="POST" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-danger" type="submit" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: gray;">Không có sản phẩm nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>    

        <hr style="margin: 40px 0;">

        <!-- Đồ Uống - Đã Duyệt -->
        <div id="beverage-approved" class="product-section">
            <h2 class="h2">Đồ Uống - Đã Duyệt</h2>
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
                    @forelse($beveragesApproved as $beverage)
                        @php $rowspan = count($beverage->beverageSizes); @endphp

                        @foreach($beverage->beverageSizes as $index => $size)
                            <tr>
                                @if ($index === 0)
                                    <td rowspan="{{ $rowspan }}">{{ $beverage->id }}</td>
                                    <td rowspan="{{ $rowspan }}">
                                        <img src="{{ asset('storage/' . $beverage->image) }}" alt="Hình" width="70">
                                    </td>
                                    <td rowspan="{{ $rowspan }}">{{ $beverage->name }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $beverage->restaurant->name ?? 'Không có' }}</td>
                                @endif

                                <td>{{ $size->size }}</td>
                                <td>{{ number_format($size->new_price) }}₫</td>

                                @if ($index === 0)
                                    <td rowspan="{{ $rowspan }}">
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
                        <tr>
                            <td colspan="7" style="text-align: center; color: gray;">Không có sản phẩm nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>    

        <hr style="margin: 40px 0;">

        <!-- Đồ Uống - Chờ Duyệt -->
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
                                    <td rowspan="{{ $rowspan }}">
                                        <img src="{{ asset('storage/' . $beverage->image) }}" alt="Hình" width="70">
                                    </td>
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
                        <tr>
                            <td colspan="7" style="text-align: center; color: gray;">Không có sản phẩm nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
   </div>

   <script>
        function showSection(id) {
            const sections = document.querySelectorAll('.product-section');
            sections.forEach(section => {
                section.style.display = (section.id === id) ? 'block' : 'none';
            });
        }

        // Hiển thị phần đầu tiên mặc định khi load trang
        window.onload = function () {
            showSection('food-approved');
        };
    </script>

    {{-- @section('title', 'Quản lý sản phẩm')

    @section('content')
    <link rel="stylesheet" href="{{ asset('css/Admin/products/product_control_management.css') }}">

    <div class="container">
        @if(session('success'))
            <div style="color: green; font-weight: bold;">
                {{ session('success') }}
            </div>
        @endif

        <div class="list-btn">
            <button class="header_btn" onclick="showSection('food-approved')">Đồ Ăn - Đã Duyệt</button>
            <button class="header_btn" onclick="showSection('food-pending')">Đồ Ăn - Chờ Duyệt</button>
            <button class="header_btn" onclick="showSection('beverage-approved')">Đồ Uống - Đã Duyệt</button>
            <button class="header_btn" onclick="showSection('beverage-pending')">Đồ Uống - Chờ Duyệt</button>
        </div>

        @include('products.sections.food_approved', ['foodsApproved' => $foodsApproved])
        @include('products.sections.food_pending', ['foodsPending' => $foodsPending])
        @include('products.sections.beverage_approved', ['beveragesApproved' => $beveragesApproved])
        @include('products.sections.beverage_pending', ['beveragesPending' => $beveragesPending])

    </div>

    <script>
        function showSection(id) {
            const sections = document.querySelectorAll('.product-section');
            sections.forEach(section => {
                section.style.display = (section.id === id) ? 'block' : 'none';
            });
        }
        window.onload = () => showSection('food-approved');
    </script>
    @endsection --}}

    

    @include('layout.footer')
</body>
</html>
