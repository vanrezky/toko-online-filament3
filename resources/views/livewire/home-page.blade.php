<x-app-container>
    {{-- start categories & slider --}}
    <div class="flex mt-4 lg:mt-10">
        <div class="hidden w-1/5 lg:flex flex-shrink-0">
            <nav class="-mt-2">
                @foreach ($categories->take(10) as $category)
                    <a href="#" class="block py-2  px-4 text-text2 hover:underline underline-offset-4 hover:bg-white">{{ $category->name }}</a>
                @endforeach
            </nav>
        </div>
        <div class="hidden lg:block border-r border-gray-200"></div>
        <div class="sm:w-full lg:w-4/5">
            <div class="lg:ml-[45px]">
                <x-sliders :$sliders />
            </div>

        </div>
    </div>
    {{-- end categories & slider --}}

    {{-- start flash sales product --}}
    @if ($flashSales->count() > 0)
        <x-home-card-flashsales title="Today's" subtitle="Flashsales" link="/products/?status=flashsales" sliderkey="flashsales" :products="$flashSales" />
    @endif
    {{-- end flash sales product --}}

    {{-- start categories --}}
    @if ($categories->count() > 0)
        <x-home-card title="Categories" subtitle="Browse By Categories" sliderkey="categories">
            <livewire:home.categories :$categories lazy sliderkey="categories" />
        </x-home-card>
    @endif
    {{-- end categories --}}


    {{-- start best selling product --}}
    <x-home-card title="This Month" subtitle="Best Selling Products" link="/products/?status=bestselling">
        <livewire:home.products :products="$bestSelling" lazy />
    </x-home-card>
    {{-- end best selling  product --}}

    {{-- start our product --}}
    <x-home-card title="Our Products" subtitle="Our Best Products" link="/products/">
        <livewire:home.products :products="$products" lazy />
    </x-home-card>
    {{-- end our product --}}


    {{-- start new arrival --}}
    <x-home-new-arrival />

    {{-- end new arrival --}}


    {{-- start icon sections --}}
    <!-- Icon Blocks -->
    <x-home-icon-block />
    <!-- End Icon Blocks -->
    {{-- end icon sections --}}
</x-app-container>
