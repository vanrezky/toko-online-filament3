<a {{ $attributes }} class="card card-compact product-item" title="{{ $product->name }}">
    <figure class="border-b border-gray-50 bg-secondary">
        @if ($isSalePrice)
            <div class="discount">
                - {{ $product->discount_percentace }}%
            </div>
        @endif
        <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" />
    </figure>
    <div class="card-body">
        <h2 class="product-name tracking-wide leading-relaxed">{{ $product->name_short }}</h2>
        <div>
            <span class="product-price tracking-wide leading-relaxed">{{ $product->price_currency }}</span>
            @if ($isSalePrice)
                <span class="product-price-sale tracking-wide leading-relaxed">{{ $product->sale_price_currency }}</span>
            @endif
        </div>
    </div>
</a>
