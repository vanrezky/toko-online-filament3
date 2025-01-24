<div>
    <div class="swiper flashsales-swiper product-container pb-3" x-data="{ swiperflash: null }" x-init="swiperflash = new Swiper($refs.swiperflash, {
        slidesPerView: 2,
        spaceBetween: 8,
        freeMode: true,
        clickable: true,
        pagination: {
            // el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            prevEl: '.{{ $sliderkey }}-swiper-button-prev',
            nextEl: '.{{ $sliderkey }}-swiper-button-next',
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
                spaceBetween: 16,
            },
            768: {
                slidesPerView: 3,
                spaceBetween: 16,
            },
            1024: {
                slidesPerView: 5,
                spaceBetween: 16,
            },
        },
    });" x-ref="swiperflash">
        <div class="swiper-wrapper">
            @foreach ($products as $product)
                <div class="swiper-slide" wire:navigate>
                    <x-product-item :$product href="/products/{{ $product->slug }}" />
                </div>
            @endforeach
        </div>
        {{-- <div class="swiper-pagination"></div> --}}
    </div>

    <div class="flex justify-center items-center mt-5 md:mt-8">
        <a href="#" class="btn btn-general text-text text-sm font-normal">{{ __('View all Products') }}</a>
    </div>
</div>
