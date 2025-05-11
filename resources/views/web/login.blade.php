<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập / Đăng ký Hakido Food</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    @include('layout.header')

    <section id="body">
        <div class="container {{ session('show_signup') ? 'active' : '' }}" id="container">
            <!-- Đăng ký -->
            <div class="form-container sign-up">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <h1>Đăng ký tài khoản</h1>

                    <select name="role" required>
                        <option value="">--Chọn vai trò--</option>
                        <option value="customer">Khách hàng</option>
                        <option value="restaurant">Nhà hàng</option>
                        <option value="shipper">Shipper</option>
                    </select>

                    <input type="text" name="name" placeholder="Họ và tên" value="{{ old('name') }}">
                    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}">
                    <input type="password" name="password" placeholder="Mật khẩu">
                    <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu">

                    @if ($errors->any())
                        <div style="color: red;">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <button type="submit">Đăng ký</button>
                </form>
            </div>

            <!-- Đăng nhập -->
            <div class="form-container sign-in">
                <form method="POST" action="{{ route('postlogin') }}">
                    @csrf
                    <h1>Đăng nhập</h1>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Mật khẩu" required>

                    @if(session('status'))
                        <p style="color:red">{{ session('status') }}</p>
                    @endif

                    <button type="submit">Đăng nhập</button>
                    <a href="{{ route('password.request') }}">Quên mật khẩu?</a>

                    <div class="social-icons">
                        <a href="/login/facebook"><i class="fab fa-facebook"></i> Đăng nhập với Facebook</a>
                        <a href="/login/google"><i class="fab fa-google"></i> Đăng nhập với Google</a>
                    </div>
                </form>
            </div>

            <!-- Toggle -->
            <div class="toggle-container">
                <div class="toggle">
                    <div class="toggle-panel toggle-left">
                        <h1>Chào mừng trở lại!</h1>
                        <p>Đăng nhập để sử dụng tất cả tính năng</p>
                        <button class="hidden" id="login">Đăng nhập</button>
                    </div>
                    <div class="toggle-panel toggle-right">
                        <h1>Xin chào!</h1>
                        <p>Tạo tài khoản mới để khám phá</p>
                        <button class="hidden" id="register">Đăng ký</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('layout.footer')

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const container = document.getElementById('container');
            const registerBtn = document.getElementById('register');
            const loginBtn = document.getElementById('login');

            if (registerBtn && loginBtn && container) {
                registerBtn.addEventListener('click', () => container.classList.add("active"));
                loginBtn.addEventListener('click', () => container.classList.remove("active"));
            }
        });
    </script>
</body>
</html>
