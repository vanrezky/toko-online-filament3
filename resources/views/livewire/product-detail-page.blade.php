<x-app-container>
    <div class="container-content">
        <div class="flex flex-wrap">
            <div class="w-full mb-8 md:w-1/2 md:mb-0" x-data="{ mainImage: '{{ $product->thumbnail }}' }">
                <div class="flex lg:space-x-4">
                    <div class="hidden md:flex flex-col space-y-2 lg:w-1/5">
                        @foreach ($product->images as $image)
                            <div class="w-full " x-on:click="mainImage='{{ getUrlImage($image) }}'">
                                <img src="{{ getUrlImage($image) }}" alt="" class="object-cover w-full cursor-pointer hover:border hover:border-blue-500">
                            </div>
                        @endforeach
                    </div>
                    <div class="sticky top-0 z-50 overflow-hidden lg:w-4/5">
                        <div class="relative mb-6 lg:mb-10">
                            <img x-bind:src="mainImage" alt="" class="object-cover w-full lg:h-full ">
                        </div>
                        <div class="px-6 pb-6 mt-6 border-t border-gray-300">
                            <div class="flex flex-wrap items-center mt-6">
                                <span class="mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="w-4 h-4 text-gray-700 bi bi-truck" viewBox="0 0 16 16">
                                        <path
                                            d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5v-7zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456zM12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z">
                                        </path>
                                    </svg>
                                </span>
                                <h2 class="text-lg font-bold text-gray-700">Free Shipping</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full px-4 md:w-1/2 ">
                <div class="lg:pl-20">
                    <div class="mb-8 ">
                        <h2 class="max-w-xl mb-6 text-xl font-bold md:text-2xl">
                            {{ $product->name }}</h2>
                        <p class="inline-block mb-6 text-4xl font-bold text-gray-700 ">
                            <span>{{ $product->price_currency }}</span>
                            @if ($product->sale_price)
                                <span class="text-base font-normal text-gray-500 line-through">{{ $product->sale_price_currency }}</span>
                            @endif
                        </p>
                        <div class="flex flex-wrap space-x-4 items-center">

                            <div class="w-32 mb-8">
                                <div class="relative flex flex-row w-full h-10 mt-6 bg-transparent rounded-lg">
                                    <button class="w-20 h-full text-gray-600 bg-gray-300 rounded-l outline-none cursor-pointer  hover:text-gray-700  hover:bg-gray-400">
                                        <span class="m-auto text-2xl font-thin">-</span>
                                    </button>
                                    <input type="number" readonly class="flex items-center w-full font-semibold text-center text-gray-700 placeholder-gray-700 bg-gray-300 outline-none   focus:outline-none text-md hover:text-black" placeholder="1">
                                    <button class="w-20 h-full text-gray-600 bg-gray-300 rounded-r outline-none cursor-pointer   hover:text-gray-700 hover:bg-gray-400">
                                        <span class="m-auto text-2xl font-thin">+</span>
                                    </button>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-4">
                                <button class="btn btn-primary py-2">Add to cart</button>
                            </div>
                        </div>

                        <p class="max-w-md text-gray-700">{!! $product->description !!}
                        </p>
                    </div>


                </div>
            </div>
        </div>
    </div>
    @if ($product->faqs->count() > 0)
        <div class="container-content">
            <h2 class="text-2xl font-bold text-gray-700"></h2>
            <div class="join join-vertical w-full">
                @foreach ($product->faqs as $key => $faq)
                    <div class="collapse collapse-arrow join-item border border-base-300">
                        <input type="radio" name="my-accordion-4" @if ($key === 0) checked="checked" @endif />
                        <div class="collapse-title text-xl font-medium">
                            {{ $faq->question }}
                        </div>
                        <div class="collapse-content">
                            <p>{{ $faq->answer }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    @endif

</x-app-container>
