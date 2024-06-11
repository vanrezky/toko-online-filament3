<div>
    <div data-hs-carousel='{
    "loadingClasses": "opacity-0"
  }' class="relative">
        <div class="hs-carousel relative overflow-hidden w-full h-[8rem] md:h-[15rem] lg:h-[25rem] bg-gray-100 ">
            <div class="absolute top-0 bottom-0 flex transition-transform duration-700 opacity-0 hs-carousel-body start-0 flex-nowrap">
                {{ $slot }}
            </div>
        </div>

        <!-- Arrows -->
        <button type="button"
            class="absolute inset-y-0 inline-flex items-center justify-center w-12 h-full text-black hs-carousel-prev hs-carousel:disabled:opacity-50 disabled:pointer-events-none start-0 hover:bg-white/20 focus:outline-none focus:bg-white/20">
            <span class="text-2xl" aria-hidden="true">
                <svg class="flex-shrink-0 size-3.5 md:size-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"></path>
                </svg>
            </span>
            <span class="sr-only">Previous</span>
        </button>

        <button type="button"
            class="absolute inset-y-0 inline-flex items-center justify-center w-12 h-full text-black hs-carousel-next hs-carousel:disabled:opacity-50 disabled:pointer-events-none end-0 hover:bg-white/20 focus:outline-none focus:bg-white/20">
            <span class="sr-only">Next</span>
            <span class="text-2xl" aria-hidden="true">
                <svg class="flex-shrink-0 size-3.5 md:size-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"></path>
                </svg>
            </span>
        </button>
        <!-- End Arrows -->
    </div>
</div>
