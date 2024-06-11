@props(['categories'])

<div id="navbar-collapse-with-animation" class="hidden overflow-hidden w-full mx-auto px-4 py-4 lg:px-[120px] transition-all duration-300 hs-collapse basis-full grow lg:block lg:w-auto md:basis-auto lg:order-2 lg:col-span-6">
    <div class="flex flex-col gap-y-4 gap-x-0 md:flex-row md:justify-center md:items-center md:gap-y-0 md:gap-x-4 md:mt-0">
        @foreach ($categories as $category)
            <x-nav-link :active="request()->input('category') == $category->slug" :category="$category" />
        @endforeach
    </div>
