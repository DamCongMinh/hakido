<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm</title>
</head>
<body>
    @include('layout.header')

    <h2>Thêm Sản Phẩm Mới</h2>

    <form action="{{ route('restaurant.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label>Loại:</label>
        <select name="type" id="typeSelect">
            <option value="food" {{ $type === 'food' ? 'selected' : '' }}>Đồ ăn</option>
            <option value="beverage" {{ $type === 'beverage' ? 'selected' : '' }}>Đồ uống</option>
        </select><br><br>

        <label>Tên:</label>
        <input type="text" name="name" required><br><br>

        <label>Mô tả:</label>
        <textarea name="description"></textarea><br><br>

        <label>Giá:</label>
        <input type="number" name="price" required><br><br>

        <div id="sizeOptions" style="display: none;">
            <label>Chọn Size:</label><br>
            <input type="radio" name="size" value="S"> S
            <input type="radio" name="size" value="M"> M
            <input type="radio" name="size" value="L"> L
            <br><br>
        </div>

        <label>Hình ảnh:</label>
        <input type="file" name="image" required><br><br>

        <button type="submit">Thêm</button>
    </form>

    <script>
        const typeSelect = document.getElementById('typeSelect');
        const sizeOptions = document.getElementById('sizeOptions');

        function toggleSizeOptions() {
            if (typeSelect.value === 'beverage') {
                sizeOptions.style.display = 'block';
            } else {
                sizeOptions.style.display = 'none';
            }
        }

        typeSelect.addEventListener('change', toggleSizeOptions);

        // Gọi ngay khi load nếu có dữ liệu cũ (edit hoặc reload)
        window.onload = toggleSizeOptions;
    </script>
</body>
</html>
