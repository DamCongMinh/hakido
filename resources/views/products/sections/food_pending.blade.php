
    <div id="food-pending" class="product-section">
        <h2 class="h2">Đồ Ăn - Chờ Duyệt</h2>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Hình ảnh</th>
                    <th>Tên</th>
                    <th>Nhà hàng</th>
                    <th>Giá</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($foodsPending as $food)
                    <tr>
                        <td>{{ $food->id }}</td>
                        <td><img src="{{ asset('storage/' . $food->image) }}" alt="Hình" width="70"></td>
                        <td>{{ $food->name }}</td>
                        <td>{{ $food->restaurant->name ?? 'Không có' }}</td>
                        <td>{{ number_format($food->new_price) }}đ</td>
                        <td>
                            <form action="{{ route('admin.foods.approve', $food->id) }}" method="POST" style="display:inline">
                                @csrf
                                <button class="btn" type="submit">Duyệt</button>
                            </form>
                            |
                            <form action="{{ route('admin.foods.reject', $food->id) }}" method="POST" style="display:inline">
                                @csrf
                                <button class="btn-reject" type="submit" onclick="return confirm('Bạn có chắc muốn từ chối?')">Từ chối</button>
                            </form>
                            |
                            <form action="{{ route('admin.foods.destroy', $food->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn-danger" type="submit" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align: center; color: gray;">Không có sản phẩm nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
