<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body>
    <section id="header">
        <div class="header_container">
            <div class="header_left">
                <div class="header_left--logo">
                    <a href="{{ route('home') }}"><img src="{{ asset('img/logo.png') }}" alt=""></a>
                </div>
            </div>

            <div class="header_center">
                <div class="header_center--directory">
                    @foreach ($categories as $category)
                        <div class="directory--title">
                            <a href="{{ route('products.byCategory', ['category_id' => $category->id]) }}">
                                <p>{{ $category->name }}</p>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="header_center--classify">
                    <div class="classify-title">
                        <ul>
                            <li>đồ chay</li>
                            <li>đồ mặn</li>
                            <li>đồ khô</li>
                            <li>đồ ướt</li>
                        </ul>
                    </div>
                    <div class="classify-img">
                        <div><img src="{{ asset('img/home_img2.jpg') }}" alt=""></div>
                        <div><img src="{{ asset('img/home_img2.jpg') }}" alt=""></div>
                        <div><img src="{{ asset('img/home_img2.jpg') }}" alt=""></div>
                        <div><img src="{{ asset('img/home_img2.jpg') }}" alt=""></div>
                    </div>
                </div>
                
            </div>
            <div class="header_right">
                <form id="searchForm" action="/search" method="GET" style="position: relative;">
                    <div class="header_right--search">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" id="searchInput" name="keyword" placeholder="Tìm kiếm sản phẩm..." autocomplete="off">
                        <input type="hidden" id="searchType" name="type" value="product"> 
                    </div>
                
                    <div id="suggestionsBox" class="search-suggestions">
                        <ul id="suggestionsList"></ul>
                    </div>
                </form>
                
                
                
                <div class="header_right--email">
                    <i class="fa-solid fa-envelope"></i>
                </div>

                <div class="header_right--cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <div class="header_right--account">
                    <div class="account-icon"><i class="fa-solid fa-user"></i></div>
                    <div class="account-nav">
                        <ul>
                            
                            <!-- Nếu chưa đăng nhập -->
                            @guest
                                <li><a href="{{ route('register') }}">Đăng ký</a></li>
                                <li><a href="{{ route('login') }}">Đăng nhập</a></li>
                            @endguest

                            <!-- Nếu đã đăng nhập -->
                            @auth
                                <li>
                                    <img class="avatar" src="{{ asset('img/shiper_avt.jpg') }}" alt="Avatar" style="width: 32px; height: 32px; border-radius: 50%;">
                                    <h3>{{ Auth::user()->name }}</h3>

                                    <!-- Link cho tất cả user -->
                                    <a href="{{ route('profile.home_info') }}">Tài khoản của tôi</a>

                                    <!-- Nếu là admin -->
                                    @if (Auth::user()->role === 'admin')
                                        <a href="{{ route('admin.dashboard') }}">Quản lý của admin</a>
                                    @endif

                                    <!-- Nếu là restaurant -->
                                    @if (Auth::user()->role === 'restaurant')
                                        <a href="{{ route('restaurant.products.home') }}">Quản lý sản phẩm</a>
                                        <a href="{{ route('restaurant.statistics.index') }}">Quản lý đơn hàng</a>
                                        <a href="{{ route('restaurant.statistics.home') }}">Thống kê</a>
                                    @endif

                                    <!-- Nếu là shipper -->
                                    @if (Auth::user()->role === 'shipper')
                                        <a href="{{ route('shipper.orders.available') }}">🛒 Đơn hàng đang chờ</a>
                                        <a href="{{ route('shipper.orders.current') }}">🚚 Đơn đang giao</a>
                                        <a href="{{ route('shipper.orders.history') }}">📜 Lịch sử giao hàng</a>
                                        <a href="{{ route('shipper.orders.incomeStats') }}">💰 Thống kê thu nhập</a>
                                    @endif


                                    <!-- Nếu là customer -->
                                    @if (Auth::user()->role === 'customer')
                                        <a href="{{ route('cart.show') }}">Giỏ hàng của tôi</a>
                                        <a href="{{ route('orders.items') }}">📦 Sản phẩm đã đặt</a>
                                    @endif

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit">Đăng xuất</button>
                                    </form>
                                </li>
                            @endauth




                        </ul>                                      
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </section>
    <script src="{{ asset('js/header.js') }}"></script>
    @if (session('token') && session('user'))
    <script>
    // Lưu dữ liệu vào localStorage sau khi đăng nhập bằng Google
        localStorage.setItem('token', '{{ session('token') }}');
        localStorage.setItem('user', JSON.stringify({!! session('user') !!}));
    </script>
    @endif
</body>
</html>