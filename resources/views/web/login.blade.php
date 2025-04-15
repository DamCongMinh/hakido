<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login to Hakido Food</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">   
</head>
<body>
<!-------------- Header của trang Login ---------------->
    @include('layout.header')

<!-------------- Body của trang Login ---------------->
<section id="body">
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form method="POST" id="formsign-up">
                <h1>Create Account</h1>
                <input type="text" placeholder="Name" id="signup--name" name="name">
                <input type="text" placeholder="Username" id="signup--username" name="sign-up-username">
                <input type="password" placeholder="Password" id="signup--password" name="sign-up-password">
                <input type="password" placeholder="Repassword" id="signup--repassword" name="repassword">
                <input type="email" placeholder="Email" id="email" name="email">
                <button>Đăng ký</button>
                <p id="signup-message" style="color:red;"></p>
            </form>
            

        </div>

        <div class="form-container sign-in">
            <form id="formsign-in" action="{{ route('postlogin') }}" method="POST">
                <h1>Đăng Nhập</h1>
            
                @csrf
            
                <input type="text" id="login-email" name="email" placeholder="Email" required>
                <input type="password" id="login-password" name="password" placeholder="Mật khẩu" required>
            
                <button type="submit">Đăng nhập</button>
                <p id="login-message" style="color:red;"></p>
            
                @if(session('status'))
                <p id="message"
                    style="color: red; font-weight: bold; margin-top: 10px; {{ session('status') ? '' : 'display: none !important;' }}">
                    {{ session('status') }}
                </p>
                @endif
            
                <a href="#">Quên mật khẩu?</a>
            
                <div class="social-icons">
                    <a href="#"><i class="fa-brands fa-facebook"></i> Đăng nhập với Facebook</a>
                    <a href="/login/google"><i class="fa-brands fa-google"></i> Đăng nhập với Google</a>

                </div>
            </form>
                     
              
        </div>

        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Wellcome Back!</h1>
                    <p>Enter your personal details to use all of site features</p>
                    <button class="hidden" id="login">Sign In</button>
                </div>

                <div class="toggle-panel toggle-right">
                    <h1>Hello my Friends!</h1>
                    <p>Enter your personal details to use all of site features</p>
                    <button class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>

    </div>
</section>
<script src="{{ url('js/login.js') }}"></script>
</body>
</html>