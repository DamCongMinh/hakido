<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý người dùng</title>
    <link rel="stylesheet" href="{{ asset('css/Admin/accounts/index.css') }}">
</head>
<body>
    @include('layout.header')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Danh sách Khách hàng --}}
    <h2>Danh sách Khách hàng</h2>
    <table border="1">
        @foreach($customers as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if(!$user->is_approved)
                        <form action="{{ route('admin.accounts.approve', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit">Duyệt</button>
                        </form>
                    @endif

                    <form action="{{ route('admin.accounts.toggle', $user->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit">
                            {{ $user->is_active ? 'Khóa' : 'Mở khóa' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.accounts.delete', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa tài khoản này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Xóa</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>

    {{-- Danh sách Nhà hàng --}}
    <h2>Danh sách Nhà hàng</h2>
    <table border="1">
        @foreach($restaurants as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if(!$user->is_approved)
                        <form action="{{ route('admin.accounts.approve', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit">Duyệt</button>
                        </form>
                    @endif

                    <form action="{{ route('admin.accounts.toggle', $user->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit">
                            {{ $user->is_active ? 'Khóa' : 'Kích hoạt' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.accounts.delete', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa tài khoản này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Xóa</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>

    {{-- Danh sách Shipper --}}
    <h2>Danh sách Shipper</h2>
    <table border="1">
        @foreach($shippers as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if(!$user->is_approved)
                        <form action="{{ route('admin.accounts.approve', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit">Duyệt</button>
                        </form>
                    @endif

                    <form action="{{ route('admin.accounts.toggle', $user->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit">
                            {{ $user->is_active ? 'Khóa' : 'Kích hoạt' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.accounts.delete', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa tài khoản này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Xóa</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>

    <a href="{{ route('admin.dashboard') }}">← Quay lại trang Admin</a>
</body>
</html>
