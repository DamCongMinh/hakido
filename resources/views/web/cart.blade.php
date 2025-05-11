<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
</head>
<body>
    @include('layout.header')

    <!-- Giỏ hàng -->
    <h2>Giỏ hàng</h2>

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
                <th>Xóa</th>                
            </tr>
        </thead>
        <tbody>
            @forelse ($cart?->items ?? [] as $index => $item)
                <tr>
                    <td>
                        <img src="{{ asset('storage/' . ($item->food?->image ?? $item->beverage?->image)) }}" 
                            alt="{{ $item->food?->name ?? $item->beverage?->name ?? 'Không rõ' }}"
                            style="width: 80px;" onerror="this.src='{{ asset('img/slide.png') }}'">
                    </td>
                    <td>
                        {{ $item->food?->name ?? $item->beverage?->name ?? 'Không rõ' }}
                        @if ($item->product_type === 'beverage' && $item->size)
                            (Size {{ $item->size }})
                        @endif
                    </td>
                    <td>{{ number_format($item->unit_price) }}₫</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price * $item->quantity) }}₫</td>
                    <td>
                        <form id="select-item-{{ $index }}">
                            <input type="checkbox" name="selected_items[{{ $index }}][selected]" value="1" class="item-checkbox" form="checkout-form">
                            <input type="hidden" name="selected_items[{{ $index }}][product_id]" value="{{ $item->product_id }}" form="checkout-form">
                            <input type="hidden" name="selected_items[{{ $index }}][product_type]" value="{{ $item->product_type }}" form="checkout-form">
                            <input type="hidden" name="selected_items[{{ $index }}][size]" value="{{ $item->size }}" form="checkout-form">
                        </form>
                    </td>
                    <td>
                        {{-- Xóa: FORM ĐẶT BÊN NGOÀI --}}
                        <form action="{{ route('cart.removeItem') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                            <input type="hidden" name="product_type" value="{{ $item->product_type }}">
                            <input type="hidden" name="size" value="{{ $item->size }}">
                            <button type="submit" style="color:red; border:none; background:none;">🗑️</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">Giỏ hàng trống</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- FORM THANH TOÁN --}}
    <form action="{{ route('cart.processCheckout') }}" method="POST" id="checkout-form">
        @csrf
        <button type="submit" class="btn-payment">Thanh toán</button>
    </form>

    @include('layout.footer')

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const countDisplay = document.getElementById('selected-count');

        function updateCount() {
            const checked = document.querySelectorAll('.item-checkbox:checked').length;
            countDisplay.textContent = checked;
        }

        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateCount();
        });

        checkboxes.forEach(cb => cb.addEventListener('change', updateCount));
        updateCount();
    });
    </script>

</body>
</html>
