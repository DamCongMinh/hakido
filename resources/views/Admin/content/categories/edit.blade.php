<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa danh mục</title>
    <link rel="stylesheet" href="{{ asset('css/Admin/content/categories/edit.css') }}">
</head>
<body>
    @include('layout.header')

    <div class="form-container">
        <h2>Sửa Danh Mục</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Tên danh mục:</label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required>
            </div>

            <div class="form-group">
                <label for="is_active">Trạng thái:</label>
                <select name="is_active" id="is_active">
                    <option value="1" {{ $category->is_active ? 'selected' : '' }}>Hiển thị</option>
                    <option value="0" {{ !$category->is_active ? 'selected' : '' }}>Ẩn</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-save">Cập nhật</button>
                <a href="{{ route('admin.content') }}" class="btn-cancel">Hủy</a>
            </div>
        </form>
    </div>
</body>
</html>
