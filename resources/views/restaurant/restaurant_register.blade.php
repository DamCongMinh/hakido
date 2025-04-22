<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Thiết lập hò sơ cho nhà hàng của bạn!</title>
</head>
<body>
    <div class="container">
        <h2>Thiết lập hồ sơ nhà hàng</h2>
        <form action="{{ route('restaurant.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="restaurant_name">Tên nhà hàng</label>
                <input type="text" name="restaurant_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="address">Địa chỉ</label>
                <input type="text" name="address" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="phone">Số điện thoại</label>
                <input type="text" name="phone" class="form-control">
            </div>
            <div class="mb-3">
                <label for="description">Mô tả</label>
                <textarea name="description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label for="logo">Logo</label>
                <input type="file" name="logo" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Lưu hồ sơ</button>
        </form>
    </div>
</body>
</html>