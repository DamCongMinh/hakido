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
                    <h1>Đồ ăn ngon</h1>
                    <div class="title-list">
                        @foreach ($products as $product)
                            <div class="list-show">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                <div class="show-cart">
                                    @if ($product->type == 'food')
                                        <form action="{{ route('cart.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="type" value="food">
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="show-cart" style="background: none; border: none; cursor: pointer; padding: 0;">
                                                <p><i class="fa-solid fa-cart-shopping"></i></p>
                                            </button>
                                        </form>
                                    @elseif ($product->type == 'beverage')
                                        @foreach ($product->beverageSizes as $size)
                                            <form action="{{ route('cart.add') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="type" value="beverage">
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <input type="hidden" name="size" value="{{ $size->size }}">
                                                <button type="submit" class="show-cart" style="background: none; border: none; cursor: pointer; padding: 0;">
                                                    <p><i class="fa-solid fa-cart-shopping"></i></p>
                                                </button>
                                            </form>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="show-title">
                                    <h1>{{ $product->name }}</h1>
                                    <div class="title-detail">
                                        <div class="detail-cost">
                                            @if ($product->discount_percent > 0)
                                                <p class="old-cost">Giá cũ: {{ number_format($product->old_price) }}đ</p>
                                            @endif
                                            <p class="new-cost">Giá mới: {{ number_format($product->new_price) }}đ</p>
                                        </div>
                                        <div class="title-access">
                                            Đánh giá:
                                            <p>
                                                @for ($i = 0; $i < floor($product->rating ?? 4.5); $i++)
                                                    <i class="fa-solid fa-star"></i>
                                                @endfor
                                                @if (fmod($product->rating ?? 4.5, 1) > 0)
                                                    <i class="fa-solid fa-star-half-stroke"></i>
                                                @endif
                                            </p>
                                            <p>{{ $product->rating ?? '4.5' }}/5</p>
                                            {{-- <p>Category ID: {{ $product->category_id }}</p> --}}

                                        </div>
                                    </div>
                                    <a href="{{ route('product.show', ['type' => $product->type, 'id' => $product->id]) }}">
                                        <button class="btn btn-primary">Mua ngay</button>
                                    </a>
                                    
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

    @include('layout.footer')
</body>
</html>
