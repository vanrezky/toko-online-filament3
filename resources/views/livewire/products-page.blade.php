<x-app-container>
    <div class="container-content flex flex-wrap mb-24 -mx-3">
        <div class="w-full pr-2 lg:w-1/5 lg:block">
            <x-products-card-filter title="Categories">
                <ul>
                    <li class="mb-4">
                        <label for="" class="flex items-center  ">
                            <input type="checkbox" class="w-4 h-4 mr-2">
                            <span class="text-lg">Smartphones</span>
                        </label>
                    </li>
                    <li class="mb-4">
                        <label for="" class="flex items-center  ">
                            <input type="checkbox" class="w-4 h-4 mr-2 ">
                            <span class="text-lg">Laptops</span>
                        </label>
                    </li>
                    <li class="mb-4">
                        <label for="" class="flex items-center ">
                            <input type="checkbox" class="w-4 h-4 mr-2">
                            <span class="text-lg">Smartwatches</span>
                        </label>
                    </li>
                    <li class="mb-4">
                        <label for="" class="flex items-center ">
                            <input type="checkbox" class="w-4 h-4 mr-2">
                            <span class="text-lg">Television</span>
                        </label>
                    </li>
                </ul>
            </x-products-card-filter>
            <x-products-card-filter title="Brand">
                <ul>
                    <li class="mb-4">
                        <label for="" class="flex items-center">
                            <input type="checkbox" class="w-4 h-4 mr-2">
                            <span class="text-lg ">Apple</span>
                        </label>
                    </li>
                    <li class="mb-4">
                        <label for="" class="flex items-center">
                            <input type="checkbox" class="w-4 h-4 mr-2">
                            <span class="text-lg ">Samsung</span>
                        </label>
                    </li>
                    <li class="mb-4">
                        <label for="" class="flex items-center">
                            <input type="checkbox" class="w-4 h-4 mr-2">
                            <span class="text-lg ">Nothing</span>
                        </label>
                    </li>
                    <li class="mb-4">
                        <label for="" class="flex items-center">
                            <input type="checkbox" class="w-4 h-4 mr-2">
                            <span class="text-lg ">One Plus</span>
                        </label>
                    </li>
                </ul>
            </x-products-card-filter>

            <x-products-card-filter title="Product Status">
                <ul>
                    <li class="mb-4">
                        <label for="" class="flex items-center">
                            <input type="checkbox" class="w-4 h-4 mr-2">
                            <span class="text-lg ">In Stock</span>
                        </label>
                    </li>
                    <li class="mb-4">
                        <label for="" class="flex items-center">
                            <input type="checkbox" class="w-4 h-4 mr-2">
                            <span class="text-lg ">On Sale</span>
                        </label>
                    </li>
                </ul>
            </x-products-card-filter>

            <x-products-card-filter title="price">
                <div>
                    <input type="range" class="w-full h-1 mb-4 bg-primary/40 rounded appearance-none cursor-pointer" max="500000" value="100000" step="100000">
                    <div class="flex justify-between ">
                        <span class="inline-block text-lg font-bold text-primary "> {{ toMoney(1000) }}</span>
                        <span class="inline-block text-lg font-bold text-primary "> {{ toMoney(5000000) }}</span>
                    </div>
                </div>

            </x-products-card-filter>
        </div>

        <div class="w-full px-3 lg:w-4/5">
            <div class=" mb-4">
                <div class="items-center justify-between hidden px-3 py-2 bg-gray-100 md:flex ">
                    <div class="flex items-center justify-between">
                        <select name="" id="" class="block w-40 text-base bg-gray-100 cursor-pointer ">
                            <option value="">Sort by latest</option>
                            <option value="">Sort by Price</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="home-card grid grid-cols-2 gap-4 md:grid-cols-2 lg:grid-cols-4">
                @foreach ($products as $product)
                    <x-product-item :$product href="/products/{{ $product->slug }}" wire:navigate />
                @endforeach
            </div>
            <!-- pagination start -->
            <div class="flex justify-end mt-6">
                <nav aria-label="page-navigation">
                    <ul class="flex list-style-none">
                        <li class="page-item disabled ">
                            <a href="#" class="relative block pointer-events-none px-3 py-1.5 mr-3 text-base text-gray-700 transition-all duration-300  rounded-md  hover:text-gray-100 hover:bg-blue-600">Previous
                            </a>
                        </li>
                        <li class="page-item ">
                            <a href="#" class="relative block px-3 py-1.5 mr-3 text-base hover:text-blue-700 transition-all duration-300 hover:bg-blue-200 rounded-md text-gray-100 bg-primary">1
                            </a>
                        </li>
                        <li class="page-item ">
                            <a href="#" class="relative block px-3 py-1.5 text-base text-gray-700 transition-all duration-300 hover:bg-blue-100 rounded-md mr-3  ">2
                            </a>
                        </li>
                        <li class="page-item ">
                            <a href="#" class="relative block px-3 py-1.5 text-base text-gray-700 transition-all duration-300 hover:bg-blue-100 rounded-md mr-3 ">3
                            </a>
                        </li>
                        <li class="page-item ">
                            <a href="#" class="relative block px-3 py-1.5 text-base text-gray-700 transition-all duration-300 hover:bg-blue-100 rounded-md ">Next
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <!-- pagination end -->
        </div>
    </div>
</x-app-container>
