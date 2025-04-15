<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">    
</head>
<body>
    <meta name="csrf-token" content="{{ csrf_token() }}">
<!-------------- Header của trang home ---------------->
    @include('layout.header')

<!-------------- Body của trang home ---------------->
    <section id="home_body">
        <div class="body_container">
            <div class="home-slide">
                <div class="content-slide">
                    <div class="slide-show">
                        <div class="show-left">
                            <h1>Phở ngon cung đình, tâm tình với bạn</h1>
                            <p>Nước dùng đậm đà , sợi phở tươi ngon</p>
                            <p>chất lượng tốt</p>
                            <a href="{{ route('detail') }}" class="btn">Order now</a>
                        </div>
                        <div class="show-right">
                            <img src="{{ asset('img/slide.png') }}" alt="">
                        </div>
                    </div>
                </div>

                <div class="nav-slide">
                    <button class="prev-btn"><i class="fa-solid fa-arrow-left"></i></button>
                    <button class="next-btn"><i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </div>

            <div class="home-product center-col">
                <div class="product-title">
                    <h1>Lorem ipsum dolor sit amet consectetur.</h1>
                    <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Inventore ut quibusdam fuga.</p>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Eum qui voluptatem quae quos error atque officiis!</p>
                </div>
                <div class="product-content">
                    <div>
                        <img src="{{ asset('img/slide4.jpg') }}" alt="">
                        <img src="{{ asset('img/slide5.jpg') }}" alt="">
                    </div>
                </div>
            </div>
            <div class="home-restaurant center-col">
                <div class="product-title">
                    <h1>Lorem ipsum dolor sit amet consectetur.</h1>
                    <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Inventore ut quibusdam fuga.</p>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Eum qui voluptatem quae quos error atque officiis!</p>
                </div>
                <div class="product-content">
                    <div class="content-img">
                        <img src="{{ asset('img/restaurant_img1.jpg') }}" alt="">
                    </div>
                    <div class="content-title">
                        <h1>Lorem ipsum dolor, sit amet consectetur adipisicing.</h1>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Vitae, eum!</p>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Vitae, eum!</p>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Vitae, eum!</p>
                    </div>
                </div>
            </div>
            <div class="home-products center-col">
                <div class="product-title">
                    <h1>Lorem ipsum dolor sit amet consectetur.</h1>
                    <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Inventore ut quibusdam fuga.</p>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Eum qui voluptatem quae quos error atque officiis!</p>
                </div>
                <div class="products-content">
                    <div>
                        <img src="{{ asset('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset('img/slide5.jpg') }}" alt="">
                        <img src="{{ asset('img/slide5.jpg') }}" alt="">
                    </div>
                </div>
            </div>

            <div class="home-shiper center-col">
                <div class="product-title">
                    <h1>Lorem ipsum dolor sit amet consectetur.</h1>
                    <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Inventore ut quibusdam fuga.</p>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Eum qui voluptatem quae quos error atque officiis!</p>
                </div>
                <div class="product-content">
                    <div class="content-img">
                        <img src="{{ asset('img/home_shiper.png') }}" alt="">
                    </div>
                    <div class="content-title">
                        <h1>Lorem ipsum dolor, sit amet consectetur adipisicing.</h1>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Vitae, eum!</p>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Vitae, eum!</p>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Vitae, eum!</p>
                    </div>
                </div>
            </div>
            <div class="home-access center-col">
                <div class="product-title">
                    <h1>Lorem ipsum dolor sit amet consectetur.</h1>
                    <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Inventore ut quibusdam fuga.</p>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Eum qui voluptatem quae quos error atque officiis!</p>
                </div>
                <div class="title-container">
                    <div class="title-access">
                        <div class="infor-user">
                            <img src="{{ asset('img/shiper_avt.jpg') }}" alt="">
                            <h1>Đàm Công Minh</h1>
                        </div>
                        <div class="detail-access">
                            <img src="{{ asset('img/shiper_avt.jpg') }}" alt="">
                            <div class="access-conment">
                                <h1>Đàm Công Minh</h1>
                                <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Dignissimos dicta dolorem explicabo fuga labore officiis accusamus ipsum sequi cumque nobis.</p>
                            </div>
                        </div>
                        <div class="btn-home">
                            <div class="home-ip"></div>
                        </div>
                    </div>
                    <div class="title-access">
                        <div class="infor-user">
                            <img src="{{ asset('img/shiper_avt.jpg') }}" alt="">
                            <h1>Đàm Công Minh</h1>
                        </div>
                        <div class="detail-access">
                            <img src="{{ asset('img/shiper_avt.jpg') }}" alt="">
                            <div class="access-conment">
                                <h1>Đàm Công Minh</h1>
                                <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Dignissimos dicta dolorem explicabo fuga labore officiis accusamus ipsum sequi cumque nobis.</p>
                            </div>
                        </div>
                        <div class="btn-home">
                            <div class="home-ip"></div>
                        </div>
                    </div>
                    <div class="title-access">
                        <div class="infor-user">
                            <img src="{{ asset('img/shiper_avt.jpg') }}" alt="">
                            <h1>Đàm Công Minh</h1>
                        </div>
                        <div class="detail-access">
                            <img src="{{ asset('img/shiper_avt.jpg') }}" alt="">
                            <div class="access-conment">
                                <h1>Đàm Công Minh</h1>
                                <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Dignissimos dicta dolorem explicabo fuga labore officiis accusamus ipsum sequi cumque nobis.</p>
                            </div>
                        </div>
                        <div class="btn-home">
                            <div class="home-ip"></div>
                        </div>
                    </div>
                    <div class="title-access">
                        <div class="infor-user">
                            <img src="{{ asset('img/shiper_avt.jpg') }}" alt="">
                            <h1>Đàm Công Minh</h1>
                        </div>
                        <div class="detail-access">
                            <img src="{{ asset('img/shiper_avt.jpg') }}" alt="">
                            <div class="access-conment">
                                <h1>Đàm Công Minh</h1>
                                <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Dignissimos dicta dolorem explicabo fuga labore officiis accusamus ipsum sequi cumque nobis.</p>
                            </div>
                        </div>
                        <div class="btn-home">
                            <div class="home-ip"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-------------- js của trang home ---------------->
        <script src="{{ url('js/home.js') }}"></script>
    </section>


<!-------------- Footer của trang home ---------------->
    @include('layout.footer')
</body>
</html>