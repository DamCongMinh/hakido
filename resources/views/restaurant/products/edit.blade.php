<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sửa sản phẩm</title>

    <link rel="stylesheet" href="{{ asset('css/product/create.css') }}">
</head>
<body>
    @include('layout.header')

    <h2>Sửa {{ $type === 'food' ? 'Đồ ăn' : 'Đồ uống' }}</h2>

    <form action="{{ route('restaurant.products.update', $item->id) }}" method="POST" enctype="multipart/form-data">
        @csrf 
        @method('PUT')

        <input type="hidden" name="type" value="{{ $type }}">

        <label>Danh mục:</label>
        <select name="category_id" required>
            <option value="">-- Chọn danh mục --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select><br><br>

        <label>Tên:</label>
        <input type="text" name="name" value="{{ $item->name }}" required><br><br>

        <label>Mô tả:</label>
        <textarea name="description">{{ $item->description }}</textarea><br><br>



        @if($type === 'beverage')
            <div id="sizeOptions">
                <label>Giá theo Size:</label><br>
                <div>
                    Size S:
                    <input type="number" name="sizes[S][old_price]" placeholder="Giá gốc Size S" value="{{ $item->sizes['S']['old_price'] ?? '' }}">
                    <input type="number" name="sizes[S][discount_percent]" placeholder="Giảm giá Size S" value="{{ $item->sizes['S']['discount_percent'] ?? '' }}">
                    <input type="number" name="sizes[S][quantity]" placeholder="Số lượng Size S" value="{{ $item->sizes['S']['quantity'] ?? '' }}">
                </div>
                <div>
                    Size M:
                    <input type="number" name="sizes[M][old_price]" placeholder="Giá gốc Size M" value="{{ $item->sizes['M']['old_price'] ?? '' }}">
                    <input type="number" name="sizes[M][discount_percent]" placeholder="Giảm giá Size M" value="{{ $item->sizes['M']['discount_percent'] ?? '' }}">
                    <input type="number" name="sizes[M][quantity]" placeholder="Số lượng Size M" value="{{ $item->sizes['M']['quantity'] ?? '' }}">
                </div>
                <div>
                    Size L:
                    <input type="number" name="sizes[L][old_price]" placeholder="Giá gốc Size L" value="{{ $item->sizes['L']['old_price'] ?? '' }}">
                    <input type="number" name="sizes[L][discount_percent]" placeholder="Giảm giá Size L" value="{{ $item->sizes['L']['discount_percent'] ?? '' }}">
                    <input type="number" name="sizes[L][quantity]" placeholder="Số lượng Size L" value="{{ $item->sizes['L']['quantity'] ?? '' }}">
                </div>
            </div><br>
        @else
            <label>Giá gốc:</label>
            <input type="number" name="old_price" min="0" step="0.01" value="{{ $item->old_price }}"><br><br>

            <label>Phần trăm giảm giá:</label>
            <input type="number" name="discount_percent" min="0" max="100" step="1" value="{{ $item->discount_percent }}"><br><br>
            
            <label>Số lượng:</label>
            <input type="number" name="quantity" min="0" value="{{ $item->quantity ?? 0 }}"><br><br>
        @endif

        <label>Hình ảnh hiện tại:</label><br>
        <img src="{{ asset('storage/' . $item->image) }}" width="100"><br><br>

        <label>Đổi hình ảnh:</label>
        <input type="file" name="image"><br><br>

        <label>Trạng thái:</label>
        <select name="is_active" required>
            <option value="1" {{ $item->is_active ? 'selected' : '' }}>Còn bán</option>
            <option value="0" {{ !$item->is_active ? 'selected' : '' }}>Dừng bán</option>
        </select><br><br>
        <br><br>

        <button type="submit">Cập nhật</button>
    </form>
</body>
</html>
