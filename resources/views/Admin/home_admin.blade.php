<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Quản Trị Admin</title>
    <link rel="stylesheet" href="{{ asset('css/Admin/home_admin.css') }}">
</head>
<body>
    @include('layout.header') {{-- Sử dụng header dùng chung --}}
    
    <h1>Chào mừng, Admin!</h1>
    
    <nav>
        <ul>
            <li><a href="{{ route('admin.accounts') }}">Quản lý người dùng</a></li>
            <li><a href="#">Quản lý đơn hàng</a></li>
            <li><a href="#">Quản lý nhà hàng</a></li>
            <li><a href="#">Quản lý shipper</a></li>
        </ul>
    </nav>
</body>
</html>
