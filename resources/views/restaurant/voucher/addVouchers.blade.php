<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Vouchers</title>

    <link rel="stylesheet" href="{{ asset('css/Restaurant/voucher/addVoucher.css') }}">
</head>
<body>
    @include('layout.header')
    <div class="container">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <form action="{{ route('restaurant.add.voucher') }}" method="POST" id="voucherForm">
            @csrf
            <div class="form-group">
                <label for="code">Mã giảm giá:</label>
                <input type="text" id="code" name="code" class="form-control" required>
                <div class="error-message" id="code-error"></div>
            </div>
        
            <div class="form-group">
                <label for="typeSelect">Loại:</label>
                <select name="type" id="typeSelect" class="form-control" required>
                    <option value="">-- Chọn loại --</option>
                    <option value="percent">Giảm theo %</option>
                    <option value="free_shipping">Free Ship</option>
                </select>
            </div>
        
            <div class="form-group" id="percentField">
                <label for="value">Giá trị (%):</label>
                <input type="number" id="value" name="value" class="form-control" min="1" max="100" value="10">
                <div class="error-message" id="value-error"></div>
            </div>
        
            <div class="form-group">
                <label for="min_order_value">Đơn hàng tối thiểu (vnđ):</label>
                <input type="number" id="min_order_value" name="min_order_value" class="form-control" min="0" value="0">
            </div>
        
            <div class="form-group">
                <label for="start_date">Ngày bắt đầu:</label>
                <input type="date" id="start_date" name="start_date" class="form-control" required>
                <div class="error-message" id="start_date-error"></div>
            </div>
        
            <div class="form-group">
                <label for="end_date">Ngày kết thúc:</label>
                <input type="date" id="end_date" name="end_date" class="form-control" required>
                <div class="error-message" id="end_date-error"></div>
            </div>
        
            <div class="form-group">
                <label for="usage_limit">Số lượt sử dụng:</label>
                <input type="number" id="usage_limit" name="usage_limit" class="form-control" min="1" value="100">
            </div>
        
            <button type="submit" class="btn btn-primary">Tạo voucher</button>
        </form>
    </div>

    @include('layout.footer')
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('typeSelect');
            const percentField = document.getElementById('percentField');
            const valueInput = document.getElementById('value');
        
            // Cập nhật khi thay đổi loại voucher
            function updateValueFieldVisibility() {
                if (typeSelect.value === 'percent') {
                    percentField.style.display = 'block';
                    valueInput.disabled = false;
                    valueInput.required = true;
                    valueInput.min = 1;
                    valueInput.max = 100;
                    if (valueInput.value == 0) valueInput.value = 10;
                } else {
                    percentField.style.display = 'none';
                    valueInput.disabled = true;
                    valueInput.required = false;
                    valueInput.removeAttribute('min');
                    valueInput.removeAttribute('max');
                }
            }
        
            // Gọi hàm khi trang load và khi thay đổi loại
            updateValueFieldVisibility();
            typeSelect.addEventListener('change', updateValueFieldVisibility);
        
            // Xử lý submit form
            document.getElementById('voucherForm').addEventListener('submit', async function(e) {
                e.preventDefault();
        
                // Reset error messages
                document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        
                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: new FormData(this)
                    });
        
                    const data = await response.json();
        
                    if (!response.ok) {
                        // Hiển thị lỗi validation
                        if (data.errors) {
                            for (const [field, errors] of Object.entries(data.errors)) {
                                const errorElement = document.getElementById(`${field}-error`);
                                if (errorElement) {
                                    errorElement.textContent = errors.join(', ');
                                }
                            }
                        }
                        throw new Error(data.message || 'Có lỗi xảy ra');
                    }
        
                    alert(data.success || 'Tạo voucher thành công!');
                    window.location.href = '/restaurant/vouchers/home';
                } catch (error) {
                    alert(error.message);
                    console.error('Error:', error);
                }
            });
        });
    </script>
</body>
</html>