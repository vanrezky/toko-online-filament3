@props(['link' => '#', 'subtitle' => '', 'title' => '', 'sliderkey' => null])

<div class="container-content">
    <div class="flex items-center sm:mb-5">
        <div class="w-4 mr-2 rounded bg-secondary2 h-7"></div>
        <div class="font-semibold text-secondary2 tracking-wide">{{ $title }}</div>
    </div>

    <div class="flex items-center justify-between py-2">
        <div class="flex items-center space-x-2 sm:space-x-8">
            <div class="text-2xl font-semibold sm:text-[30px] text-text2 tracking-wide">{{ $subtitle }}</div>
            <div class=" grid grid-flow-col gap-2 md:gap-5 text-center auto-cols-max">
                <div class="flex flex-col">
                    <span class="countdown font-mono text-sm sm:text-lg">
                        <span style="--value:15;"></span>
                    </span>
                    <span class="text-xs sm:text-sm">days</span>
                </div>
                <div class="flex flex-col">
                    <span class="countdown font-mono text-sm sm:text-lg">
                        <span style="--value:10;"></span>
                    </span>
                    <span class="text-xs sm:text-sm">hours</span>
                </div>
                <div class="flex flex-col">
                    <span class="countdown font-mono text-sm sm:text-lg">
                        <span style="--value:24;"></span>
                    </span>
                    <span class="text-xs sm:text-sm">min</span>
                </div>
                <div class="flex flex-col">
                    <span class="countdown font-mono text-sm sm:text-lg">
                        <span style="--value:38;"></span>
                    </span>
                    <span class="text-xs sm:text-sm">sec</span>
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
    {{ $slot }}
</div>
