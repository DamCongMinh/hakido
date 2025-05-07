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

    @php
        $accountTypes = [
            'customers' => 'Danh sách Khách hàng',
            'restaurants' => 'Danh sách Nhà hàng',
            'shippers' => 'Danh sách Shipper',
        ];
    @endphp

    @foreach ($accountTypes as $type => $title)
        <h2>{{ $title }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($$type as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>
                            <div><strong>Trạng thái:</strong> {{ $user->is_active ? 'Đang hoạt động' : 'Bị khóa' }}</div>
                            <div><strong>Duyệt:</strong> {{ $user->is_approved ? 'Đã duyệt' : 'Chưa duyệt' }}</div>
                        </td>
                        <td>
                            {{-- Duyệt tài khoản nếu chưa duyệt --}}
                            @if (!$user->is_approved)
                                <form action="{{ route('admin.accounts.approve', $user->user_id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit">✔️ Duyệt</button>
                                </form>
                            @endif

                            {{-- Khóa / Mở khóa --}}
                            <form action="{{ route('admin.accounts.toggle', $user->user_id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit">
                                    {{ $user->is_active ? '🔒 Khóa' : '🔓 Mở khóa' }}
                                </button>
                            </form>

                            {{-- Xóa --}}
                            <form action="{{ route('admin.accounts.delete', $user->user_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa tài khoản này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit">🗑️ Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach


    <a href="{{ route('admin.dashboard') }}">← Quay lại trang Admin</a>
</body>
</html>
