<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Danh sách Voucher</title>
    <link rel="stylesheet" href="{{ asset('css/Restaurant/voucher/homeVoucher.css') }}">
</head>
<body>
    @include('layout.header')
    <div class="container">
        <a class="create-voucher" href="{{ route('restaurant.create.voucher') }}">Tạo Voucher</a>
        <div class="container">
            <h2 class="h2">Danh sách Voucher</h2>
            <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                <thead style="background-color: #f2f2f2;">
                    <tr>
                        <th>Mã</th>
                        <th>Loại</th>
                        <th>Giá trị</th>
                        <th>Đơn tối thiểu</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Lượt dùng</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vouchers as $voucher)
                        <tr>
                            <td>{{ $voucher->code }}</td>
                            <td>{{ $voucher->type }}</td>
                            <td>
                                @if($voucher->type === 'free_shipping')
                                    Miễn phí ship
                                @else
                                    {{ number_format($voucher->value) }} đ
                                @endif
                            </td>
                            <td>{{ number_format($voucher->min_order_value) }} đ</td>
                            <td>{{ $voucher->start_date }}</td>
                            <td>{{ $voucher->end_date }}</td>
                            <td>{{ $voucher->used_count }} / {{ $voucher->usage_limit }}</td>
                            <td>
                                @if($voucher->is_active)
                                    <span style="color: green;">Hoạt động</span>
                                @else
                                    <span style="color: red;">Ngưng</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @include('layout.footer')

</body>
</html>