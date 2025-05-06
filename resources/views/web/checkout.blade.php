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
    @include('layout.header')

    @if (!isset($restaurantTotalAmounts))
        <div class="alert alert-warning">
            Dữ liệu thanh toán chưa sẵn sàng. Vui lòng chọn sản phẩm từ giỏ hàng để tiến hành thanh toán.
        </div>
        @php return; @endphp
    @endif

    <h2>Xác nhận thanh toán</h2>
    
    @foreach ($groupedItems as $restaurantId => $items)
        <h3>Nhà hàng: {{ $restaurantNames[$restaurantId] ?? 'Không rõ' }}</h3>
        <table>
            <thead>
                <tr>
                    <th>Hình ảnh</th>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Tổng tiền sản phẩm</th>
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

        <h4>Khoảng cách: {{ number_format($restaurantDistances[$restaurantId], 2) ?? 'N/A' }} km</h4>
        <h4>Phí ship: {{ number_format($restaurantShippingFees[$restaurantId]) }}₫</h4>
        <h4>Tổng tiền tất cả sản phẩm: {{ number_format($restaurantTotalAmounts[$restaurantId]) }}₫</h4>
        <h3>Tổng cộng: {{ number_format($restaurantTotalSums[$restaurantId]) }}₫</h3>

        <hr>
    @endforeach

    <h3>Tổng tiền cần thanh toán: {{ number_format($finalTotal) }}₫</h3>


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



        <label for="payment_method">Chọn phương thức thanh toán</label>
        <select name="payment_method" id="payment_method" required>
            <option value="" disabled selected>-- Vui lòng chọn --</option>
            <option value="cod">🛵 Thanh toán khi nhận hàng (COD)</option>
            <option value="bank">🏦 Chuyển khoản ngân hàng</option>
            <option value="vnpay">💳 Thanh toán qua VNPAY</option>
        </select>

    
        <!-- gửi dũ liệu sang ordercontroller -->
        <input type="hidden" name="items" id="items-input">
        <input type="hidden" name="shipping_fees" id="shipping-fees-input">
        <input type="hidden" name="distances" id="distances-input">
        <input type="hidden" name="restaurantTotalAmounts" id="restaurant-total-amounts-input">
        <input type="hidden" name="restaurantTotalSums" id="restaurant-total-sums-input">




        



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
    
            const groupedItems = @json($groupedItems, JSON_PRETTY_PRINT);
            const shippingFees = @json($restaurantShippingFees, JSON_PRETTY_PRINT);
            const distances = @json($restaurantDistances, JSON_PRETTY_PRINT);
            const totalAmounts = @json($restaurantTotalAmounts, JSON_PRETTY_PRINT);
            const restaurantTotalSums = @json($restaurantTotalSums, JSON_PRETTY_PRINT);
    
            // Gán dữ liệu JSON có key đầy đủ vào các input hidden
            document.getElementById('items-input').value = JSON.stringify(groupedItems);
            document.getElementById('shipping-fees-input').value = JSON.stringify(shippingFees);
            document.getElementById('distances-input').value = JSON.stringify(distances);
            document.getElementById('restaurant-total-amounts-input').value = JSON.stringify(totalAmounts);
            document.getElementById('restaurant-total-sums-input').value = JSON.stringify(restaurantTotalSums);

            
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
    
            // ✅ Disable nút submit khi đang gửi form
            form.addEventListener('submit', function () {
                submitButton.disabled = true;
                submitButton.textContent = 'Đang xử lý...';
            });
        });
    </script>
    
    
</body>
</html>
