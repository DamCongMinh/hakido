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

    // Gán dữ liệu JSON vào hidden inputs
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
});