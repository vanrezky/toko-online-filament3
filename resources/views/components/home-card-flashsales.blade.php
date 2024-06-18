@props(['link' => '#', 'subtitle' => '', 'title' => '', 'products', 'sliderkey' => null])

<div class="container-content">
    <div class="flex items-center sm:mb-5">
        <div class="w-4 mr-2 rounded bg-secondary2 h-7"></div>
        <div class="font-semibold text-secondary2">{{ $title }}</div>
    </div>

    <div class="flex items-center justify-between py-2">
        <div class="flex items-center space-x-2 sm:space-x-8">
            <div class="text-2xl font-bold sm:text-[30px] text-text2">{{ $subtitle }}</div>
            <div class="flex items-center justify-start space-x-1 ">
                <div class="text-center">
                    <div class="text-xs sm:text-sm text-gray-500">Days</div>
                    <div class="text-sm sm:text-lg font-bold">03</div>
                </div>
                <div class="text-sm sm:text-lg font-bold text-secondary2">:</div>
                <div class="text-center">
                    <div class="text-xs sm:text-sm text-gray-500">Hours</div>
                    <div class="text-sm sm:text-lg font-bold">23</div>
                </div>
                <div class="text-sm sm:text-lg font-bold text-secondary2">:</div>
                <div class="text-center">
                    <div class="text-xs sm:text-sm text-gray-500">Minutes</div>
                    <div class="text-sm sm:text-lg font-bold">19</div>
                </div>
                <div class="text-sm sm:text-lg font-bold text-secondary2">:</div>
                <div class="text-center">
                    <div class="text-xs sm:text-sm text-gray-500">Seconds</div>
                    <div class="text-sm sm:text-lg font-bold">56</div>
                </div>
            </div>
        </div>
        @if ($sliderkey)
            <div class="flex space-x-2">
                <button class="p-2 bg-gray-200 rounded-full {{ $sliderkey }}-swiper-button-prev">
                    <x-tabler-chevron-left class="h-4 w-4" />
                </button>
                <button class="p-2 bg-gray-200 rounded-full {{ $sliderkey }}-swiper-button-next">
                    <x-tabler-chevron-right class="h-4 w-4" />
                </button>
            </div>
        @endif
    </div>
    <div class="separator"></div>
    <livewire:home.product-flashsales :$products lazy :$sliderkey />
</div>
