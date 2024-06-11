@props(['link' => '#', 'subtitle' => '', 'title' => '', 'flashsales' => false])

<div class="container-content">
    <div class="flex items-center sm:mb-5">
        <div class="w-4 mr-2 rounded bg-primary h-7"></div>
        <div class="font-semibold text-primary font-glory">{{ $title }}</div>
    </div>

    @if ($flashsales)
        <div class="flex items-center justify-between py-2">
            <div class="flex items-center space-x-2 sm:space-x-8">
                <div class="text-2xl font-bold sm:text-[30px] font-glory">Flash Sales</div>
                <div class="flex items-center justify-start space-x-1 ">
                    <div class="text-center">
                        <div class="text-sm text-gray-500">Days</div>
                        <div class="text-2xl font-bold">03</div>
                    </div>
                    <div class="text-2xl font-bold text-primary">:</div>
                    <div class="text-center">
                        <div class="text-sm text-gray-500">Hours</div>
                        <div class="text-2xl font-bold">23</div>
                    </div>
                    <div class="text-2xl font-bold text-primary">:</div>
                    <div class="text-center">
                        <div class="text-sm text-gray-500">Minutes</div>
                        <div class="text-2xl font-bold">19</div>
                    </div>
                    <div class="text-2xl font-bold text-primary">:</div>
                    <div class="text-center">
                        <div class="text-sm text-gray-500">Seconds</div>
                        <div class="text-2xl font-bold">56</div>
                    </div>
                </div>
            </div>
            <div class="flex space-x-2">
                <button class="p-2 bg-gray-200 rounded-full">
                    <x-tabler-chevron-left class="h-4 w-4" />
                </button>
                <button class="p-2 bg-gray-200 rounded-full">
                    <x-tabler-chevron-right class="h-4 w-4" />
                </button>
            </div>
        </div>
    @else
        <div class="flex items-center justify-between py-2">
            <div class="text-2xl font-bold sm:text-[30px] font-glory">{{ $subtitle }}</div>
            <a href="{{ $link }}" wire:navigate class="flex items-center">
                <span class="text-xs text-text font-glory text-nowrap sm:text-sm">View All</span>
                <x-tabler-chevron-right class="size-5 stroke-primary" />
            </a>
        </div>
    @endif
    <div class="separator"></div> <!-- Separator -->
    {{ $slot }}
</div>
