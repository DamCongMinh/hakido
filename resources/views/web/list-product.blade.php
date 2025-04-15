<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/list_product.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
</head>
<body>
    <!-------------- Header của trang Product ---------------->
    @include('layout.header')

    <!-------------- Body của trang Product ---------------->
    <section id="body">
        <div class="container">
            <div class="prominient">
                <div class="prominient-product" data-index="0">
                    <div class="product-img"><img src="{{ asset('img/slide.png') }}" alt="Ảnh 1"></div>
                    <h1>Salad ngon vaix ca loan</h1>
                    <button class="btn btn-primary">Mua ngay</button>
                </div>
                <div class="prominient-product" data-index="1">
                    <div class="product-img"><img src="{{ asset('img/slide2.jpg') }}" alt="Ảnh 2"></div>
                    <h1>Salad ngon vaix ca loan</h1>
                    <button class="btn btn-primary">Mua ngay</button>
                </div>
                <div class="prominient-product main-product" data-index="2">
                    <div class="product-img"><img src="{{ asset('img/slide2.jpg') }}" alt="Ảnh 2"></div>
                    <h1>Salad ngon vaix ca loan</h1>
                    <button class="btn btn-primary">Mua ngay</button>
                </div>
                <div class="prominient-product" data-index="3">
                    <div class="product-img"><img src="{{ asset('img/slide6.png') }}" alt="Ảnh 4"></div>
                    <h1>Salad ngon vaix ca loan</h1>
                    <button class="btn btn-primary">Mua ngay</button>
                </div>
                <div class="prominient-product" data-index="4">
                    <div class="product-img"><img src="{{ asset('img/slide5.jpg') }}" alt="Ảnh 5"></div>
                    <h1>Salad ngon vaix ca loan</h1>
                    <button class="btn btn-primary">Mua ngay</button>
                </div>
            </div>
            <div class="products">
                <!-- Icon mở bộ lọc -->
                <div class="filter-float" onclick="toggleFilter()">
                    <i class="fa-solid fa-filter"></i>
                </div>                
                <div class="products-filter">
                    <!-- Bộ lọc -->
                    <div class="filter-header">
                        <h3>Bộ lọc</h3>
                        <span class="close-filter" onclick="toggleFilter()">✖</span>
                    </div>
                    <!-- Lọc theo danh mục -->
                    <div class="filter-type">
                        <label for="">Danh mục loại đồ ăn :</label>
                        <select id="category-filter">
                            <option value="all">Tất cả danh mục</option>
                            <option value="food">Đồ ăn</option>
                            <option value="drink">Đồ uống</option>
                        </select>
                    </div>
                    
                    <div class="filter-type">
                        <label for="">Danh mục loại đồ ăn :</label>
                        <select id="food-type-filter">
                            <option value="all">Tất cả loại nhà hàng</option>
                            <option value="vegetarian">sasimi</option>
                            <option value="non-vegetarian">lẩu</option>
                            <option value="seafood">Hải sản</option>
                        </select>
                    </div>

                    <!-- Lọc theo loại đồ ăn -->
                    <div class="filter-type">
                        <label for="">Danh mục loại đồ ăn :</label>
                        <select id="food-type-filter">
                            <option value="all">Tất cả loại đồ ăn</option>
                            <option value="vegetarian">Ăn chay</option>
                            <option value="non-vegetarian">Ăn mặn</option>
                            <option value="seafood">Hải sản</option>
                        </select>
                    </div>
                
                    <!-- Lọc theo địa chỉ -->
                    <div class="filter-type">
                        <label for="province-filter">Tỉnh/Thành phố:</label>
                        <select id="province-filter">
                            <option value="all">Tất cả</option>
                        </select>
                    </div>
                    
                    <div class="filter-type">
                        <label for="district-filter">Quận/Huyện:</label>
                        <select id="district-filter">
                            <option value="all">Tất cả</option>
                        </select>
                    </div>
                    
                    <div class="filter-type">
                        <label for="ward-filter">Phường/Xã:</label>
                        <select id="ward-filter">
                            <option value="all">Tất cả</option>
                        </select>
                    </div>                    
                
                    <!-- Lọc theo giá tiền -->
                    <div class="filter-cost">
                        <label for="">Danh mục loại đồ ăn :</label>
                        <div class="filter-title">
                            <input type="range" id="price-filter" min="0" max="100000" step="1000" value="100000">
                            <span id="price-value">100.000đ</span>
                        </div>
                    </div>
                
                    <!-- Nút lọc -->
                    <button onclick="applyFilter()">Lọc</button>
                </div>
                
                <div class="products-title active list-products">
                    <h1>Đồ ăn ngon</h1>
                    <div class="title-list">
                        <div class="list-show">
                            <img src="{{ asset('img/slide2.jpg') }}" alt="">
                            <div class="show-cart">
                                {{-- <p><i class="fa-solid fa-heart"></i></p> --}}
                                <p><i class="fa-solid fa-cart-shopping"></i></p>
                            </div>
                            <div class="show-title">
                                <h1>Lorem ipsum dolor sit amet consectetur.</h1>
                                <div class="title-detail">
                                    <div class="detail-cost">
                                        <p class="old-cost">Giá cũ: 40.000đ</p>
                                        <p class="new-cost">Giá mới: 26.000đ</p>
                                    </div>
                                    <div class="title-access">Đánh giá: 
                                        <p>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star-half-stroke"></i> 
                                        </p>                
                                        <p>4.5/5</p>
                                    </div>
                                </div>
                                <button class="btn btn-primary">Mua ngay</button>
                            </div>
                        </div>
                    </div>
                    {{-- test --}}
                    <div class="title-list">
                        <div class="list-show">
                            <img src="{{ asset('img/slide2.jpg') }}" alt="">
                            <div class="show-cart">
                                {{-- <p><i class="fa-solid fa-heart"></i></p> --}}
                                <p><i class="fa-solid fa-cart-shopping"></i></p>
                            </div>
                            <div class="show-title">
                                <h1>Lorem ipsum dolor sit amet consectetur.</h1>
                                <div class="title-detail">
                                    <div class="detail-cost">
                                        <p class="old-cost">Giá cũ: 40.000đ</p>
                                        <p class="new-cost">Giá mới: 26.000đ</p>
                                    </div>
                                    <div class="title-access">Đánh giá: 
                                        <p>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star-half-stroke"></i> 
                                        </p>                
                                        <p>4.5/5</p>
                                    </div>
                                </div>
                                <button class="btn btn-primary">Mua ngay</button>
                            </div>
                        </div>
                    </div>
                    <div class="title-list">
                        <div class="list-show">
                            <img src="{{ asset('img/slide2.jpg') }}" alt="">
                            <div class="show-cart">
                                {{-- <p><i class="fa-solid fa-heart"></i></p> --}}
                                <p><i class="fa-solid fa-cart-shopping"></i></p>
                            </div>
                            <div class="show-title">
                                <h1>Lorem ipsum dolor sit amet consectetur.</h1>
                                <div class="title-detail">
                                    <div class="detail-cost">
                                        <p class="old-cost">Giá cũ: 40.000đ</p>
                                        <p class="new-cost">Giá mới: 26.000đ</p>
                                    </div>
                                    <div class="title-access">Đánh giá: 
                                        <p>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star-half-stroke"></i> 
                                        </p>                
                                        <p>4.5/5</p>
                                    </div>
                                </div>
                                <button class="btn btn-primary">Mua ngay</button>
                            </div>
                        </div>
                    </div>
                    <div class="title-list">
                        <div class="list-show">
                            <img src="{{ asset('img/slide2.jpg') }}" alt="">
                            <div class="show-cart">
                                {{-- <p><i class="fa-solid fa-heart"></i></p> --}}
                                <p><i class="fa-solid fa-cart-shopping"></i></p>
                            </div>
                            <div class="show-title">
                                <h1>Lorem ipsum dolor sit amet consectetur.</h1>
                                <div class="title-detail">
                                    <div class="detail-cost">
                                        <p class="old-cost">Giá cũ: 40.000đ</p>
                                        <p class="new-cost">Giá mới: 26.000đ</p>
                                    </div>
                                    <div class="title-access">Đánh giá: 
                                        <p>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star-half-stroke"></i> 
                                        </p>                
                                        <p>4.5/5</p>
                                    </div>
                                </div>
                                <button class="btn btn-primary">Mua ngay</button>
                            </div>
                        </div>
                    </div>
                    <div class="title-list">
                        <div class="list-show">
                            <img src="{{ asset('img/slide2.jpg') }}" alt="">
                            <div class="show-cart">
                                {{-- <p><i class="fa-solid fa-heart"></i></p> --}}
                                <p><i class="fa-solid fa-cart-shopping"></i></p>
                            </div>
                            <div class="show-title">
                                <h1>Lorem ipsum dolor sit amet consectetur.</h1>
                                <div class="title-detail">
                                    <div class="detail-cost">
                                        <p class="old-cost">Giá cũ: 40.000đ</p>
                                        <p class="new-cost">Giá mới: 26.000đ</p>
                                    </div>
                                    <div class="title-access">Đánh giá: 
                                        <p>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star-half-stroke"></i> 
                                        </p>                
                                        <p>4.5/5</p>
                                    </div>
                                </div>
                                <button class="btn btn-primary">Mua ngay</button>
                            </div>
                        </div>
                    </div>
                    <div class="title-list">
                        <div class="list-show">
                            <img src="{{ asset('img/slide2.jpg') }}" alt="">
                            <div class="show-cart">
                                {{-- <p><i class="fa-solid fa-heart"></i></p> --}}
                                <p><i class="fa-solid fa-cart-shopping"></i></p>
                            </div>
                            <div class="show-title">
                                <h1>Lorem ipsum dolor sit amet consectetur.</h1>
                                <div class="title-detail">
                                    <div class="detail-cost">
                                        <p class="old-cost">Giá cũ: 40.000đ</p>
                                        <p class="new-cost">Giá mới: 26.000đ</p>
                                    </div>
                                    <div class="title-access">Đánh giá: 
                                        <p>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star-half-stroke"></i> 
                                        </p>                
                                        <p>4.5/5</p>
                                    </div>
                                </div>
                                <button class="btn btn-primary">Mua ngay</button>
                            </div>
                        </div>
                    </div>
                    <div class="title-list">
                        <div class="list-show">
                            <img src="{{ asset('img/slide2.jpg') }}" alt="">
                            <div class="show-cart">
                                {{-- <p><i class="fa-solid fa-heart"></i></p> --}}
                                <p><i class="fa-solid fa-cart-shopping"></i></p>
                            </div>
                            <div class="show-title">
                                <h1>Lorem ipsum dolor sit amet consectetur.</h1>
                                <div class="title-detail">
                                    <div class="detail-cost">
                                        <p class="old-cost">Giá cũ: 40.000đ</p>
                                        <p class="new-cost">Giá mới: 26.000đ</p>
                                    </div>
                                    <div class="title-access">Đánh giá: 
                                        <p>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star-half-stroke"></i> 
                                        </p>                
                                        <p>4.5/5</p>
                                    </div>
                                </div>
                                <button class="btn btn-primary">Mua ngay</button>
                            </div>
                        </div>
                    </div>
                    <div class="title-list">
                        <div class="list-show">
                            <img src="{{ asset('img/slide2.jpg') }}" alt="">
                            <div class="show-cart">
                                {{-- <p><i class="fa-solid fa-heart"></i></p> --}}
                                <p><i class="fa-solid fa-cart-shopping"></i></p>
                            </div>
                            <div class="show-title">
                                <h1>Lorem ipsum dolor sit amet consectetur.</h1>
                                <div class="title-detail">
                                    <div class="detail-cost">
                                        <p class="old-cost">Giá cũ: 40.000đ</p>
                                        <p class="new-cost">Giá mới: 26.000đ</p>
                                    </div>
                                    <div class="title-access">Đánh giá: 
                                        <p>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star-half-stroke"></i> 
                                        </p>                
                                        <p>4.5/5</p>
                                    </div>
                                </div>
                                <button class="btn btn-primary">Mua ngay</button>
                            </div>
                        </div>
                    </div>
                    <!-- Phân trang -->
                    <div class="pagination" style="margin-top: 20px;">
                        <button class="page-btn" data-page="1">1</button>
                        <button class="page-btn" data-page="2">2</button>
                        <button class="page-btn" data-page="3">3</button>
                    </div>
                </div>
                
            </div>
            <div class="more-products">
                <h1>Các sản phẩm tương tự</h1>
                <div class="list-products">
                    <div class="title-list">
                        <div class="list-show">
                            <img src="{{ asset('img/slide2.jpg') }}" alt="">
                            <div class="show-cart">
                                {{-- <p><i class="fa-solid fa-heart"></i></p> --}}
                                <p><i class="fa-solid fa-cart-shopping"></i></p>
                            </div>
                            <div class="show-title">
                                <h1>Lorem ipsum dolor sit amet consectetur.</h1>
                                <div class="title-detail">
                                    <div class="detail-cost">
                                        <p class="old-cost">Giá cũ: 40.000đ</p>
                                        <p class="new-cost">Giá mới: 26.000đ</p>
                                    </div>
                                    <div class="title-access">Đánh giá: 
                                        <p>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star-half-stroke"></i> 
                                        </p>                
                                        <p>4.5/5</p>
                                    </div>
                                </div>
                                <button class="btn btn-primary">Mua ngay</button>
                            </div>
                        </div>
                    </div>
                    <div class="title-list">
                        <div class="list-show">
                            <img src="{{ asset('img/slide2.jpg') }}" alt="">
                            <div class="show-cart">
                                {{-- <p><i class="fa-solid fa-heart"></i></p> --}}
                                <p><i class="fa-solid fa-cart-shopping"></i></p>
                            </div>
                            <div class="show-title">
                                <h1>Lorem ipsum dolor sit amet consectetur.</h1>
                                <div class="title-detail">
                                    <div class="detail-cost">
                                        <p class="old-cost">Giá cũ: 40.000đ</p>
                                        <p class="new-cost">Giá mới: 26.000đ</p>
                                    </div>
                                    <div class="title-access">Đánh giá: 
                                        <p>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star-half-stroke"></i> 
                                        </p>                
                                        <p>4.5/5</p>
                                    </div>
                                </div>
                                <button class="btn btn-primary">Mua ngay</button>
                            </div>
                        </div>
                    </div>
                    <div class="title-list">
                        <div class="list-show">
                            <img src="{{ asset('img/slide2.jpg') }}" alt="">
                            <div class="show-cart">
                                {{-- <p><i class="fa-solid fa-heart"></i></p> --}}
                                <p><i class="fa-solid fa-cart-shopping"></i></p>
                            </div>
                            <div class="show-title">
                                <h1>Lorem ipsum dolor sit amet consectetur.</h1>
                                <div class="title-detail">
                                    <div class="detail-cost">
                                        <p class="old-cost">Giá cũ: 40.000đ</p>
                                        <p class="new-cost">Giá mới: 26.000đ</p>
                                    </div>
                                    <div class="title-access">Đánh giá: 
                                        <p>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star-half-stroke"></i> 
                                        </p>                
                                        <p>4.5/5</p>
                                    </div>
                                </div>
                                <button class="btn btn-primary">Mua ngay</button>
                            </div>
                        </div>
                    </div>
                    <div class="title-list">
                        <div class="list-show">
                            <img src="{{ asset('img/slide2.jpg') }}" alt="">
                            <div class="show-cart">
                                {{-- <p><i class="fa-solid fa-heart"></i></p> --}}
                                <p><i class="fa-solid fa-cart-shopping"></i></p>
                            </div>
                            <div class="show-title">
                                <h1>Lorem ipsum dolor sit amet consectetur.</h1>
                                <div class="title-detail">
                                    <div class="detail-cost">
                                        <p class="old-cost">Giá cũ: 40.000đ</p>
                                        <p class="new-cost">Giá mới: 26.000đ</p>
                                    </div>
                                    <div class="title-access">Đánh giá: 
                                        <p>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star-half-stroke"></i> 
                                        </p>                
                                        <p>4.5/5</p>
                                    </div>
                                </div>
                                <button class="btn btn-primary">Mua ngay</button>
                            </div>
                        </div>
                    </div>
                    <div class="title-list">
                        <div class="list-show">
                            <img src="{{ asset('img/slide2.jpg') }}" alt="">
                            <div class="show-cart">
                                {{-- <p><i class="fa-solid fa-heart"></i></p> --}}
                                <p><i class="fa-solid fa-cart-shopping"></i></p>
                            </div>
                            <div class="show-title">
                                <h1>Lorem ipsum dolor sit amet consectetur.</h1>
                                <div class="title-detail">
                                    <div class="detail-cost">
                                        <p class="old-cost">Giá cũ: 40.000đ</p>
                                        <p class="new-cost">Giá mới: 26.000đ</p>
                                    </div>
                                    <div class="title-access">Đánh giá: 
                                        <p>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star-half-stroke"></i> 
                                        </p>                
                                        <p>4.5/5</p>
                                    </div>
                                </div>
                                <button class="btn btn-primary">Mua ngay</button>
                            </div>
                        </div>
                    </div>
                    <div class="title-list">
                        <div class="list-show">
                            <img src="{{ asset('img/slide2.jpg') }}" alt="">
                            <div class="show-cart">
                                {{-- <p><i class="fa-solid fa-heart"></i></p> --}}
                                <p><i class="fa-solid fa-cart-shopping"></i></p>
                            </div>
                            <div class="show-title">
                                <h1>Lorem ipsum dolor sit amet consectetur.</h1>
                                <div class="title-detail">
                                    <div class="detail-cost">
                                        <p class="old-cost">Giá cũ: 40.000đ</p>
                                        <p class="new-cost">Giá mới: 26.000đ</p>
                                    </div>
                                    <div class="title-access">Đánh giá: 
                                        <p>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star-half-stroke"></i> 
                                        </p>                
                                        <p>4.5/5</p>
                                    </div>
                                </div>
                                <button class="btn btn-primary">Mua ngay</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Phân trang -->
                <div class="pagination" style="margin-top: 20px;">
                    <button class="page-btn" data-page="1">1</button>
                    <button class="page-btn" data-page="2">2</button>
                    <button class="page-btn" data-page="3">3</button>
                </div>
            </div> 
        </div>
    </section>

    <script src="{{ url('js/list-products.js') }}"></script>

    <!-------------- Footer của trang list-product ---------------->
    @include('layout.footer')   
</body>
</html>
