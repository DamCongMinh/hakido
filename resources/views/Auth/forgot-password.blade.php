<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/Auth/auth.css') }}">
    <title>Quên mật khẩu</title>
</head>
<body>
    <x-guest-layout>
        <h2>Quên mật khẩu</h2>
    
        @if (session('status'))
            <div>{{ session('status') }}</div>
        @endif
    
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <input type="email" name="email" placeholder="Email" required />
            @error('email') <span>{{ $message }}</span> @enderror
            <button type="submit">Gửi link đặt lại mật khẩu</button>
        </form>
    </x-guest-layout>
    
</body>
</html>