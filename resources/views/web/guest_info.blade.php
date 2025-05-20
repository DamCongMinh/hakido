<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin giao hàng</title>
</head>
<body>
    <h2>Thông tin giao hàng cho khách không đăng nhập</h2>

    <form action="{{ route('checkout.saveGuestInfo') }}" method="POST">
        @csrf

        <label>Họ tên</label>
        <input type="text" name="name" value="{{ old('name') }}" required>

        <label>Số điện thoại</label>
        <input type="text" name="phone" value="{{ old('phone') }}" required>

        <label>Địa chỉ</label>
        <textarea name="address" required>{{ old('address') }}</textarea>

        <!-- Geolocation -->
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <input type="hidden" name="province" id="province">

        <button type="submit">Tiếp tục thanh toán</button>
    </form>

    <script>
        navigator.geolocation?.getCurrentPosition(function(position) {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
        });
    </script>
</body>
</html>
