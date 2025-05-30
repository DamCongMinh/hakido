
    <div id="beverage-pending" class="product-section">
        <h2 class="h2">Đồ Uống - Chờ Duyệt</h2>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Hình ảnh</th>
                    <th>Tên</th>
                    <th>Nhà hàng</th>
                    <th>Size</th>
                    <th>Giá</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($beveragesPending as $beverage)
                    @php $rowspan = count($beverage->beverageSizes); @endphp
                    @foreach($beverage->beverageSizes as $index => $size)
                        <tr>
                            @if ($index === 0)
                                <td rowspan="{{ $rowspan }}">{{ $beverage->id }}</td>
                                <td rowspan="{{ $rowspan }}"><img src="{{ asset('storage/' . $beverage->image) }}" width="70"></td>
                                <td rowspan="{{ $rowspan }}">{{ $beverage->name }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $beverage->restaurant->name ?? 'Không có' }}</td>
                            @endif
                            <td>{{ $size->size }}</td>
                            <td>{{ number_format($size->new_price) }}₫</td>
                            @if ($index === 0)
                                <td rowspan="{{ $rowspan }}">
                                    <form action="{{ route('admin.beverages.approve', $beverage->id) }}" method="POST" style="display:inline">
                                        @csrf
                                        <button class="btn" type="submit">Duyệt</button>
                                    </form>
                                    |
                                    <form action="{{ route('admin.beverages.reject', $beverage->id) }}" method="POST" style="display:inline">
                                        @csrf
                                        <button class="btn-reject" type="submit" onclick="return confirm('Bạn có chắc muốn từ chối?')">Từ chối</button>
                                    </form>
                                    |
                                    <form action="{{ route('admin.beverages.destroy', $beverage->id) }}" method="POST" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-danger" type="submit" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @empty
                    <tr><td colspan="7" style="text-align: center; color: gray;">Không có sản phẩm nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
