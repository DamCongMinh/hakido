<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Giỏ hàng</title>

    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
</head>
<body>
    @include('layout.header')

    <h2>Giỏ hàng</h2>
    <form action="{{ route('cart.processCheckout') }}" method="POST">
        @csrf
        <table>
            <thead>
                <tr>
                    <th>Hình ảnh</th>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng</th>
                    <th>
                        <input type="checkbox" id="select-all">
                        Chọn tất cả (<span id="selected-count">0</span>)
                    </th>
                    {{-- <th>Xóa</th>                 --}}
                </tr>
            </thead>
            <tbody>
                @forelse ($cart?->items ?? [] as $item)
                    <tr>
                        <td>
                            <img 
                                src="{{ asset('storage/' . ($item->food?->image ?? $item->beverage?->image)) }}" 
                                alt="{{ $item->food?->name ?? ($item->beverage?->name ?? 'Không rõ') }}" 
                                style="width: 80px; height: auto;"
                                onerror="this.src='{{ asset('img/slide.png') }}'"
                            >
                        </td>
                        <td>
                            {{ $item->food?->name ?? ($item->beverage?->name ?? 'Không rõ') }}
                            @if ($item->product_type === 'beverage' && $item->size)
                                (Size {{ $item->size }})
                            @endif
                        </td>
                        <td>{{ number_format($item->unit_price) }}₫</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_price * $item->quantity) }}₫</td>
                        <td>
                            <input type="checkbox" name="selected_items[{{ $loop->index }}][selected]" value="1" class="item-checkbox">
                            <input type="hidden" name="selected_items[{{ $loop->index }}][product_id]" value="{{ $item->product_id }}">
                            <input type="hidden" name="selected_items[{{ $loop->index }}][product_type]" value="{{ $item->product_type }}">
                            <input type="hidden" name="selected_items[{{ $loop->index }}][size]" value="{{ $item->size }}">
                        </td>

                        {{-- <td>
                            <form action="{{ route('cart.removeItem') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                <input type="hidden" name="product_type" value="{{ $item->product_type }}">
                                <input type="hidden" name="size" value="{{ $item->size }}">
                                <button type="submit" style="color: red; border: none; background: none; cursor: pointer;">🗑️</button>
                            </form>
                        </td> --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Giỏ hàng trống</td>
                    </tr>
                @endforelse

            </tbody>
        </table>
        <button type="submit">Thanh toán</button>
    </form>   

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all');
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            const selectedCount = document.getElementById('selected-count');
    
            function updateCount() {
                const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
                selectedCount.textContent = checkedCount;
            }
    
            // Khi click "chọn tất cả"
            selectAllCheckbox.addEventListener('change', function() {
                itemCheckboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
                updateCount();
            });
    
            // Khi tick từng item
            itemCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    updateCount();
    
                    // Đồng bộ trạng thái "chọn tất cả"
                    if (!this.checked) {
                        selectAllCheckbox.checked = false;
                    } else if (document.querySelectorAll('.item-checkbox:checked').length === itemCheckboxes.length) {
                        selectAllCheckbox.checked = true;
                    }
                });
            });
    
            // Init lần đầu
            updateCount();
        });
    </script>    
</body>
</html>