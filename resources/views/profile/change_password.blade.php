<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Đổi mật khẩu của bạn đi</title>

    <link rel="stylesheet" href="{{ asset('css/profile/change_password.css') }}">
</head>
<body>
    @include('layout.header')

    <form action="{{ route('profile.change_password') }}" method="POST">
        @csrf

        {{-- Thông báo thành công --}}
        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Thông báo lỗi tổng quát --}}
        @if($errors->any())
            <div class="alert-error">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <label for="current_password">Mật khẩu hiện tại:</label>
        <input type="password" name="current_password" id="current_password" required>

        <label for="new_password">Mật khẩu mới:</label>
        <input type="password" name="new_password" id="new_password" required>

        <label for="new_password_confirmation">Xác nhận mật khẩu mới:</label>
        <input type="password" name="new_password_confirmation" id="new_password_confirmation" required>

        <button type="submit">Đổi mật khẩu</button>
    </form>    
</body>
    
    
</body>
</html>