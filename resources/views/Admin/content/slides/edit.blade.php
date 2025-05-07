<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sửa Slide</title>

    <link rel="stylesheet" href="{{ asset('css/Admin/content/categories/edit.css') }}">
</head>
<body>

    @include('layout.header')
    
    <h2>Sửa Slide</h2>
    <form class="form-container" action="{{ route('admin.slides.update', $slide->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label>Tiêu đề:</label>
        <input type="text" name="title" value="{{ $slide->title }}" required><br>

        <label>Mô tả 1:</label>
        <input type="text" name="description1" value="{{ $slide->description1 }}"><br>

        <label>Mô tả 2:</label>
        <input type="text" name="description2" value="{{ $slide->description2 }}"><br>

        <label>Hình ảnh hiện tại:</label><br>
        <img id="preview-image" src="{{ asset($slide->image_path) }}" width="100"><br>

        <label>Chọn ảnh mới (nếu có):</label>
        <input type="file" name="image" onchange="previewImage(event)"><br>


        <label>Trạng thái:</label>
        <select name="is_active">
            <option value="1" {{ $slide->is_active ? 'selected' : '' }}>Hiển thị</option>
            <option value="0" {{ !$slide->is_active ? 'selected' : '' }}>Ẩn</option>
        </select><br>

        <button type="submit">Cập nhật</button>
    </form>
    <a href="{{ route('admin.slides.index') }}">← Quay lại danh sách slide</a>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('preview-image');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
    
</body>
</html>
