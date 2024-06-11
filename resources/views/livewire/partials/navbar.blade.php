<header class="z-50 flex flex-wrap w-full pb-2 mb-3 text-sm bg-white border-b border-gray-200 sm:mb-4 sm:justify-start sm:flex-col sm:pb-0 dark:bg-neutral-800 dark:border-neutral-700">
    <!-- Topbar -->
    <x-topbar />
    <!-- End Topbar -->
    {{-- head --}}
    <nav class="w-full mx-auto px-4 py-3 sm:py-5 lg:px-[120px] flex items-center justify-between bg-white border-b border-border" aria-label="Global">
        <div class="flex items-center justify-between order-1">
            <a class="flex items-center flex-none text-3xl font-semibold text-primary" href="/" wire:navigate aria-label="{{ settings('site_name') }}">
                @if (settings('logo'))
                    <img src="{{ settings('logo') }}" alt="{{ settings('site_name') }}" class="max-w-36 h-auto">
                @else
                    {{ settings('site_name') }}
                @endif
            </a>
        </div>
        <div class="lg:hidden">
            <button type="button" class="group size-[38px] flex justify-center items-center  border text-sm font-semibold rounded-xl border-primary/20 bg-background1 sm:order-1 hs-collapse-toggle hover:bg-primary"
                data-hs-collapse="#navbar-collapse-with-animation" aria-controls="navbar-collapse-with-animation" aria-label="Toggle navigation">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="flex-shrink-0 stroke-2 size-6 stroke-primary group-hover:stroke-white hs-collapse-open:hidden">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                </svg>
                <svg class="flex-shrink-0 hidden hs-collapse-open:block size-4 group-hover:stroke-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>
        </div>
        <div class="flex items-center order-2 space-x-2 sm:space-x-4">
            <div class="hidden lg:block">
                <x-navbar-search />
            </div>
            <a href="/login" class="inline-flex items-center gap-2" wire:navigate>
                <x-tabler-user class="stroke-primary" />
                <span class="hidden font-semibold text-text sm:block hover:text-light">Sign In/Sign Up</span>
            </a>
            <div class="h-6 border-l border-border"></div> <!-- Separator -->
            <a href="/cart" wire:navigate class="inline-flex items-center gap-2">
                <x-tabler-shopping-cart class="stroke-primary" />
                <span class="hidden font-semibold text-text sm:block ">Cart</span>
            </a>
        </div>
    </nav>
    {{-- end head --}}
    {{-- start input in mobile --}}
    <div class="w-full px-4 py-2 lg:hidden">
        <x-navbar-search :mobile="true" />


    </div>
    {{-- end input in mobile --}}
    {{-- start menu --}}

    <x-nav-menu :$categories />

    </div>
    {{-- end menu --}}
</header>
