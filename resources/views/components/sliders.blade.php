<div class="glider-contain w-full h-[8rem] md:h-[15rem] lg:h-[25rem] bg-gray-100 overflow-hidden relative">
    <div class="glider">
        @foreach ($sliders as $slider)
            @if ($slider->target_link)
                <a target="{{ $slider->target_anchor ? $slider->target_anchor : '_self' }}" href="{{ $slider->target_link }}" class="flex justify-center items-center h-full">
                    <img src="{{ $slider->image_url }}" alt="" class="w-full h-full object-cover">
                </a>
            @endif
            <div class="flex justify-center items-center h-full">
                <img src="{{ $slider->image_url }}" alt="" class="w-full h-full object-cover">
            </div>
        @endforeach
    </div>

    <button type="button" class="glider-prev absolute inset-y-0 inline-flex items-center justify-center w-12 h-full text-black disabled:pointer-events-none start-0 hover:bg-white/20 focus:outline-none focus:bg-white/20">
        <span class="text-2xl" aria-hidden="true">
            <svg class="flex-shrink-0 size-3.5 md:size-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"></path>
            </svg>
        </span>
        <span class="sr-only">Previous</span>
    </button>

    <button type="button" class="glider-next absolute inset-y-0 inline-flex items-center justify-center w-12 h-full text-black  disabled:pointer-events-none end-0 hover:bg-white/20 focus:outline-none focus:bg-white/20">
        <span class="sr-only">Next</span>
        <span class="text-2xl" aria-hidden="true">
            <svg class="flex-shrink-0 size-3.5 md:size-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"></path>
            </svg>
        </span>
    </button>
    <div id="dots" class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-2"></div>
</div>

@push('scripts')
    <script>
        const gliderSlider = new Glider(document.querySelector('.glider'), {
            slidesToShow: 1,
            dots: '#dots',
            draggable: false,
            itemWidth: undefined,
            duration: .5,
            arrows: {
                prev: '.glider-prev',
                next: '.glider-next'
            },
            // responsive: [{
            //         breakpoint: 900,
            //         settings: {
            //             slidesToShow: 2,
            //             slidesToScroll: 2
            //         }
            //     },
            //     {
            //         breakpoint: 575,
            //         settings: {
            //             slidesToShow: 3,
            //             slidesToScroll: 3
            //         }
            //     }
            // ]
        });

        function autoSlide() {
            setInterval(() => {
                if (!document.hidden) {
                    gliderSlider.scrollItem('next');
                }
            }, 3000); // Ganti dengan interval waktu yang Anda inginkan, misalnya 3000ms (3 detik)
        }

        autoSlide();
    </script>
@endpush
