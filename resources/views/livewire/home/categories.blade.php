<div class="swiper flashsales-swiper product-container" x-data="{ swiperCategories: null }" x-init="swiperCategories = new Swiper($refs.swiperCategories, {
    slidesPerView: 3,
    spaceBetween: 8,
    freeMode: true,
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
            slidesPerView: 3,
            spaceBetween: 16,
        },
        768: {
            slidesPerView: 4,
            spaceBetween: 16,
        },
        1024: {
            slidesPerView: 6,
            spaceBetween: 16,
        },
    },
});" x-ref="swiperCategories">
    <div class="swiper-wrapper">
        @foreach ($categories as $category)
            <a href="#" class="swiper-slide card card-compact border border-gray-300 p-2" title="{{ $category->name }}">
                <figure class=" rounded-xl overflow-hidden flex justify-center text-center">
                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="max-w-16" />
                </figure>
                <div class="card-body">
                    <div class="flex justify-center text-center">
                        <h2 class="text-xs font-semibold sm:text-sm">{{ $category->name }}</h2>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
    {{-- <div class="swiper-pagination"></div> --}}
</div>
