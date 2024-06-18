<div class="swiper slider-home" x-data="{ swiperSlider: null }" x-data="{ swiperSlider: null }" x-init="swiperSlider = new Swiper($refs.swiperSlider, {
    pagination: {
        el: '.swiper-slider-pagination',
        dynamicBullets: true,
    },
    autoplay: {
        delay: 3000, // Adjust the delay as needed
        disableOnInteraction: false,
    },
    loop: true,
});" x-ref="swiperSlider">
    <div class="swiper-wrapper slider-home-wrapper">
        @foreach ($sliders as $slider)
            @if ($slider->target_link)
                <a target="{{ $slider->target_anchor ? $slider->target_anchor : '_self' }}" href="{{ $slider->target_link }}" class="swiper-slide slider-slide">
                    <img src="{{ $slider->image_url }}" alt="">
                </a>
            @else
                <div class="swiper-slide slider-slide">
                    <img src="{{ $slider->image_url }}" alt="">
                </div>
            @endif
        @endforeach
    </div>
    <div class="swiper-pagination swiper-slider-pagination"></div>
</div>
