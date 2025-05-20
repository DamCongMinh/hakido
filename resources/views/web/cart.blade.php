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
    <h2 class="h2">Giỏ hàng</h2>

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
            @php
                $itemsToShow = $cart?->items ?? $sessionCart ?? collect();
            @endphp
        
            @forelse ($itemsToShow as $index => $item)
                @php
                    $isArray = is_array($item);
                    $type = $isArray ? $item['type'] : $item->product_type;
                    $product = null;

                    if ($isArray) {
                        $product = $item['product'];
                        $size = $item['size'] ?? null;
                        $quantity = $item['quantity'];
                        $unit_price = $item['unit_price'];
                        $product_id = $item['product_id'];
                    } else {
                        $product = $type === 'food' ? $item->food : $item->beverage;
                        $size = $item->size ?? null;
                        $quantity = $item->quantity;
                        $unit_price = $item->unit_price;
                        $product_id = $item->product_id;
                    }
                @endphp
        
                <tr>
                    <td>
                        <img src="{{ asset('storage/' . ($product->image ?? '')) }}" 
                             alt="{{ $product->name ?? 'Không rõ' }}"
                             style="width: 80px;" 
                             onerror="this.src='{{ asset('img/slide.png') }}'">
                    </td>
                    <td>
                        {{ $product->name ?? 'Không rõ' }}
                        @if ($type === 'beverage' && $size)
                            (Size {{ $size }})
                        @endif
                    </td>
                    <td>{{ number_format($unit_price) }}₫</td>
                    <td>{{ $quantity }}</td>
                    <td>{{ number_format($unit_price * $quantity) }}₫</td>
                    <td>
                        <form id="select-item-{{ $index }}">
                            <input type="checkbox" name="selected_items[{{ $index }}][selected]" value="1" class="item-checkbox" form="checkout-form">
                            <input type="hidden" name="selected_items[{{ $index }}][product_id]" value="{{ $product_id }}" form="checkout-form">
                            <input type="hidden" name="selected_items[{{ $index }}][product_type]" value="{{ $type }}" form="checkout-form">
                            <input type="hidden" name="selected_items[{{ $index }}][size]" value="{{ $size }}" form="checkout-form">
                        </form>
                    </td>
                    <td>
                        <form action="{{ route('cart.removeItem') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="product_id" value="{{ $product_id }}">
                            <input type="hidden" name="product_type" value="{{ $type }}">
                            <input type="hidden" name="size" value="{{ $size }}">
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
