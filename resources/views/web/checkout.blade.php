<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán</title>
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
</head>
<body>
    @include('layout.header')

    @php
        $user = auth()->user();
        $isGuest = !$user;
        $customer = $isGuest ? session('guest_customer') : ($user->customer ?? null);
    @endphp

    @if (empty($restaurantTotalAmounts))
        <div class="alert alert-warning">
            Dữ liệu thanh toán chưa sẵn sàng. Vui lòng chọn sản phẩm từ giỏ hàng để tiến hành thanh toán.
        </div>
    @else
        <h2 class="h2">Xác nhận thanh toán</h2>

        @foreach ($groupedItems as $restaurantId => $items)
            <h3 class="h3">Nhà hàng: {{ $restaurantNames[$restaurantId] ?? 'Không rõ' }}</h3>
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

            <h4 class="h4">Khoảng cách: {{ number_format($restaurantDistances[$restaurantId], 2) }} km</h4>
            <h4 class="h4">Phí ship: {{ number_format($restaurantShippingFees[$restaurantId]) }}₫</h4>
            <h4 class="h4">Tổng tiền tất cả sản phẩm: {{ number_format($restaurantTotalAmounts[$restaurantId]) }}₫</h4>
            <h3 class="h3">Tổng cộng: {{ number_format($restaurantTotalSums[$restaurantId]) }}₫</h3>

            <hr>
        @endforeach

        <h3 class="h3">Tổng tiền cần thanh toán: {{ number_format($finalTotal) }}₫</h3>

        <form id="checkout_form" action="{{ route('orders.store') }}" method="POST">
            @csrf

            @if (!$isGuest)
                <label>
                    <input type="checkbox" id="use-default-info" checked>
                    Dùng thông tin mặc định
                </label>
            @endif

            <label>Họ tên người nhận</label>
            <input type="text" name="receiver_name" id="receiver_name"
                value="{{ old('receiver_name', $isGuest ? '' : $user->name) }}" required>

            <label>Số điện thoại</label>
            <input type="text" name="receiver_phone" id="receiver_phone"
                value="{{ old('receiver_phone', $customer['phone'] ?? '') }}" required>

            <label>Địa chỉ</label>
            <textarea name="receiver_address" id="receiver_address" required>{{ old('receiver_address', $customer['address'] ?? '') }}</textarea>

            <label for="payment_method">Chọn phương thức thanh toán</label>
            <select name="payment_method" id="payment_method" required>
                <option value="" disabled selected>-- Vui lòng chọn --</option>
                <option value="cod">🛵 Thanh toán khi nhận hàng (COD)</option>
                <option value="bank">🏦 Chuyển khoản ngân hàng</option>
                <option value="vnpay">💳 Thanh toán qua VNPAY</option>
            </select>

            <input type="hidden" name="items" id="items-input">
            <input type="hidden" name="shipping_fees" id="shipping-fees-input">
            <input type="hidden" name="distances" id="distances-input">
            <input type="hidden" name="restaurantTotalAmounts" id="restaurant-total-amounts-input">
            <input type="hidden" name="restaurantTotalSums" id="restaurant-total-sums-input">

            <button type="submit">Xác nhận & Thanh toán</button>
        </form>
    @endif

    @include('layout.footer')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('checkout_form');
            const submitButton = form.querySelector('button[type="submit"]');
        
            const groupedItems = @json($groupedItems);
            const shippingFees = @json($restaurantShippingFees);
            const distances = @json($restaurantDistances);
            const totalAmounts = @json($restaurantTotalAmounts);
            const restaurantTotalSums = @json($restaurantTotalSums);
            const isGuest = @json($isGuest);
        
            document.getElementById('items-input').value = JSON.stringify(groupedItems);
            document.getElementById('shipping-fees-input').value = JSON.stringify(shippingFees);
            document.getElementById('distances-input').value = JSON.stringify(distances);
            document.getElementById('restaurant-total-amounts-input').value = JSON.stringify(totalAmounts);
            document.getElementById('restaurant-total-sums-input').value = JSON.stringify(restaurantTotalSums);
        
            if (!isGuest) {
                const defaultName = @json($user->name);
                const defaultPhone = @json($customer['phone'] ?? '');
                const defaultAddress = @json($customer['address'] ?? '');
        
                const checkbox = document.getElementById('use-default-info');
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
            }
        
            form.addEventListener('submit', function (e) {
                const paymentMethod = document.getElementById('payment_method').value;
        
                if (!paymentMethod) {
                    e.preventDefault();
                    alert('Vui lòng chọn phương thức thanh toán.');
                    return;
                }
        
                submitButton.disabled = true;
                submitButton.textContent = 'Đang xử lý...';
        
                if (paymentMethod === 'vnpay') {
                    e.preventDefault(); // Ngăn submit mặc định
                    form.action = '{{ route("vnpay.payment") }}';
                    form.submit(); // Gửi form tới controller Laravel
                }
                // Nếu không phải vnpay thì vẫn để form tự submit theo action mặc định
            });
        });
        </script>
        
    
</body>
</html>
