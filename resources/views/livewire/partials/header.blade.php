<div class="relative z-50 w-full">
    <header class=" flex flex-wrap pb-2  border-b border-gray-200 text-sm  sm:flex-col sm:pb-0">
        <x-topbar />

        {{-- head --}}
        <nav class="margin-content w-full mx-auto flex items-center justify-between" aria-label="Global">
            <div class="flex items-center">
                <x-navigation-logo />
            </div>
            <div class="flex-1 flex justify-center">
                <x-navigation-menu />
            </div>
            <div class="flex items-center">
                <x-navigation-icon />
            </div>
        </nav>
        {{-- end head --}}

        {{-- start input in mobile --}}
        {{-- <div class="w-full px-4 py-2 lg:hidden">
            <x-navbar-search :mobile="true" />
        </div> --}}
        {{-- end input in mobile --}}
        {{-- start menu --}}
    </header>
    <x-navigation-menu :mobile="true" :categories="$categories" />
</div>
