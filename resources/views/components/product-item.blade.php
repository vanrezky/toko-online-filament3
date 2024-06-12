    <div {{ $attributes }} class="home-card-item-square" title="{{ $product->name }}">
        @if ($isSalePrice)
            <div class="discount">
                {{ $product->discount_percentace }}% OFF
            </div>
        @endif
        <div class="image">
            <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}">
        </div>
        <div class="description">
            <h3 class="product-name text-nowrap">{{ $product->name_short }}</h3>
            <div class="price-detail">
                <span class="price">{{ $product->price_currency }}</span>
                @if ($isSalePrice)
                    <span class="discount-price">{{ $product->sale_price_currency }}</span>
                @endif
            </div>
            @if ($saveprice && $isSalePrice)
                <div class="separator"></div> <!-- Separator -->
                <p class="save-price">Save - {{ $product->save_price_currency }}</p>
            @endif
        </div>
    </div>
