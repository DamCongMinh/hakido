<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Thêm sản phẩm</title>
</head>
<body>
    @include('layout.header') {{-- Sử dụng header dùng chung --}}
    <h2>Thêm Sản Phẩm Mới</h2>
    <form action="{{ route('restaurant.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label>Loại:</label>
        <select name="type">
            <option value="food" {{ $type === 'food' ? 'selected' : '' }}>Đồ ăn</option>
            <option value="beverage" {{ $type === 'beverage' ? 'selected' : '' }}>Đồ uống</option>
        </select><br><br>

        <label>Tên:</label>
        <input type="text" name="name" required><br><br>

        <label>Mô tả:</label>
        <textarea name="description"></textarea><br><br>

        <label>Giá:</label>
        <input type="number" name="price" required><br><br>

        <label>Hình ảnh:</label>
        <input type="file" name="image" required><br><br>

        <button type="submit">Thêm</button>
    </form>

</body>
</html>