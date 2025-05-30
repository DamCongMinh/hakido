<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đánh giá</title>
    <link rel="stylesheet" href="{{ asset('css/review/reviews.css') }}">
</head>
<body>
    @include('layout.header')

    <div class="container">
        <div class="review-tabs">
            <button onclick="showSection('food')">Đánh giá đồ ăn</button>
            <button onclick="showSection('beverage')">Đánh giá đồ uống</button>
            <button onclick="showSection('shipping')">Đánh giá dịch vụ</button>
        </div>

        {{-- Đồ ăn --}}
        <div id="section-food" class="review-section-group" style="display: none;">
            <h2>Đánh giá chất lượng đồ ăn</h2>
            @foreach ($foodItems as $item)
                @include('review.review-item', [
                    'product_name' => $item->product_name,
                    'review' => $foodReviews[$item->product_id] ?? null,
                    'form_action' => route('reviews.FoodReview'),
                    'hidden_fields' => [
                        'order_id' => $order->id,
                        'food_id' => $item->product_id,
                        'customer_id' => auth()->user()->customer->id,
                    ],
                    'input_prefix' => 'food-' . $item->product_id,
                    'product_image' => $item->image  // 🔧 thêm dòng này
            ])
            
            
            @endforeach
        </div>

        {{-- Đồ uống --}}
        <div id="section-beverage" class="review-section-group" style="display: none;">
            <h2>Đánh giá chất lượng đồ uống</h2>
            @foreach ($beverageItems as $item)
                @include('review.review-item', [
                    'product_name' => $item->product_name,
                    'review' => $beverageReviews[$item->product_id] ?? null,
                    'form_action' => route('reviews.BeverageReview'),
                    'hidden_fields' => [
                        'order_id' => $order->id,
                        'beverage_id' => $item->product_id,
                        'customer_id' => auth()->user()->customer->id,
                    ],
                    'input_prefix' => 'beverage-' . $item->product_id,
                    'product_image' => $item->image
                ])      
            @endforeach
        </div>

        {{-- Giao hàng --}}
        @if ($order && $order->shipper_id)
            <div id="section-shipping" class="review-section-group review-card" style="display: none;">
                <h2>Đánh giá dịch vụ giao hàng</h2>

                @if ($shippingReview)
                    <strong>Người giao hàng:</strong> 
                    <h3>{{ $order->shipper->name }}</h3>
                    <div class="review-rate">
                        <strong>Đánh giá:</strong>
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= $shippingReview->rating ? 'star_filled' : 'star' }}">&#9733;</span>
                        @endfor
                    </div>
                    <strong>Nhận xét:</strong>
                    <textarea disabled>{{ $shippingReview->comment }}</textarea>
                @else
                    <form action="{{ route('reviews.ShippingReview') }}" method="POST" class="review-form">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <input type="hidden" name="shipper_id" value="{{ $order->shipper->id }}">
                        <input type="hidden" name="customer_id" value="{{ auth()->user()->customer->id }}">

                        <h3 class="h3"><strong>Người giao hàng:</strong> {{ $order->shipper->name }}</h3>
                        <div class="review-rate">
                            <strong>Chất lượng giao hàng:</strong>
                            <div class="rating">
                                @for ($i = 5; $i >= 1; $i--)
                                    <input type="radio" id="shipping-star{{ $i }}" name="shipping_rating" value="{{ $i }}" required>
                                    <label for="shipping-star{{ $i }}">&#9733;</label>
                                @endfor
                            </div>
                        </div>

                        <textarea name="shipping_comment" placeholder="Nhận xét về dịch vụ giao hàng..."></textarea>
                        <div class="review-btn"><button class="submit" type="submit">Gửi đánh giá</button></div>
                    </form>
                @endif
            </div>
        @endif
    </div>

    <script>
        function showSection(section) {
            const sections = ['food', 'beverage', 'shipping'];
            sections.forEach(s => {
                const el = document.getElementById('section-' + s);
                if (el) el.style.display = (s === section) ? 'block' : 'none';
            });
        }

        // Tự động mở tab đầu tiên có sản phẩm
        document.addEventListener('DOMContentLoaded', function () {
            @if(count($foodItems) > 0)
                showSection('food');
            @elseif(count($beverageItems) > 0)
                showSection('beverage');
            @else
                showSection('shipping');
            @endif
        });
    </script>
</body>
</html>
