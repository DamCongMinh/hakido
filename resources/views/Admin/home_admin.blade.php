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
            <li><a href="{{ route('admin.accounts.index') }}">Quản lý người dùng</a></li>
            <li><a href="{{ route('admin.orders.index') }}">Quản lý đơn hàng</a></li>
            <li><a href="{{ route('control_product') }}">Quản lý sản phẩm</a></li>
            <li><a href="{{ route('admin.content') }}">Quản lý nội dung</a></li>
            <li><a href="{{ route('admin.statistics') }}">Thống kê doanh thu và số lượng đơn hàng</a></li>
        </ul>
    </nav>
    
</body>
</html>
