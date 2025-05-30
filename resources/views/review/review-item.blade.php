<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/review/reviews.css') }}">
</head>
<body>
    {{-- resources/views/components/review-item.blade.php --}}
    <div class="review-card">
        <div class="review-img">
            @if (!empty($product_image))
                <img src="{{ asset('storage/' . $product_image) }}" alt="{{ $product_name }}" class="w-32 h-32 object-cover rounded mb-3">
            @endif
        </div>

        <div class="review-title">
            <strong>Tên sản phẩm:</strong>
            <h3 class="h3">{{ $product_name }}</h3 class="h3">

            @if ($review)
                <div class="review-rate">
                    <strong>Đánh giá:</strong>
                    @for ($i = 1; $i <= 5; $i++)
                        <span class="{{ $i <= $review->rating ? 'star_filled' : 'star' }}">&#9733;</span>
                    @endfor
                </div>
                <strong>Nhận xét:</strong>
                <textarea disabled>{{ $review->comment }}</textarea>
            @else
                <form action="{{ $form_action }}" method="POST">
                    @csrf
                    @foreach ($hidden_fields as $name => $value)
                        <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                    @endforeach

                    <div class="review-rate">
                        <strong>Đánh giá:</strong>
                        <div class="rating">
                            @for ($i = 5; $i >= 1; $i--)
                                <input type="radio" id="{{ $input_prefix }}-star{{ $i }}" name="rating" value="{{ $i }}">
                                <label for="{{ $input_prefix }}-star{{ $i }}">&#9733;</label>
                            @endfor
                        </div>
                    </div>
                    <strong>Nhận xét:</strong>
                    <textarea name="comment" placeholder="Nhận xét..."></textarea>
                    <div class="reivew-btn"><button class="submit" type="submit">Gửi đánh giá</button></div>
                </form>
            @endif
        </div>
    </div>

</body>
</html>