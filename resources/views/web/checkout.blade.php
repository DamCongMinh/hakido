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
        <div class="container">
            <div class="check-product">
                <h2 class="h2">Thông tin đơn hàng</h2>

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

                    <h4 class="h4">Phí ship: {{ number_format($restaurantShippingFees[$restaurantId]) }}₫</h4>
                    <h4 class="h4">Tổng tiền tất cả sản phẩm: {{ number_format($restaurantTotalAmounts[$restaurantId]) }}₫</h4>
                    <h3 class="h3">Tổng cộng: {{ number_format($restaurantTotalSums[$restaurantId]) }}₫</h3>

                    <div class="discount-section">
                        <h4 class="h4 discount-amount text-success">
                            @if(isset($discount) && $discount > 0)
                                - {{ number_format($discount) }}₫
                            @endif
                        </h4>
                    </div>

                    

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <hr>
                @endforeach
                <h3 class="h3">Tổng tiền cần thanh toán: 
                    <span class="final-total-display">{{ number_format($finalTotal) }}₫</span>
                </h3>
            </div>

            <div class="check-info">
                <h2 class="h2">Thông tin người mua hàng</h2>
                <form id="checkout_form" method="POST">
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

                    <div class="voucher-section">
                        <label for="voucher_code">Mã giảm giá</label>
                        <input type="text" name="voucher_code" id="voucher_code">
                        <button type="button" id="apply-voucher-btn">Áp dụng</button>

                        @if (session('voucher_code_copied'))
                            <p class="text-success">
                                Đã áp dụng mã: <strong>{{ session('voucher_code_copied') }}</strong>
                            </p>
                        @endif
                    </div>

                    <label for="payment_method">Chọn phương thức thanh toán</label>
                    <select name="payment_method" id="payment_method" required>
                        <option value="cod">🛵 Thanh toán khi nhận hàng (COD)</option>
                        <option value="vnpay">💳 Thanh toán qua VNPAY</option>
                    </select>

                    {{-- Các input hidden --}}
                    <input type="hidden" name="items" id="items-input">
                    <input type="hidden" name="shipping_fees" id="shipping-fees-input">
                    <input type="hidden" name="distances" id="distances-input">
                    <input type="hidden" name="restaurantTotalAmounts" id="restaurant-total-amounts-input">
                    <input type="hidden" name="restaurantTotalSums" id="restaurant-total-sums-input">
                    <input type="hidden" name="voucher_discount" id="voucher-discount-input">
                    <input type="hidden" name="voucher_id" id="voucher_id" value="">
                    <input type="hidden" name="final_total" id="final-total-input" value="{{ $finalTotal ?? 0 }}">
                    <input type="hidden" name="voucher_code_hidden" id="voucher-code-hidden-input" value="{{ session('voucher_code_copied') }}">


                    <button type="submit" id="submit-button">Xác nhận & Thanh toán</button>
                </form>
            </div>
        </div>
    @endif

    @include('layout.footer')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('checkout_form');
            const submitButton = form.querySelector('button[type="submit"]');
            const paymentSelect = document.getElementById('payment_method');

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
                const paymentMethod = paymentSelect.value;

                if (!paymentMethod) {
                    e.preventDefault();
                    alert('Vui lòng chọn phương thức thanh toán.');
                    return;
                }

                submitButton.disabled = true;
                submitButton.textContent = 'Đang xử lý...';

                if (paymentMethod === 'vnpay') {
                    form.action = '{{ route("vnpay.payment") }}';
                } else {
                    form.action = '{{ route("orders.store") }}';
                }
            });

            const applyButton = document.getElementById('apply-voucher-btn');
            const voucherInput = document.querySelector('input[name="voucher_code"]');

            if (applyButton && voucherInput) {
                applyButton.addEventListener('click', function () {
                    const voucherCode = voucherInput.value;

                    fetch('{{ route('checkout.applyVoucher') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            voucher_code: voucherCode,
                            groupedItems: groupedItems,
                            shippingFees: shippingFees
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);

                            document.querySelectorAll('.final-total-display').forEach(el => {
                                el.textContent = new Intl.NumberFormat('vi-VN').format(data.finalTotal) + '₫';
                            });

                            const discountEl = document.querySelector('.discount-amount');
                            if (discountEl) {
                                discountEl.textContent = '- ' + new Intl.NumberFormat('vi-VN').format(data.discount) + '₫';
                            } else {
                                const newDiscountEl = document.createElement('h4');
                                newDiscountEl.className = 'h4 discount-amount text-success';
                                newDiscountEl.textContent = '- ' + new Intl.NumberFormat('vi-VN').format(data.discount) + '₫';
                                document.querySelector('.discount-section')?.appendChild(newDiscountEl);
                            }
                            document.getElementById('voucher-discount-input').value = data.discount;
                            document.getElementById('final-total-input').value = data.finalTotal;
                            document.getElementById('voucher_id').value = data.voucher_id;
                            document.getElementById('final-total-input').value = '{{ $finalTotal ?? 0 }}';
                            document.getElementById('voucher-code-hidden-input').value = '{{ session('voucher_code_copied') ?? '' }}';

                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi:', error);
                        alert('Đã có lỗi xảy ra khi áp dụng mã.');
                    });
                });
            }
        });
    </script>
</body>
</html>
