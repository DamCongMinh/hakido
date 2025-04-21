<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>sửa sản phẩm ở đây nha bro</title>
</head>
<body>
    {{-- @include('layout.header') \ --}}
    <h2>Sửa {{ $type === 'food' ? 'Đồ ăn' : 'Đồ uống' }}</h2>
<form action="{{ route('restaurant.products.update', $item->id) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

    <label>Tên:</label>
    <input type="text" name="name" value="{{ $item->name }}" required><br><br>

    <label>Mô tả:</label>
    <textarea name="description">{{ $item->description }}</textarea><br><br>

    <label>Giá:</label>
    <input type="number" name="price" value="{{ $item->price }}" required><br><br>

    <label>Hình ảnh hiện tại:</label><br>
    <img src="{{ asset('storage/' . $item->image) }}" width="100"><br><br>

    <label>Đổi hình ảnh:</label>
    <input type="file" name="image"><br><br>

    <button type="submit">Cập nhật</button>
</form>

</body>
</html>