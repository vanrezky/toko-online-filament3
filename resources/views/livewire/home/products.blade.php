<div class="home-card grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-5">

    @foreach ($products as $product)
        <x-product-item :$product href="/products/{{ $product->slug }}" wire:navigate />
        {{-- <livewire:home.product-item :$product lazy wire:navigate /> --}}
    @endforeach

</div>
