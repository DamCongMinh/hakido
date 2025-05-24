<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán thất bại</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container text-center py-5">
        <h2 class="text-danger mb-4">❌ Thanh toán thất bại</h2>
        <p>Giao dịch không thành công. Vui lòng thử lại hoặc chọn phương thức khác.</p>
        <a href="{{ url('/cart') }}" class="btn btn-warning mt-3">Quay lại giỏ hàng</a>
    </div>
</body>
</html>
