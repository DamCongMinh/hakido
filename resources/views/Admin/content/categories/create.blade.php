<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Danh mục mới</title>

    <link rel="stylesheet" href="{{ asset('css/Admin/content/slides/create.css') }}">
</head>
<body>
    @include('layout.header')
    
    <h2>➕ Thêm Danh mục mới</h2>

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        <label for="name">Tên danh mục:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="description">Mô tả:</label><br>
        <textarea id="description" name="description" rows="4"></textarea><br><br>

        <button type="submit">Thêm</button>
    </form>

    <br>
    <a href="{{ route('admin.categories.index') }}">← Quay lại danh sách danh mục</a>
</body>
</html>
