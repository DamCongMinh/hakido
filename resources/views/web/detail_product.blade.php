<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
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
                    <img src="{{ asset('img/slide.png') }}" alt="product image">
                </div>
                <div class="image-slider">
                    <button class="prev-btn">&#10094;</button> <!-- Nút trái -->
                    <div class="list-img">
                        <img src="{{ asset('img/slide2.jpg') }}" alt="product image">
                        <img src="{{ asset('img/slide5.jpg') }}" alt="product image">
                        <img src="{{ asset('img/slide6.png') }}" alt="product image">
                        <img src="{{ asset('img/slide.png') }}" alt="product image">
                        <img src="{{ asset('img/slide.png') }}" alt="product image">
                        <img src="{{ asset('img/slide.png') }}" alt="product image">
                    </div>
                    <button class="next-btn">&#10095;</button> <!-- Nút phải -->
                </div>                
            </div>
            <div class="detail-product">
                <h2>Product Name</h2>
                <div class="restaurant">Tên Nhà Hàng :
                    <p class="name-restaurant">Lorem ipsum dolor sit amet consectetur adipisicing elit. Earum esse qui eaque quis voluptate nobis voluptatibus nisi voluptates distinctio molestias! Quasi modi neque doloribus adipisci inventore necessitatibus ex iure veritatis?</p>
                </div>
                <div class="descripttion">Mo ta :
                    <p class="description">Lorem, ipsum dolor sit amet consectetur adipisicing elit. Alias recusandae eos temporibus molestiae dolore non deleniti ipsam adipisci debitis accusantium ipsum, at impedit molestias tempora, autem ullam quasi. Blanditiis, illum.</p>
                </div>
                <div class="title">
                    <div class="title-left">
                        <div class="cost">
                            Giá Sản Phẩm :
                            <div class="price">
                                <p class="old-price">Giá Cũ: <span id="old-price">$100</span></p>
                                <p class="save">Tiết kiệm tới <span id="discount">20%</span></p>
                            </div>
                            <p class="new-price">Giá Mới: <span id="new-price">$80</span></p>
                        </div>
                        
                        <div class="size">
                            <p class="size-title">Size:</p>
                            <div class="size-list">
                                <div class="size-item">
                                    <input type="button" class="size-btn" value="S" data-price="80" data-old="100">
                                    <input type="button" class="size-btn" value="M" data-price="90" data-old="110">
                                    <input type="button" class="size-btn" value="L" data-price="100" data-old="120">
                                    <input type="button" class="size-btn" value="XL" data-price="110" data-old="130">
                                </div>
                            </div>    
                        </div>
                        <div class="amount">
                            <label for="quantity">Số lượng sản phẩm:</label>
                            <button class="decrease">-</button>
                            <input type="number" id="quantity" value="1" min="1" max="100">
                            <button class="increase">+</button>
                        </div>
                        
                        <div class="total-payouts">
                            <p>Tổng tiền cần thanh toán: <span id="total-amount">$80</span></p>
                        </div>                               
                        <div class="btn-nav">
                            <button class="btn-add">
                                <i class="fa fa-shopping-cart"></i>
                                Thêm vào giỏ hàng
                            </button>                
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
                <img src="{{ asset ('img/restaurant_img1.jpg') }}" alt="Restaurant Avatar" class="restaurant-avatar">
                <button class="btn-fav">Yêu Thích</button>
            </div>
            <div class="restaurant-middle">
                <h3 class="restaurant-name">mini.house123</h3>
                <p class="restaurant-status">Online 8 Phút Trước</p>
                <div class="restaurant-actions">
                    <button class="btn-chat"><i class="fa-solid fa-message"></i> Chat Ngay</button>
                    <button class="btn-view">Xem Nhà Hàng</button>
                </div>
            </div>
            <div class="restaurant-right">
                <div class="restaurant-stats">
                    <div class="stat"><span>Đánh Giá</span><strong>149,9k</strong></div>
                    <div class="stat"><span>Sản Phẩm</span><strong>50</strong></div>
                    <div class="stat"><span>Tỉ Lệ Phản Hồi</span><strong>82%</strong></div>
                    <div class="stat"><span>Thời Gian Phản Hồi</span><strong>trong vài giờ</strong></div>
                    <div class="stat"><span>Tham Gia</span><strong>6 năm trước</strong></div>
                    <div class="stat"><span>Người Theo Dõi</span><strong>539,9k</strong></div>
                </div>
            </div>
        </div>
        <div class="review-section">
            <div class="header-comment">
                <h3>ĐÁNH GIÁ SẢN PHẨM</h3>
          
                <div class="rating-summary">
                    <div class="average-rating">
                        <span class="score">4.9</span> <span>trên 5</span>
                        <div class="stars">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <div class="rating-filters">
                            <button>Tất Cả</button>
                            <button>5 Sao (712)</button>
                            <button>4 Sao (31)</button>
                            <button>3 Sao (10)</button>
                            <button>2 Sao (1)</button>
                            <button>1 Sao (5)</button>
                            <button>Có Hình Ảnh / Video (287)</button>
                            <button>Có Bình Luận (495)</button>
                        </div>
                    </div>
                </div>
            </div>
          
            <div class="review-list">
                <div class="review-container">
                    <div class="user-info">
                        <img src="{{ asset ('img/shiper_avt.jpg') }}" alt="avatar">
                        <div>
                            <strong>hnglam417</strong>
                            <div class="stars">⭐⭐⭐⭐⭐</div>
                            <div class="time">2024-06-17 10:21 | Phân loại hàng: M</div>
                        </div>
                    </div>
                    <div class="review-content">
                        <p><strong>Chất liệu:</strong> jean</p>
                        <p><strong>Màu sắc:</strong> xanh than thẫm</p>
                        <p><strong>Đúng với mô tả:</strong> chuẩn lắm luoonn</p>
                        <p>Ê lần đầu tiên t đặt quần mà t thấy cái quần này đẹp nhất trong các số quần mà t đã từng đặt. Vừa khít eo luôn mà 💞</p>
                    </div>
                    <div class="media">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                    </div>
                    <div class="likes">👍 59</div>
                </div>

                <div class="review-container">
                    <div class="user-info">
                        <img src="{{ asset ('img/shiper_avt.jpg') }}" alt="avatar">
                        <div>
                            <strong>hnglam417</strong>
                            <div class="stars">⭐⭐⭐⭐⭐</div>
                            <div class="time">2024-06-17 10:21 | Phân loại hàng: M</div>
                        </div>
                    </div>
                    <div class="review-content">
                        <p><strong>Chất liệu:</strong> jean</p>
                        <p><strong>Màu sắc:</strong> xanh than thẫm</p>
                        <p><strong>Đúng với mô tả:</strong> chuẩn lắm luoonn</p>
                        <p>Ê lần đầu tiên t đặt quần mà t thấy cái quần này đẹp nhất trong các số quần mà t đã từng đặt. Vừa khít eo luôn mà 💞</p>
                    </div>
                    <div class="media">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                    </div>
                    <div class="likes">👍 59</div>
                </div>

                <div class="review-container">
                    <div class="user-info">
                        <img src="{{ asset ('img/shiper_avt.jpg') }}" alt="avatar">
                        <div>
                            <strong>hnglam417</strong>
                            <div class="stars">⭐⭐⭐⭐⭐</div>
                            <div class="time">2024-06-17 10:21 | Phân loại hàng: M</div>
                        </div>
                    </div>
                    <div class="review-content">
                        <p><strong>Chất liệu:</strong> jean</p>
                        <p><strong>Màu sắc:</strong> xanh than thẫm</p>
                        <p><strong>Đúng với mô tả:</strong> chuẩn lắm luoonn</p>
                        <p>Ê lần đầu tiên t đặt quần mà t thấy cái quần này đẹp nhất trong các số quần mà t đã từng đặt. Vừa khít eo luôn mà 💞</p>
                    </div>
                    <div class="media">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                    </div>
                    <div class="likes">👍 59</div>
                </div>

                <div class="review-container">
                    <div class="user-info">
                        <img src="{{ asset ('img/shiper_avt.jpg') }}" alt="avatar">
                        <div>
                            <strong>hnglam417</strong>
                            <div class="stars">⭐⭐⭐⭐⭐</div>
                            <div class="time">2024-06-17 10:21 | Phân loại hàng: M</div>
                        </div>
                    </div>
                    <div class="review-content">
                        <p><strong>Chất liệu:</strong> jean</p>
                        <p><strong>Màu sắc:</strong> xanh than thẫm</p>
                        <p><strong>Đúng với mô tả:</strong> chuẩn lắm luoonn</p>
                        <p>Ê lần đầu tiên t đặt quần mà t thấy cái quần này đẹp nhất trong các số quần mà t đã từng đặt. Vừa khít eo luôn mà 💞</p>
                    </div>
                    <div class="media">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                    </div>
                    <div class="likes">👍 59</div>
                </div>

                <div class="review-container">
                    <div class="user-info">
                        <img src="{{ asset ('img/shiper_avt.jpg') }}" alt="avatar">
                        <div>
                            <strong>hnglam417</strong>
                            <div class="stars">⭐⭐⭐⭐⭐</div>
                            <div class="time">2024-06-17 10:21 | Phân loại hàng: M</div>
                        </div>
                    </div>
                    <div class="review-content">
                        <p><strong>Chất liệu:</strong> jean</p>
                        <p><strong>Màu sắc:</strong> xanh than thẫm</p>
                        <p><strong>Đúng với mô tả:</strong> chuẩn lắm luoonn</p>
                        <p>Ê lần đầu tiên t đặt quần mà t thấy cái quần này đẹp nhất trong các số quần mà t đã từng đặt. Vừa khít eo luôn mà 💞</p>
                    </div>
                    <div class="media">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                    </div>
                    <div class="likes">👍 59</div>
                </div>

                <div class="review-container">
                    <div class="user-info">
                        <img src="{{ asset ('img/shiper_avt.jpg') }}" alt="avatar">
                        <div>
                            <strong>hnglam417</strong>
                            <div class="stars">⭐⭐⭐⭐⭐</div>
                            <div class="time">2024-06-17 10:21 | Phân loại hàng: M</div>
                        </div>
                    </div>
                    <div class="review-content">
                        <p><strong>Chất liệu:</strong> jean</p>
                        <p><strong>Màu sắc:</strong> xanh than thẫm</p>
                        <p><strong>Đúng với mô tả:</strong> chuẩn lắm luoonn</p>
                        <p>Ê lần đầu tiên t đặt quần mà t thấy cái quần này đẹp nhất trong các số quần mà t đã từng đặt. Vừa khít eo luôn mà 💞</p>
                    </div>
                    <div class="media">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                    </div>
                    <div class="likes">👍 59</div>
                </div>

                <div class="review-container">
                    <div class="user-info">
                        <img src="{{ asset ('img/shiper_avt.jpg') }}" alt="avatar">
                        <div>
                            <strong>hnglam417</strong>
                            <div class="stars">⭐⭐⭐⭐⭐</div>
                            <div class="time">2024-06-17 10:21 | Phân loại hàng: M</div>
                        </div>
                    </div>
                    <div class="review-content">
                        <p><strong>Chất liệu:</strong> jean</p>
                        <p><strong>Màu sắc:</strong> xanh than thẫm</p>
                        <p><strong>Đúng với mô tả:</strong> chuẩn lắm luoonn</p>
                        <p>Ê lần đầu tiên t đặt quần mà t thấy cái quần này đẹp nhất trong các số quần mà t đã từng đặt. Vừa khít eo luôn mà 💞</p>
                    </div>
                    <div class="media">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                    </div>
                    <div class="likes">👍 59</div>
                </div>

                <div class="review-container">
                    <div class="user-info">
                        <img src="{{ asset ('img/shiper_avt.jpg') }}" alt="avatar">
                        <div>
                            <strong>hnglam417</strong>
                            <div class="stars">⭐⭐⭐⭐⭐</div>
                            <div class="time">2024-06-17 10:21 | Phân loại hàng: M</div>
                        </div>
                    </div>
                    <div class="review-content">
                        <p><strong>Chất liệu:</strong> jean</p>
                        <p><strong>Màu sắc:</strong> xanh than thẫm</p>
                        <p><strong>Đúng với mô tả:</strong> chuẩn lắm luoonn</p>
                        <p>Ê lần đầu tiên t đặt quần mà t thấy cái quần này đẹp nhất trong các số quần mà t đã từng đặt. Vừa khít eo luôn mà 💞</p>
                    </div>
                    <div class="media">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                    </div>
                    <div class="likes">👍 59</div>
                </div>

                <div class="review-container">
                    <div class="user-info">
                        <img src="{{ asset ('img/shiper_avt.jpg') }}" alt="avatar">
                        <div>
                            <strong>hnglam417</strong>
                            <div class="stars">⭐⭐⭐⭐⭐</div>
                            <div class="time">2024-06-17 10:21 | Phân loại hàng: M</div>
                        </div>
                    </div>
                    <div class="review-content">
                        <p><strong>Chất liệu:</strong> jean</p>
                        <p><strong>Màu sắc:</strong> xanh than thẫm</p>
                        <p><strong>Đúng với mô tả:</strong> chuẩn lắm luoonn</p>
                        <p>Ê lần đầu tiên t đặt quần mà t thấy cái quần này đẹp nhất trong các số quần mà t đã từng đặt. Vừa khít eo luôn mà 💞</p>
                    </div>
                    <div class="media">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                    </div>
                    <div class="likes">👍 59</div>
                </div>

                <div class="review-container">
                    <div class="user-info">
                        <img src="{{ asset ('img/shiper_avt.jpg') }}" alt="avatar">
                        <div>
                            <strong>hnglam417</strong>
                            <div class="stars">⭐⭐⭐⭐⭐</div>
                            <div class="time">2024-06-17 10:21 | Phân loại hàng: M</div>
                        </div>
                    </div>
                    <div class="review-content">
                        <p><strong>Chất liệu:</strong> jean</p>
                        <p><strong>Màu sắc:</strong> xanh than thẫm</p>
                        <p><strong>Đúng với mô tả:</strong> chuẩn lắm luoonn</p>
                        <p>Ê lần đầu tiên t đặt quần mà t thấy cái quần này đẹp nhất trong các số quần mà t đã từng đặt. Vừa khít eo luôn mà 💞</p>
                    </div>
                    <div class="media">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset ('img/slide5.jpg') }}" alt="">
                    </div>
                    <div class="likes">👍 59</div>
                </div>
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
<!-------------- Footer của trang detail ---------------->
    @include('layout.footer')    
</body>
</html>