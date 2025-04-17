<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/restaurant.css') }}">
    <title>Restaurant</title>
</head>
<body>
    <!-------------- Header của trang Login ---------------->
    @include('layout.header')

    <main class="content">
        <header>
            <h1>Chào mừng, Nhà hàng!</h1>
            <p>Hôm nay là <strong>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</strong></p>
        </header>
    
        <section class="stats">
            <div class="card">
                <h3>Đơn hôm nay</h3>
                <p>23</p>
            </div>
            <div class="card">
                <h3>Đang xử lý</h3>
                <p>8</p>
            </div>
            <div class="card">
                <h3>Doanh thu</h3>
                <p>1.500.000đ</p>
            </div>
        </section>
    
        <section class="recent-orders">
            <h2>Đơn hàng gần đây</h2>
            <table>
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tình trạng</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#001</td>
                        <td>Nguyễn Văn A</td>
                        <td>16/04/2025</td>
                        <td><span class="status pending">Chờ xác nhận</span></td>
                    </tr>
                    <tr>
                        <td>#002</td>
                        <td>Trần Thị B</td>
                        <td>16/04/2025</td>
                        <td><span class="status processing">Đang xử lý</span></td>
                    </tr>
                    <tr>
                        <td>#003</td>
                        <td>Lê Minh C</td>
                        <td>15/04/2025</td>
                        <td><span class="status completed">Hoàn thành</span></td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>
    
</body>
</html>