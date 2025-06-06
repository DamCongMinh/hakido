<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Danh sách sản phẩm</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/list_product.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    
</head>
<body>
    @include('layout.header')

    <section id="body">
        <div class="slide_list-product">
            <img src="{{ asset('img/slide_list-product.png') }}" alt="">
        </div>
        <div class="container">
            <div class="products">
                <!-- Nút mở bộ lọc -->
                <div class="filter-float" onclick="toggleFilter()">
                    <i class="fa-solid fa-filter"></i>
                </div>

                <!-- Bộ lọc -->
                <form id="filter-form" class="filter-form" action="{{ route('products.filter') }}" method="GET">
                    <div class="filter-header">
                        <h3>Bộ lọc</h3>
                        <button type="button" class="close-filter" onclick="toggleFilter()">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <select id="category-filter" name="category">
                        <option value="all">Tất cả danh mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>                    

                    <select id="type-filter" name="type">
                        <option value="all">Tất cả loại</option>
                        <option value="food" {{ request('type') == 'food' ? 'selected' : '' }}>Đồ ăn</option>
                        <option value="beverage" {{ request('type') == 'beverage' ? 'selected' : '' }}>Đồ uống</option>
                    </select>
                
                    <select id="province-filter" name="province">
                        <option value="all">Tất cả tỉnh</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province['code'] }}" 
                                {{ request('province') == $province['code'] ? 'selected' : '' }}>
                                {{ $province['name'] }}
                            </option>
                        @endforeach
                    </select>
                    
                    <select id="district-filter" name="district">
                        <option value="all">Tất cả quận huyện</option>
                        <!-- Khi load quận huyện thì cần check selected -->
                    </select>
                    
                    <select id="ward-filter" name="ward">
                        <option value="all">Tất cả phường xã</option>
                        <!-- Khi load phường xã thì cũng cần check selected -->
                    </select>

                    
                    <input type="range" name="price" id="price-filter" min="0" max="500000" step="1000"
                        value="{{ request('price', 500000) }}">
                    <span id="price-value">{{ request('price', 500000) }}</span>                    
                
                    <button type="submit" class="btn btn-primary" style="margin-top: 20px;">Lọc sản phẩm</button>
                </form>
                
                
                <!-- Danh sách sản phẩm -->
                <div class="products-title active list-products">
                    <h1>| Sản phẩm đề xuất</h1>
                    <div class="title-list grid-container">
                        @foreach ($products as $product)
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                    <a href="{{ route('product.show', ['type' => $product->type, 'id' => $product->id]) }}" class="overlay-button">
                                        <i class="fa-solid fa-magnifying-glass"></i> Xem ngay
                                    </a>
                                </div>
                
                                <div class="product-info">
                                    <h2 class="product-name">{{ $product->name }}</h2>
                                
                                    @if ($product->type === 'food')
                                        <p class="product-price">Giá mới: {{ number_format($product->new_price) }}đ</p>
                                        @if ($product->discount_percent > 0)
                                            <p class="old-price">Giá cũ: {{ number_format($product->old_price) }}đ</p>
                                        @endif
                                
                                    @elseif ($product->type === 'beverage')
                                        <p class="product-price">
                                            Giá mới: 
                                            {{ number_format($product->min_new_price) }}đ 
                                            @if ($product->min_new_price != $product->max_new_price)
                                                - {{ number_format($product->max_new_price) }}đ
                                            @endif
                                        </p>
                                        @if ($product->min_old_price != $product->max_old_price || $product->min_old_price > $product->min_new_price)
                                            <p class="old-price">
                                                Giá cũ: 
                                                {{ number_format($product->min_old_price) }}đ 
                                                @if ($product->min_old_price != $product->max_old_price)
                                                    - {{ number_format($product->max_old_price) }}đ
                                                @endif
                                            </p>
                                        @endif
                                    @endif
                                
                                    {{-- Thêm vào giỏ hàng --}}
                                    @if ($product->type == 'food')
                                    <form class="add-to-cart-form" 
                                            data-product-id="{{ $product->id }}" 
                                            data-product-type="food" 
                                            method="POST" 
                                            action="{{ route('cart.add') }}">
                                        @csrf
                                        <input type="hidden" name="type" value="food">
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn-cart">Thêm vào giỏ hàng</button>
                                    </form>
                              
                                    @elseif ($product->type == 'beverage' && isset($product->best_size))
                                    <form class="add-to-cart-form" 
                                            data-product-id="{{ $product->id }}" 
                                            data-product-type="beverage" 
                                            data-size="{{ $product->best_size }}" 
                                            method="POST" 
                                            action="{{ route('cart.add') }}">
                                        @csrf
                                        <input type="hidden" name="type" value="beverage">
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <input type="hidden" name="size" value="{{ $product->best_size }}">
                                        <button type="submit" class="btn-cart">Thêm vào giỏ hàng</button>
                                    </form>
                              
                                    @endif
                                </div>
                                
                            </div>
                        @endforeach
                    </div>
                </div>
                 
                <div class="my-pagination">
                    @if ($products->onFirstPage())
                        <span class="disabled">&laquo; Trang Trước</span>
                    @else
                        <a href="{{ $products->previousPageUrl() }}">&laquo; Trang Trước</a>
                    @endif
                
                    {{-- Hiển thị trang 1 --}}
                    @if ($products->currentPage() > 3)
                        <a href="{{ $products->url(1) }}">1</a>
                        @if ($products->currentPage() > 4)
                            <span class="dots">...</span>
                        @endif
                    @endif
                
                    {{-- Hiển thị các trang gần current page --}}
                    @for ($i = max(1, $products->currentPage() - 2); $i <= min($products->lastPage(), $products->currentPage() + 2); $i++)
                        @if ($i == $products->currentPage())
                            <span class="current">{{ $i }}</span>
                        @else
                            <a href="{{ $products->url($i) }}">{{ $i }}</a>
                        @endif
                    @endfor
                
                    {{-- Hiển thị trang cuối --}}
                    @if ($products->currentPage() < $products->lastPage() - 2)
                        @if ($products->currentPage() < $products->lastPage() - 3)
                            <span class="dots">...</span>
                        @endif
                        <a href="{{ $products->url($products->lastPage()) }}">{{ $products->lastPage() }}</a>
                    @endif
                
                    @if ($products->hasMorePages())
                        <a href="{{ $products->nextPageUrl() }}">Trang Sau &raquo;</a>
                    @else
                        <span class="disabled">Trang Sau &raquo;</span>
                    @endif
                </div>
                
        </div>
    </section>

    <script src="{{ url('js/list-products.js') }}"></script>

    <script>
        // thêm giỏ
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.add-to-cart-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault(); // Ngăn reload
        
                    const productId = this.dataset.productId;
                    const productType = this.dataset.productType;
                    const size = this.dataset.size;
                    const quantity = this.querySelector('input[name="quantity"]').value;
                    const token = this.querySelector('input[name="_token"]').value;
        
                    fetch("{{ route('cart.add') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            type: productType,
                            quantity: quantity,
                            size: size
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                        } else {
                            alert("Lỗi: " + data.message);
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert("Đã xảy ra lỗi khi thêm vào giỏ hàng.");
                    });
                });
            });
        });
    </script>
        
        

    @include('layout.footer')
</body>
</html>
