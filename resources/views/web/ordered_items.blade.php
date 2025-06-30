<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sản phẩm đã đặt</title>
    <link rel="stylesheet" href="{{ asset('css/order_items.css') }}">
</head>
<body>
    @include('layout.header')

    <div class="container mt-5">
        @if(session('success'))
            <script>
                alert("{{ session('success') }}");
            </script>
        @endif

        @if(session('error'))
            <script>
                alert("{{ session('error') }}");
            </script>
        @endif


        <h1 class="mb-4">Sản phẩm đã đặt</h1>

        @if ($orders->isEmpty())
            <p>Bạn chưa đặt sản phẩm nào.</p>
        @else

            <form method="GET" action="{{ route('orders.items') }}" class="filter-form mb-4">
                <div class="filter_controller row g-2 align-items-center">
                    <div class="col-auto">
                        <label for="status" class="form-label mb-0 fw-bold">Trạng thái:</label>
                        <select name="status" id="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Tất cả trạng thái</option>
                            @foreach (\App\Models\Order::statusMapping() as $key => $value)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
            
                    <div class="col-auto">
                        <label for="from_date" class="form-label mb-0 fw-bold">Từ ngày:</label>
                        <input type="date" name="from_date" id="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
                    </div>
            
                    <div class="col-auto">
                        <label for="to_date" class="form-label mb-0 fw-bold">Đến ngày:</label>
                        <input type="date" name="to_date" id="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
                    </div>
            
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm">Lọc</button>
                        <a href="{{ route('orders.items') }}" class="btn btn-primary btn-sm">Xóa lọc</a>
                    </div>
                </div>
            </form>
        
        
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#Đơn hàng</th>
                        <th>Ngày đặt</th>
                        <th>Nhà hàng</th>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>                       
                        <th>Loại</th>
                        <th>SL</th>
                        <th>Giá</th>
                        <th>Thành tiền</th>
                        <th>Tổng cộng</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        @php $itemCount = $order->orderItems->count(); @endphp
                        @foreach ($order->orderItems as $index => $item)
                            <tr>
                                @if ($index === 0)
                                    <td rowspan="{{ $itemCount }}">{{ $order->id }}</td>
                                    <td rowspan="{{ $itemCount }}">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td rowspan="{{ $itemCount }}">{{ $order->restaurant->name ?? 'Không rõ' }}</td>
                                @endif
                                <td>
                                    @php
                                        $product = null;
                                        if ($item->product_type === 'food') {
                                            $product = $foods[$item->product_id] ?? null;
                                        } elseif ($item->product_type === 'beverage') {
                                            $product = $beverages[$item->product_id] ?? null;
                                        }
                                    @endphp

                                    @if ($product)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" width="80">
                                    @endif
                                </td>   
                
                                <td>{{ $item->product_name }}</td>                                                         
                                <td>{{ $item->product_type }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price) }}₫</td>
                                <td>{{ number_format($item->total_price) }}₫</td>
                
                                @if ($index === 0)
                                    <td rowspan="{{ $itemCount }}">
                                        {{ number_format($order->total) }}₫<br>
                                        <small>(Phí ship: {{ number_format($order->shipping_fee) }}₫)</small>
                                    </td>
                                    <td rowspan="{{ $itemCount }}">
                                        {{ \App\Models\Order::statusMapping()[$order->status] ?? ucfirst($order->status) }}
                                    </td>
                                    <td rowspan="{{ $itemCount }}">
                                        @if ($order->status === 'pending')
                                            <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn này không?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-danger btn-sm">Hủy đơn</button>
                                            </form>
                                        @elseif ($order->status === 'completed')
                                            <a href="{{ route('reviews.reviews', ['orderId' => $order->id]) }}" class="btn btn-primary btn-sm">Đánh giá đơn hàng</a>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>                
            </table>
        
        
        @endif
    </div>

    @include('layout.footer')
</body>
</html>
