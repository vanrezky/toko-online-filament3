<div class="product-container grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-5" id="{{ $blockid }}">
    @foreach ($products as $product)
        <x-product-item :$product href="/products/{{ $product->slug }}" wire:navigate />
    @endforeach
</div>
