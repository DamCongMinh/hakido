<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm</title>

    <link rel="stylesheet" href="{{ asset('css/product/create.css') }}">
</head>
<body>
    @include('layout.header')

    <h2>Thêm Sản Phẩm Mới</h2>

    <form id="product_create-form" action="{{ route('restaurant.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf   

        <label>Loại:</label>
        <select name="type" id="typeSelect">
            <option value="food" {{ $type === 'food' ? 'selected' : '' }}>Đồ ăn</option>
            <option value="beverage" {{ $type === 'beverage' ? 'selected' : '' }}>Đồ uống</option>
        </select><br><br>

        <label>Danh mục:</label>
        <select name="category_id" required>
            <option value="">-- Chọn danh mục --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select><br><br>

        <label>Tên:</label>
        <input type="text" name="name" required><br><br>

        <label>Mô tả:</label>
        <textarea name="description"></textarea><br><br>

        <div id="foodFields" style="display: none;">
            <label>Giá gốc:</label>
            <input type="number" name="old_price" min="0" step="0.01"><br><br>
        
            <label>Phần trăm giảm giá:</label>
            <input type="number" name="discount_percent" min="0" max="100" step="1"><br><br>

            <label>Số lượng:</label>
            <input type="number" name="quantity" min="0"><br><br>

        </div>        


        <div id="sizeOptions" style="display: none;">
            <label>Chọn Giá theo Size:</label><br>
            <div>
                Size S:
                <input type="number" name="sizes[S][old_price]" placeholder="Giá gốc Size S">
                <input type="number" name="sizes[S][discount_percent]" placeholder="Giảm giá Size S">
                <input type="number" name="sizes[S][quantity]" placeholder="Số lượng Size S">
            </div>
            <div>
                Size M:
                <input type="number" name="sizes[M][old_price]" placeholder="Giá gốc Size M">
                <input type="number" name="sizes[M][discount_percent]" placeholder="Giảm giá Size M">
                <input type="number" name="sizes[M][quantity]" placeholder="Số lượng Size M">
            </div>
            <div>
                Size L:
                <input type="number" name="sizes[L][old_price]" placeholder="Giá gốc Size L">
                <input type="number" name="sizes[L][discount_percent]" placeholder="Giảm giá Size L">
                <input type="number" name="sizes[L][quantity]" placeholder="Số lượng Size L">
            </div>
        </div>

        <!-- Ảnh preview -->
        <img id="imagePreview" src="#" alt="Ảnh xem trước" style="display:none; max-height: 200px;"><br><br>

        <label>Hình ảnh:</label>
        <input type="file" name="image" id="imageInput" required><br><br>


        <button type="submit">Thêm</button>
    </form>


    @include('layout.footer')

    <script>
        const typeSelect = document.getElementById('typeSelect');
        const sizeOptions = document.getElementById('sizeOptions');
        const foodFields = document.getElementById('foodFields');
    
        function toggleSizeOptions() {
            if (typeSelect.value === 'beverage') {
                sizeOptions.style.display = 'block';
                foodFields.style.display = 'none';
            } else {
                sizeOptions.style.display = 'none';
                foodFields.style.display = 'block';
            }
        }
    
        // Khi trang load lần đầu
        window.onload = function() {
            toggleSizeOptions();
        };
    
        // Khi thay đổi select
        typeSelect.addEventListener('change', toggleSizeOptions);

        document.getElementById('imageInput').addEventListener('change', function (event) {
            const preview = document.getElementById('imagePreview');
            const file = event.target.files[0];

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };

                reader.readAsDataURL(file);
            } else {
                preview.src = '#';
                preview.style.display = 'none';
            }
        });
    </script>
    
</body>
</html>
