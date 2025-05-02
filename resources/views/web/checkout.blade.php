<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Thanh toán</title>

    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
</head>
<body>
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h2>Xác nhận thanh toán</h2>

    <table>
        <thead>
            <tr>
                <th>Hình ảnh</th>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Tổng</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td data-label="Hình ảnh">
                        <img 
                            src="{{ asset('storage/' . $item['image']) }}" 
                            alt="{{ $item['name'] }}" 
                            style="width: 80px; height: auto;"
                            onerror="this.src='{{ asset('img/slide.png') }}'"
                        >
                    </td>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ number_format($item['price']) }}₫</td>
                    <td>{{ number_format($item['total']) }}₫</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h4>Tổng tiền: {{ number_format($totalAmount) }}₫</h4>

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf
    
        @php
            $user = auth()->user();
            $customer = $user->customer;
        @endphp

        <label>
            <input type="checkbox" id="use-default-info" checked>
            Dùng thông tin mặc định
        </label>

        <label>Họ tên người nhận</label>
        <input type="text" name="receiver_name" id="receiver_name"
            value="{{ old('receiver_name', $user->name) }}" required>

        <label>Số điện thoại</label>
        <input type="text" name="receiver_phone" id="receiver_phone"
            value="{{ old('receiver_phone', $customer->phone ?? '') }}" required>

        <label>Địa chỉ</label>
        <textarea name="receiver_address" id="receiver_address" required>{{ old('receiver_address', $customer->address ?? '') }}</textarea>



        <label>Phương thức thanh toán</label>
        <select name="payment_method" required>
            <option value="cod">Thanh toán khi nhận hàng</option>
            <option value="bank">Chuyển khoản ngân hàng</option>
            <option value="vnpay">VNPAY</option>
        </select>
    
        <!-- FIXED: safely encode items to hidden input -->
        <input type="hidden" name="items" value='@json($items)'>

        <button type="submit">Xác nhận & Thanh toán</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');
            const submitButton = form.querySelector('button[type="submit"]');
            const checkbox = document.getElementById('use-default-info');

            const defaultName = @json($user->name);
            const defaultPhone = @json($customer->phone ?? '');
            const defaultAddress = @json($customer->address ?? '');

            const nameInput = document.getElementById('receiver_name');
            const phoneInput = document.getElementById('receiver_phone');
            const addressInput = document.getElementById('receiver_address');

            checkbox.addEventListener('change', function () {
            if (this.checked) {
                nameInput.value = defaultName;
                phoneInput.value = defaultPhone;
                addressInput.value = defaultAddress;
            } else {
                nameInput.value = '';
                phoneInput.value = '';
                addressInput.value = '';
            }
            });
        
            form.addEventListener('submit', function () {
                submitButton.disabled = true;
                submitButton.textContent = 'Đang xử lý...';
            });
        });

    </script>
</body>
</html>
