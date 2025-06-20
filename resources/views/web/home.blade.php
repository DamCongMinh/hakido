{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HakidoFood</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">    
</head>
<body>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('layout.header')

    <section id="home_body">
        <div class="body_container">
            <div class="home-slide">
                @foreach ($slidesData as $slide)
                @if ($slide->is_active)
                    <div class="content-slide">
                        <div class="slide-show">
                            <div class="show-left">
                                <h1>{{ $slide->title }}</h1>
                                <p>{{ $slide->description1 }}</p>
                                <p>{{ $slide->description2 }}</p>
                            </div>
                            <div class="show-right">
                                <img src="{{ $slide->image }}" alt="">
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

                <div class="nav-slide">
                    <button class="prev-btn"><i class="fa-solid fa-arrow-left"></i></button>
                    <button class="next-btn"><i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </div>

            <div class="home-product center-col">
                <div class="product-title">
                    <h1>Phục vụ tốt cho khách hàng là tiêu chí hàng đầu của chúng tôi.</h1>
                    <p>Chúng tôi có các món ăn đặc sắc cho bạn lựa chọn.</p>
                    <p>Bạn đang tìm kiếm đồ uống sao, hãy đến với chúng tôi, chúng tôi sẽ phục vụ tận tình</p>
                </div>
                <div class="product-content">
                    <div>
                        <img src="{{ asset('img/nuoc-ep-tao.png') }}" alt="">
                        <img src="{{ asset('img/slide5.jpg') }}" alt="">
                    </div>
                </div>
            </div>
            <div class="home-restaurant center-col">
                <div class="product-title">
                    <h1>Bạn muốn tìm kiếm một thị trường phù hợp để bán sản phẩm ?.</h1>
                    <p>Chúng tôi có môi trường phù hợp để bạn có thể tìm kiếm được khách hàng của mình.</p>
                    <p>Khám phá thế giới ẩm thực phong phú chỉ với một cú click!</p>
                    <p>Giải pháp kết nối nhà hàng và khách hàng một cách nhanh chóng, hiệu quả</p>
                </div>
                <div class="product-content">
                    <div class="content-img">
                        <img src="{{ asset('img/restaurant_img1.jpg') }}" alt="">
                    </div>
                    <div class="content-title">
                        <h1>Chúng tôi có rất nhiều ưu điểm để bạn lựa chọn.</h1>
                        <p>Tìm đúng thị trường – bán đúng sản phẩm – tăng trưởng bền vững</p>
                        <p>Hàng nghìn khách hàng đang chờ đón sản phẩm của bạn mỗi ngày</p>
                        <p>Quản lý đơn hàng, khách hàng, và giao hàng – tất cả trong một nền tảng duy nhất.</p>
                    </div>
                </div>
            </div>
            <div class="home-products center-col">
                <div class="product-title">
                    <h1>Từ quán ăn vỉa hè đến nhà hàng cao cấp – tất cả đều có trên nền tảng của chúng tôi.</h1>
                    <p>Đặt đồ ăn, thức uống dễ dàng – Giao hàng nhanh chóng – Trải nghiệm tuyệt vời mỗi ngày</p>
                    <p>Từ món chính đến món tráng miệng, từ trà sữa đến cà phê, tất cả đều chỉ cách bạn một cú chạm.</p>
                </div>
                <div class="products-content">
                        <img src="{{ asset('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset('img/slide5.jpg') }}" alt="">
                </div>
            </div>

            <div class="home-shiper center-col">
                <div class="product-title">
                    <h1>Bạn đang muốn kiếm thêm thu nhâp ?.</h1>
                    <p>Phù hợp với mọi độ tuổi – linh hoạt cho cả sinh viên và người đi làm.</p>
                    <p>Không cần kinh nghiệm – hướng dẫn chi tiết từ A-Z</p>
                </div>
                <div class="product-content">
                    <div class="content-img">
                        <img src="{{ asset('img/home_shiper.png') }}" alt="">
                    </div>
                    <div class="content-title">
                        <h1>Trở thành đối tác giao hàng đáng tin cậy cùng chúng tôi.</h1>
                        <p>Tăng thu nhập – nhận đơn dễ dàng – giao hàng thuận tiện</p>
                        <p>Giao hàng đúng giờ, giữ trọn trải nghiệm khách hàng.</p>
                        <p>Giải pháp giao hàng hiện đại cho người hiện đại</p>
                    </div>
                </div>
            </div>
            <div class="home-access center-col">
                <div class="product-title">
                    <h1>Đánh giá thực tế từ khách hàng và đối tác.</h1>
                    <p>Hãy xem họ nói gì về dịch vụ của chúng tôi – và đừng quên để lại ý kiến sau khi trải nghiệm!</p>
                    <p>Mỗi đơn hàng giao thành công không chỉ là một món ăn được chuyển đi, mà còn là sự hài lòng của khách hàng, nỗ lực của shipper và chất lượng từ nhà hàng.</p>
                </div>
                <div class="review-section">
                    @for ($i = 0; $i < 6; $i++)
                        <div class="review-card">
                            <div class="review-user">
                                <img src="{{ asset('img/shiper_avt.jpg') }}" alt="Ảnh người dùng">
                                <h3>Đàm Công Minh</h3>
                            </div>
                            <div class="review-content">
                                <img src="{{ asset('img/shiper_avt.jpg') }}" alt="Ảnh người dùng">
                                <div class="review-text">
                                    <h4>Đàm Công Minh</h4>
                                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dignissimos dicta dolorem explicabo fuga labore officiis accusamus ipsum sequi cumque nobis.</p>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>               
            </div>
        </div>
        
        <script src="{{ url('js/home.js') }}"></script>
        
        <script>
            const slidesData = @json($slidesData);
        </script>
    </section>



    @include('layout.footer')
</body>
</html> --}}