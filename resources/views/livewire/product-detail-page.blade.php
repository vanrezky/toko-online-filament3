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
                    </div>
                </div>
            </div>
            <div class="w-full px-4 md:w-1/2">
                <div class="lg:pl-[70px]">
                    <div class="mx-auto bg-white overflow-hidden ">
                        <div class="">
                            <div class="uppercase tracking-wide text-xl text-black font-semibold">{{ $product->name }}</div>
                            <div class="flex items-center mt-2 gap-2">
                                <span class="text-yellow-400">★★★★☆</span>
                                <span class=" text-gray-600">(150 Reviews)</span>
                                <span class=" text-green-500 border-l pl-2 border-gray-500 ">In Stock</span>
                            </div>
                            <div class="mt-2 flex gap-3 items-center">
                                <div class="text-black text-3xl font-bold">{{ $product->price_currency }}</div>
                                @if ($product->sale_price)
                                    <span class="text-lg font-normal text-gray-500 line-through">{{ $product->sale_price_currency }}</span>
                                @endif
                            </div>


                            <div class="mt-4">
                                {{-- <div class="flex items-center mb-2">
                                    <span class="text-gray-700">Colours:</span>
                                    <div class="ml-2 flex space-x-2">
                                        <div class="w-6 h-6 bg-blue-500 rounded-full"></div>
                                        <div class="w-6 h-6 bg-red-500 rounded-full"></div>
                                    </div>
                                </div> --}}
                                {{-- <div class="flex items-center mb-4">
                                    <span class="text-gray-700">Size:</span>
                                    <div class="ml-2 flex space-x-2">
                                        <button class="px-3 py-1 bg-gray-200 text-gray-700 rounded">XS</button>
                                        <button class="px-3 py-1 bg-gray-200 text-gray-700 rounded">S</button>
                                        <button class="px-3 py-1 bg-red-500 text-white rounded">M</button>
                                        <button class="px-3 py-1 bg-gray-200 text-gray-700 rounded">L</button>
                                        <button class="px-3 py-1 bg-gray-200 text-gray-700 rounded">XL</button>
                                    </div>
                                </div> --}}
                                <div class="flex items-center mb-4 gap-2">
                                    <div class="w-full">
                                        <button class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-button2 hover:text-white">-</button>
                                        <input type="text" class="w-12 text-center mx-2 border rounded" value="2">
                                        <button class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-button2 hover:text-white">+</button>
                                    </div>

                                    <button class="ml-2 w-full py-2 px-4 bg-red-500 text-white rounded">Buy Now</button>
                                </div>

                            </div>
                            <div class="mt-6 gap-0">
                                <div class="flex items-center mb-2 border-gray-300 border rounded-lg  p-5">
                                    <x-tabler-truck-delivery class="size-8" />
                                    <div class="ml-4">
                                        <p class=" text-gray-700">Free Delivery</p>
                                        <a href="#" class="ml-auto text-indigo-500 underline">Enter your postal code for Delivery Availability</a>
                                    </div>
                                </div>
                                <div class="flex items-center mb-2 border-gray-300 border rounded-lg  p-5">
                                    <x-tabler-refresh class="size-8" />
                                    <div class="ml-4">
                                        <p class=" text-gray-700">Return Delivery</p>
                                        <a href="#" class="ml-auto text-indigo-500 underline">Free 30 Days Delivery Returns. Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-content">
        <div role="tablist" class="tabs tabs-lifted p-0">
            <input type="radio" name="my_tabs_2" role="tab" class="tab" aria-label="{{ __('Description') }}" checked />
            <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
                <div>
                    {!! $product->description !!}
                </div>
            </div>

            @if ($product->faqs->count() > 0)
                <input type="radio" name="my_tabs_2" role="tab" class="tab " aria-label="{{ __('FAQ') }}" />
                <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
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
        </div>
    </div>

</x-app-container>
