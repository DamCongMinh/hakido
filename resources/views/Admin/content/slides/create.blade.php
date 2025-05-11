<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thêm Slide mới</title>

    <link rel="stylesheet" href="{{ asset('css/Admin/content/slides/create.css') }}">

</head>
<body>
    <h2>Thêm Slide mới</h2>
    <form action="{{ route('admin.slides.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label>Tiêu đề:</label>
        <input type="text" name="title" required><br>

        <label>Mô tả 1:</label>
        <input type="text" name="description1"><br>

        <label>Mô tả 2:</label>
        <input type="text" name="description2"><br>

        <label>Hình ảnh:</label>
        <div class="image-preview-wrapper">
            <img id="preview-image" src="#" alt="Chưa có ảnh" class="image-preview">
        </div>
        <input type="file" name="image" required onchange="previewImage(event)"><br>

        <label>Trạng thái:</label>
        <select name="is_active">
            <option value="1">Hiển thị</option>
            <option value="0">Ẩn</option>
        </select><br>

        <button type="submit">Lưu</button>
    </form>
    <a href="{{ route('admin.slides.index') }}">← Quay lại danh sách slide</a>

    @include('layout.footer')

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const preview = document.getElementById('preview-image');
                preview.src = reader.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
    
</body>
</html>
