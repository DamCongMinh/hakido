<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chi tiết sản phẩm</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/detail_product.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">   
</head>
<body>
<!-------------- Header của trang detail ---------------->
    @include('layout.header')

<!-------------- Body của trang detail ---------------->
    <section id="body">
        <div class="container">
            <div class="infor-product">
                <div class="product-show">
                    <img 
                        src="{{ asset('storage/' . $product->image) }}" 
                        alt="{{ $product->name }}"
                        onerror="this.src='{{ asset('img/slide.png') }}'"
                    >
                </div>
                
                <div class="image-slider">
                    <button class="prev-btn">&#10094;</button> <!-- Nút trái -->
                    <div class="list-img">
                        @foreach ($allProducts as $p)
                            <img 
                                src="{{ $p['image'] }}" 
                                alt="{{ $p['name'] }}" 
                                class="product-thumbnail"
                                data-id="{{ $p['id'] }}"
                                data-type="{{ $p['type'] }}"
                            >
                        @endforeach
                    </div>
                    
                    
                    <button class="next-btn">&#10095;</button> <!-- Nút phải -->
                </div>                
            </div>
            <div class="detail-product">
                <h2>{{ $product->name }}</h2>
                <div class="restaurant">Tên Nhà Hàng :
                    <p class="name-restaurant">{{ $product->restaurant->name ?? 'Không rõ' }}</p>
                </div>
                <div class="descripttion">Mô tả :
                    <div class="descripttion_title">
                        <p class="description">{{ $product->description }}</p>
                    </div>
                    <button class="toggle-btn" id="toggleBtn">
                        <i class="fa fa-chevron-down"></i>
                    </button>
                </div>
                <div class="title">
                    <div class="title-left">
                        <div class="cost">
                            Giá Sản Phẩm :
                            <div class="price">
                                <p class="old-price">Giá Cũ:
                                    <span id="old-price">
                                        @if ($type === 'food')
                                            {{ number_format($product->old_price ?? 0) }}₫
                                        @else
                                            {{ number_format($product->beverageSizes[0]->old_price ?? 0) }}₫
                                        @endif
                                    </span>
                                </p>
                                <p class="save">Tiết kiệm tới
                                    <span id="discount">
                                        @if ($type === 'food')
                                            {{ $product->discount_percent ?? 0 }}%
                                        @else
                                            {{ $product->beverageSizes[0]->discount_percent ?? 0 }}%
                                        @endif
                                    </span>
                                </p>
                            </div>
                            
                            <p class="new-price">Giá Mới:
                                <span id="new-price">
                                    @if ($type === 'food')
                                        {{ number_format(($product->old_price ?? 0) * (100 - ($product->discount_percent ?? 0)) / 100) }}₫                                 
                                    @endif
                                </span>
                            </p>
                            
                        </div>
                    
                        @if ($type === 'beverage' && $product->beverageSizes)
                            <div class="size">
                                <p class="size-title">Size:</p>
                                <div class="size-list">
                                    <div class="size-item">
                                        @foreach($product->beverageSizes as $size)
                                            @php
                                                $price = $size->old_price * (100 - $size->discount_percent) / 100;
                                            @endphp
                                            <input 
                                                type="button" 
                                                class="size-btn"
                                                value="{{ $size->size }}"
                                                data-price="{{ $price }}" 
                                                data-old="{{ $size->old_price }}" 
                                                data-discount="{{ $size->discount_percent }}"
                                                data-quantity="{{ $size->quantity }}"
                                            >
                                        @endforeach

                                    </div>
                                </div>
                            </div>
                        @endif

                    
                        <div class="amount">
                            <label for="quantity">Số lượng sản phẩm:</label>
                            <button class="decrease">-</button>
                            <input type="number" id="quantity" value="1" min="1"
                                max="{{ $type === 'food' ? $product->quantity : ($product->beverageSizes[0]->quantity ?? 1) }}">
                            <button class="increase">+</button>
                        </div>                        
                    
                        <div class="total-payouts">
                            <p>Tổng tiền cần thanh toán: <span id="total-amount">{{ number_format($product->price ?? 0) }}₫</span></p>
                        </div>                               
                        <div class="btn-nav">
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="{{ $type }}">
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" id="form-quantity" value="1">
                                @if($type === 'beverage')
                                    <input type="hidden" name="size" id="selected-size" value="">
                                @endif
                                <button type="submit" class="btn-add">
                                    <i class="fa fa-shopping-cart"></i> Thêm vào giỏ hàng
                                </button>

                            </form>

                            @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                                           
                            <button class="btn-buy"> Mua hàng</button>
                        </div>
                    </div>
                    <div class="title-right">
                        <div class="coupon-card">
                            <div class="coupon-header">
                                <h2>GIẢM 20%</h2>
                                <p>Áp dụng cho đơn hàng từ 200K</p>
                            </div>
                            <div class="coupon-body">
                                <span class="coupon-code">SALE20</span>
                                <button class="copy-btn" onclick="copyCoupon('SALE20')">Sao chép</button>
                            </div>
                            <div class="coupon-footer">
                                <p>Hạn dùng: 31/12/2025</p>
                            </div>
                        </div> 
                        <div class="coupon-card">
                            <div class="coupon-header">
                                <h2>GIẢM 20%</h2>
                                <p>Áp dụng cho đơn hàng từ 200K</p>
                            </div>
                            <div class="coupon-body">
                                <span class="coupon-code">SALE20</span>
                                <button class="copy-btn" onclick="copyCoupon('SALE20')">Sao chép</button>
                            </div>
                            <div class="coupon-footer">
                                <p>Hạn dùng: 31/12/2025</p>
                            </div>
                        </div> 
                        <div class="coupon-card">
                            <div class="coupon-header">
                                <h2>GIẢM 20%</h2>
                                <p>Áp dụng cho đơn hàng từ 200K</p>
                            </div>
                            <div class="coupon-body">
                                <span class="coupon-code">SALE20</span>
                                <button class="copy-btn" onclick="copyCoupon('SALE20')">Sao chép</button>
                            </div>
                            <div class="coupon-footer">
                                <p>Hạn dùng: 31/12/2025</p>
                            </div>
                        </div> 
                    </div>
                </div>                   
            </div>
        </div>
        <div class="info-restaurant">
            <div class="restaurant-left">
                <img 
                    src="{{ asset('storage/' . $product->restaurant->avatar) }}" 
                    alt="{{ $product->restaurant->name }}"
                    onerror="this.src='{{ asset('img/restaurant_img1.jpg') }}'"
                    class="restaurant-avatar"
                />
                <button class="btn-fav">Yêu Thích</button>
            </div>
            <div class="restaurant-middle">
                <h3 class="restaurant-name">{{ $product->restaurant->name }}</h3>

                @php
                    $restaurant = $product->restaurant;
                    $lastActive = $restaurant->last_active_at;
                    $isOnline = $lastActive && $lastActive->gt(now()->subMinutes(10));
                    $totalProducts = $restaurant->foods_count + $restaurant->beverages_count;
                @endphp

                <p class="restaurant-status">
                    @if ($isOnline)
                        <span style="color: green;">Đang hoạt động</span>
                    @elseif ($lastActive)
                        Online {{ $lastActive->diffForHumans() }}
                    @else
                        Chưa có hoạt động gần đây
                    @endif
                </p>

                <div class="restaurant-actions">
                    <button class="btn-chat"><i class="fa-solid fa-message"></i> Chat Ngay</button>
                    <button class="btn-view">Xem Nhà Hàng</button>
                </div>
            </div>
            <div class="restaurant-right">
                <div class="restaurant-stats">
                    <div class="stat">
                        <span>Đánh Giá</span>
                        <strong>{{ number_format($totalRestaurantReviews) }}</strong>
                    </div>
                    
                    <div class="stat">
                        <span>Sản Phẩm</span>
                        <strong>{{ $totalProducts }}</strong>
                    </div>
                    {{-- <div class="stat">
                        <span>Tỉ Lệ Phản Hồi</span>
                        <strong>{{ $restaurantStats['reply_rate'] }}</strong>
                    </div>
                    <div class="stat">
                        <span>Thời Gian Phản Hồi</span>
                        <strong>{{ $restaurantStats['reply_time'] }}</strong>
                    </div> --}}
                    <div class="stat">
                        <span>Tham Gia</span>
                        <strong>{{ $restaurantStats['joined'] }}</strong>
                    </div>
                    <div class="stat">
                        <span>Người Theo Dõi</span>
                        <strong>{{ number_format($restaurantStats['follower_count']) }}</strong>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="review-section">
            <div class="header-comment">
                <h3>ĐÁNH GIÁ SẢN PHẨM</h3>
          
                <div class="rating-summary">
                    <div class="average-rating">
                        <div class="average-rating mb-3">
                            @if ($productAvgRating !== null)
                                <span class="score">{{ number_format($productAvgRating, 1) }}</span> <span>trên 5</span>
                            @else
                                <span class="text-muted">Chưa có đánh giá</span>
                            @endif
                        </div>
                        
                        
                        <div class="stars">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <div class="rating-filters mb-4">
                            <button data-filter="all" class="active">Tất Cả ({{ $filters['all'] }})</button>
                            <button data-filter="5">5 Sao ({{ $filters['5'] }})</button>
                            <button data-filter="4">4 Sao ({{ $filters['4'] }})</button>
                            <button data-filter="3">3 Sao ({{ $filters['3'] }})</button>
                            <button data-filter="2">2 Sao ({{ $filters['2'] }})</button>
                            <button data-filter="1">1 Sao ({{ $filters['1'] }})</button>
                            <button data-filter="with_media">Có Hình Ảnh / Video ({{ $filters['with_media'] }})</button>
                            <button data-filter="with_comment">Có Bình Luận ({{ $filters['with_comment'] }})</button>
                        </div>
                    </div>
                </div>
            </div>
          
            <div class="review-list">
                @forelse ($reviews as $review)
                    <div class="review-container mb-3 p-3 border rounded"
                         data-rating="{{ $review->rating }}"
                         data-has-media="{{ isset($review->media) && count($review->media) ? '1' : '0' }}"
                         data-has-comment="{{ trim($review->comment) ? '1' : '0' }}">
            
                        <div class="d-flex align-items-center mb-2">
                            <img class="avatar me-2"
                                 src="{{ asset('storage/' . $review->customer->resolved_avatar) }}"
                                 onerror="this.onerror=null;this.src='{{ asset('img/shiper_avt.jpg') }}';"
                                 alt="Avatar"
                                 style="width: 42px; height: 42px; border-radius: 50%;">
                            <strong>{{ $review->customer->name ?? 'Ẩn danh' }}</strong>
                        </div>

                        <div>
                            <div class="stars mb-2">
                                {{ str_repeat('⭐', $review->rating) }}
                            </div>
                            <div class="time mb-2">{{ $review->created_at->format('Y-m-d H:i') }}</div>
                        </div>

                        @if ($review->comment)
                        <strong class="mb-2">Nhận xét:</strong>
                            <div class="mb-2">
                                {{ $review->comment }}
                            </div>
                        @endif
                    </div>
                @empty
                    <p>Chưa có đánh giá nào.</p>
                @endforelse
            </div>

            <!-- Phân trang -->
            <div class="pagination" style="margin-top: 20px;">
                <button class="page-btn" data-page="1">1</button>
                <button class="page-btn" data-page="2">2</button>
                <button class="page-btn" data-page="3">3</button>
            </div>
              
        </div>   
    </section>
    <script src="{{ url('js/detail_product.js') }}"></script>
    <script>
        const allProducts = @json($allProducts);
    </script>
    
<!-------------- Footer của trang detail ---------------->
    @include('layout.footer')    
</body>
</html>