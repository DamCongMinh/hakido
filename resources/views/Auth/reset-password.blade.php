<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/Auth/auth.css') }}">


    <title>Đặt lại mật khẩu</title>
</head>
<body>
    <x-guest-layout>
        <h2>Đặt lại mật khẩu</h2>
    
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
    
            <input type="hidden" name="token" value="{{ $token }}" />
            <input type="email" name="email" placeholder="Email" required value="{{ old('email') }}" />
            <input type="password" name="password" placeholder="Mật khẩu mới" required />
            <input type="password" name="password_confirmation" placeholder="Xác nhận mật khẩu" required />
    
            @error('email') <span>{{ $message }}</span> @enderror
            @error('password') <span>{{ $message }}</span> @enderror
    
            <button type="submit">Đặt lại mật khẩu</button>
        </form>
    </x-guest-layout>

    @include('layout.footer')
    
</body>
</html>