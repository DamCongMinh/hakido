<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa {{ $type === 'food' ? 'Đồ Ăn' : 'Đồ Uống' }}</title>
    <link rel="stylesheet" href="{{ asset('css/Admin/products/edit_product.css') }}">
</head>
<body>
    
    <h2>Sửa {{ $type === 'food' ? 'Đồ Ăn' : 'Đồ Uống' }}: {{ $item->name }}</h2>

    <form action="{{ $updateRoute }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label for="name">Tên:</label>
        <input type="text" name="name" value="{{ old('name', $item->name) }}" required>

        <label for="price">Giá:</label>
        <input type="number" name="price" value="{{ old('price', $item->price) }}" required>

        <label for="description">Mô tả:</label>
        <input type="text" name="description" value="{{ old('description', $item->description) }}">

        <label for="status">Trạng thái:</label>
        <select name="status">
            <option value="approved" {{ $item->status == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
            <option value="pending" {{ $item->status == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
        </select>

        <label>Ảnh hiện tại:</label><br>
        <img id="preview-image" src="{{ asset('storage/' . $item->image) }}" width="120"><br>

        <label>Chọn ảnh mới:</label>
        <input type="file" name="image" onchange="previewImage(event)"><br>

        <button type="submit">Cập nhật</button>
    </form>

    <br>
    <a href="{{ $indexRoute }}">← Quay lại danh sách</a>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                document.getElementById('preview-image').src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);

            const formData = new FormData();
            formData.append('image', event.target.files[0]);

            fetch("{{ route($type === 'food' ? 'foods.update' : 'beverages.update', $item->id) }}", {
                method: 'POST', // Dùng POST vì Laravel đang expect PUT spoofing
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Upload thành công");
                } else {
                    console.log("Lỗi upload ảnh");
                }
            });
        }
    </script>
</body>
</html>
