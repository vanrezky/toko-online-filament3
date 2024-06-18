@props(['link' => '#', 'subtitle' => '', 'title' => '', 'sliderkey' => null])

<div class="container-content">
    <div class="flex items-center sm:mb-5">
        <div class="w-4 mr-2 rounded bg-secondary2 h-7"></div>
        <div class="font-semibold text-secondary2">{{ $title }}</div>
    </div>

    @if ($sliderkey)
        <div class="flex items-center justify-between py-2">
            <div class="flex items-center space-x-2 sm:space-x-8">
                <div class="text-2xl font-bold sm:text-[30px] text-text2">{{ $subtitle }}</div>
            </div>
            <div class="flex space-x-2">
                <button class="p-2 bg-gray-200 rounded-full {{ $sliderkey }}-swiper-button-prev">
                    <x-tabler-chevron-left class="h-4 w-4" />
                </button>
                <button class="p-2 bg-gray-200 rounded-full {{ $sliderkey }}-swiper-button-next">
                    <x-tabler-chevron-right class="h-4 w-4" />
                </button>
            </div>
        </div>
    @else
        <div class="flex items-center justify-between py-2">
            <div class="text-2xl font-bold sm:text-[30px]">{{ $subtitle }}</div>
            <a href="#" class="rounded-lg  px-3 py-2  md:px-5 md:py-3 text-xs  btn-general ">View All</a>
        </div>
    @endif
    <div class="separator"></div>
    {{ $slot }}
</div>
