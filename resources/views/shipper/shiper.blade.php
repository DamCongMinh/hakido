<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/shiper.css') }}">
</head>
<body>
    <!-------------- Header của trang Shiper ---------------->
    @include('layout.header')
    
    <!-------------- body của trang Shiper ---------------->
    <form action="" method="GET">
        <section id="Shiper_body">
            <div class="shiper_container">
                <div class="container_content">
                    <div class="content_top">
                        <div class="top_shiper--avt">
                            <div class="avt-img">
                                <img src="{{ asset('img/shiper_avt.jpg') }}" alt="">
                            </div>
                            <div class="avt-name"><p>Đàm Công Minh</p></div>
                        </div>

                        <div class="top_shiper--turnover">
                            <div class="turnover-received">
                                <p>Tổng đơn nhận</p>
                                <p id="received">100</p>
                            </div>
                            <div class="turnover-money">
                                <p>Lương nhận được</p>
                                <p id="money">500.000.000d</p>
                            </div>
                        </div>
                    </div>

                    <div class="content_bottom">
                        <p>Danh sách đơn hàng chờ ship</p>
                        <table>
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Hình ảnh</th>
                                    <th>Tên Sản phẩm</th>
                                    <th>Tên nhà hàng</th>
                                    <th>Địa chỉ Nhận đơn</th>
                                    <th>Địa Chỉ Khách hàng</th>
                                    <th>Đơn giá</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td><img src="{{ asset('img/home_img4.jpg') }}" alt=""></td>
                                    <td>Trà sữa Trân trâu</td>
                                    <td>Đàm Công Minh</td>
                                    <td>Tân hợp-bắc lý-hiệp hòa-bắc giang</td>
                                    <td>Tân hợp-bắc lý-hiệp hòa-bắc giang</td>
                                    <td>100.000.000đ</td>
                                    <td><button>Nhận đơn</button></td>

                                </tr>
                            </tbody>
                            
                        </table>
                       
                    </div>
                </div>

                <div class="container_list">
                    <p>Danh sách đơn hàng có thể nhận</p>
                    <table>
                        <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Hình ảnh</th>
                                    <th>Tên Sản phẩm</th>
                                    <th>Đơn giá</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td><img src="{{ asset('img/home_img4.jpg') }}" alt=""></td>
                                    <td>Trà sữa Trân trâu</td>
                                    <td>100.000.000đ</td>
                                    <td><button>Nhận đơn</button></td>

                                </tr>
                            </tbody>
                    </table>
                </div>
            </div>

            <div class="restaurant_map">
                <h1>Thông Tin Nhà Hàng</h1>
                <div class="infor_restaurant">
                    <div class="restaurant_img"><img src="{{ asset('img/restaurant_img1.jpg') }}" alt=""></div>
                    <div class="restaurant_detail">
                        <h2>Nhà hàng lẩu nướng</h2>
                        <div class="detail-subject">
                            <div class="sell">
                                <h3>Lĩnh vực buôn bán</h3>
                                <p>Chuyên đồ lẩu</p>
                            </div>
                            <div class="describe">
                                <h3>Mô tả</h3>
                                <p>Nhà hàng lẩu là điểm đến lý tưởng cho những ai yêu thích hương vị đậm đà và không khí ấm cúng. Với nhiều loại nước lẩu đặc trưng như lẩu Thái chua cay, lẩu Tứ Xuyên cay nồng hay lẩu nấm thanh ngọt, thực khách có thể thưởng thức cùng các nguyên liệu tươi ngon như thịt bò, hải sản, rau xanh và nấm. Không chỉ là một bữa ăn, trải nghiệm lẩu còn mang đến sự gắn kết khi mọi người quây quần bên nồi lẩu sôi sùng sục, cùng nhau nhúng, nấu và thưởng thức từng hương vị độc đáo.</p>
                            </div>
                        </div>
                        <div class="address">
                            <h3>Địa Chỉ</h3>
                            <p>68 Nguyễn Huệ-Quang Châu-Bắc Giang</p>
                        </div>
                        <div class="detail-access">
                            <h3>Đánh Giá</h3>
                            <div class="access-icons">
                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                </div>
                                <p>(3.5/5)</p>
                            </div>
                        </div>

                        <div class="detail-btn">
                            <button>Đã Nhận Hàng</button>
                        </div>
                    </div>
                </div>
                <div class="map">
                    <h1>Bản đồ địa chỉ</h1>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14868.533610012173!2d105.96669520214219!3d21.305744477887888!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31351b09be8138a3%3A0x2020dfa9a0029fc3!2zTWnhur91IFTDom4gSOG7o3AgLSBI4bujcCBMw70!5e0!3m2!1sen!2s!4v1742549061006!5m2!1sen!2s" width="100%" height="800" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>

            <div class="restaurant_map">
                <h1>Thông Tin Đơn Hàng</h1>
                <div class="infor_restaurant">
                    <div class="restaurant_img"><img src="{{ asset('img/home_img4.jpg') }}" alt=""></div>
                    <div class="restaurant_detail">
                        <h2>TRÀ SỮ SIÊU NGON</h2>
                        <div class="detail-subject">
                            <div class="sell">
                                <h3>Tên khách hàng</h3>
                                <p>TRẦN TRÀ MY</p>
                            </div>
                            <div class="describe">
                                <h3>SĐT</h3>
                                <p>0366217981</p>
                            </div>
                        </div>
                        <div class="address">
                            <h3>Địa Chỉ</h3>
                            <p>TDP HỢP THỊNH-BẮC LÝ-HIỆP HÒA-BẮC GIANG</p>
                        </div>
                        <div class="detail-access">
                            <h3>Giá sản phẩm</h3>
                            <P>100.000.000đ</P>
                        </div>
                        <div class="detail-access">
                            <h3>Giá ship đơn</h3>
                            <P>15.000.000đ</P>
                        </div>

                        <div class="detail-btn">
                            <button>Đã Giao Hàng</button>
                        </div>
                    </div>
                </div>
                <div class="map">
                    <h1>Bản đồ địa chỉ</h1>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14868.533610012173!2d105.96669520214219!3d21.305744477887888!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31351b09be8138a3%3A0x2020dfa9a0029fc3!2zTWnhur91IFTDom4gSOG7o3AgLSBI4bujcCBMw70!5e0!3m2!1sen!2s!4v1742549061006!5m2!1sen!2s" width="100%" height="800" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </section>
    </form>

    @include('layout.footer')
</body>
</html>