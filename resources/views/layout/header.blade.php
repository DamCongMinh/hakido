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
                        @if ($category->is_active)
                            <div class="directory--title">
                                <a href="{{ route('products.byCategory', ['category_id' => $category->id]) }}">
                                    <p>{{ $category->name }}</p>
                                </a>
                            </div>
                        @endif
                    @endforeach
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
                
                
                <div class="header_right--cart">
                    <a href="{{ route('cart.show') }}"><i class="fa-solid fa-cart-shopping"></i></a>
                </div>

                
                <div class="header_right--email">
                    <i class="fa-solid fa-bell" style="cursor: pointer;"></i>
                
                    @if ($notifications->whereNull('read_at')->count() > 0)
                        <span class="badge notification-count">
                            {{ $notifications->whereNull('read_at')->count() }}
                        </span>
                    @endif
                
                    <ul class="notification-dropdown max-w-sm overflow-x-hidden whitespace-normal break-words" id="notification-list" style="display: none;">
                        @forelse ($notifications->take(5) as $notification)
                            <li
                                data-url="{{ route('notifications.read', $notification->id) }}"
                                class="notification-item"
                                style="cursor: pointer;"
                            >
                                {{ $notification->data['message'] ?? 'Thông báo mới' }}
                                <br>
                                <small>{{ $notification->created_at->diffForHumans() }}</small>
                            </li>
                        @empty
                            <li>Không có thông báo nào.</li>
                        @endforelse
                    </ul>
                </div>
                
                
                
                
                
                
                

                
                <div class="header_right--account">
                    <div class="account-icon"><i class="fa-solid fa-user"></i></div>
                    <div class="account-nav">
                        <ul>
                            
                            <!-- Nếu chưa đăng nhập -->
                            @guest
                                <li><a href="{{ route('login') }}"><i class="fa-solid fa-user-plus"></i> Đăng ký</a></li>
                                <li><a href="{{ route('login') }}"><i class="fa-solid fa-sign-in-alt"></i> Đăng nhập</a></li>
                            @endguest

                            <!-- Nếu đã đăng nhập -->
                            @auth
                                <li>
                                    <img
                                    class="avatar"
                                    src="{{ asset('storage/' . Auth::user()->resolved_avatar) }}"
                                    onerror="this.onerror=null;this.src='{{ asset('img/shiper_avt.jpg') }}';"
                                    alt="Avatar"
                                    style="width: 32px; height: 32px; border-radius: 50%;"
                                />
                                    <h3>{{ Auth::user()->name }}</h3>

                                    <!-- Link cho tất cả user -->
                                    {{-- <li><a href="{{ route('profile.home_info') }}"><i class="fa-solid fa-user"></i> Tài khoản của tôi</a></li> --}}

                                    <!-- Nếu là admin -->
                                    @if (Auth::user()->role === 'admin')
                                        <li><a href="{{ route('admin.accounts.index') }}"><i class="fa-solid fa-users-gear"></i> Quản lý người dùng</a></li>
                                        <li><a href="{{ route('admin.orders.index') }}"><i class="fa-solid fa-clipboard-list"></i> Quản lý đơn hàng</a></li>
                                        <li><a href="{{ route('admin.foods.index') }}"><i class="fa-solid fa-box-open"></i> Quản lý sản phẩm</a></li>
                                        <li><a href="{{ route('admin.content') }}"><i class="fa-solid fa-images"></i> Quản lý nội dung</a></li>
                                        <li><a href="{{ route('admin.home_statistics') }}"><i class="fa-solid fa-chart-line"></i> Báo cáo thống kê doanh thu & đơn hàng</a></li>
                                    @endif

                                    <!-- Nếu là restaurant -->
                                    @if (Auth::user()->role === 'restaurant')
                                        <li><a href="{{ route('profile.home_info') }}"><i class="fa-solid fa-user"></i> Tài khoản của tôi</a></li>
                                        <li><a href="{{ route('restaurant.products.home') }}"><i class="fa-solid fa-utensils"></i> Quản lý sản phẩm</a></li>
                                        <li><a href="{{ route('restaurant.statistics.index') }}"><i class="fa-solid fa-receipt"></i> Quản lý đơn hàng</a></li>
                                        <li><a href="{{ route('restaurant.statistics.home') }}"><i class="fa-solid fa-chart-pie"></i> Báo cáo thống kê doanh thu & đơn hàng</a></li>
                                    @endif

                                    <!-- Nếu là shipper -->
                                    @if (Auth::user()->role === 'shipper')
                                        <li><a href="{{ route('profile.home_info') }}"><i class="fa-solid fa-user"></i> Tài khoản của tôi</a></li>
                                        <li><a href="{{ route('shipper.orders.available') }}"><i class="fa-solid fa-cart-arrow-down"></i> Đơn hàng đang chờ</a></li>
                                        <li><a href="{{ route('shipper.orders.current') }}"><i class="fa-solid fa-truck"></i> Đơn đang giao</a></li>
                                        <li><a href="{{ route('shipper.orders.history') }}"><i class="fa-solid fa-file-invoice"></i> Lịch sử giao hàng</a></li>
                                        <li><a href="{{ route('shipper.orders.incomeStats') }}"><i class="fa-solid fa-sack-dollar"></i> Thống kê thu nhập</a></li>
                                    @endif

                                    <!-- Nếu là customer -->
                                    @if (Auth::user()->role === 'customer')
                                        <li><a href="{{ route('profile.home_info') }}"><i class="fa-solid fa-user"></i> Tài khoản của tôi</a></li>
                                        <li><a href="{{ route('cart.show') }}"><i class="fa-solid fa-shopping-cart"></i> Giỏ hàng của tôi</a></li>
                                        <li><a href="{{ route('orders.items') }}"><i class="fa-solid fa-box"></i> Sản phẩm đã đặt</a></li>
                                    @endif

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"><i class="fa-solid fa-sign-out-alt"></i> Đăng xuất</button>
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
    <script src="{{ asset('js/notification.js') }}"></script>
    @if (session('token') && session('user'))
    <script>
    // Lưu dữ liệu vào localStorage sau khi đăng nhập bằng Google
        localStorage.setItem('token', '{{ session('token') }}');
        localStorage.setItem('user', JSON.stringify({!! session('user') !!}));
    </script>

    @endif
</body>
</html>