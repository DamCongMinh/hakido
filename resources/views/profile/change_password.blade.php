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
    <form action="{{ route('profile.change_password') }}" method="POST">
        @csrf
    
        @if(session('success'))
            <div>{{ session('success') }}</div>
        @endif
    
        @error('current_password') <div>{{ $message }}</div> @enderror
        @error('new_password') <div>{{ $message }}</div> @enderror
    
        <label>Mật khẩu hiện tại:</label>
        <input type="password" name="current_password" required>
    
        <label>Mật khẩu mới:</label>
        <input type="password" name="new_password" required>
    
        <label>Xác nhận mật khẩu mới:</label>
        <input type="password" name="new_password_confirmation" required>
    
        <button type="submit">Đổi mật khẩu</button>
    </form>    
    
    
</body>
</html>