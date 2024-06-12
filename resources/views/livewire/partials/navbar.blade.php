<div class="relative z-50 w-full">
    <header class="flex flex-wrap pb-2 mb-3 text-sm bg-white border-b border-gray-200 sm:mb-4 sm:flex-col sm:pb-0 dark:bg-neutral-800 dark:border-neutral-700">
        <!-- Topbar -->
        <x-topbar />
        <!-- End Topbar -->

        {{-- head --}}
        <nav class="w-full mx-auto px-4 py-3 sm:py-5 lg:px-[120px] flex items-center justify-between bg-white border-b border-border" aria-label="Global">
            <div class="flex items-center justify-between">
                <a class="flex items-center flex-none text-3xl font-semibold text-primary" href="/" wire:navigate aria-label="{{ settings('site_name') }}">
                    @if (settings('logo'))
                        <img src="{{ settings('logo') }}" alt="{{ settings('site_name') }}" class="max-w-36 h-auto">
                    @else
                        {{ settings('site_name') }}
                    @endif
                </a>
            </div>
            <div>
                <div class="hidden lg:flex justify-between items-center w-full px-4 py-2">
                    <ul class="flex space-x-4">
                        <li><a href="/" class="hover:text-primary" wire:navigate>{{ __('Home') }}</a></li>
                        <li class="dropdown dropdown-hover">
                            <a tabindex="0" class="hover:text-primary">{{ __('Categories') }}</a>
                            <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52 mt-2">
                                @foreach ($categories as $category)
                                    <li><a href="/products?categories={{ $category->slug }}" wire:navigate>{{ $category->name }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                        <li><a href="/about" class="hover:text-primary" wire:navigate>{{ __('About') }}</a></li>
                        <li><a href="/contact" class="hover:text-primary" wire:navigate>{{ __('Contact') }}</a></li>
                    </ul>
                </div>
            </div>

            <div class="flex items-center order-2 space-x-1 sm:space-x-2">
                <div role="button" class="btn btn-ghost btn-sm md:btn-md btn-circle inline-flex hover:bg-primary/80 hover:text-white" title="Search">
                    <x-tabler-search />
                </div>
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-sm md:btn-md btn-circle hover:bg-primary/80 hover:text-white">
                        <div class="indicator">
                            <x-tabler-shopping-cart />
                            <span class="badge badge-sm indicator-item text-text">0</span>
                        </div>
                    </div>
                    <div tabindex="0" class="mt-3 z-[1] card card-compact dropdown-content w-52 bg-base-100 shadow">
                        <div class="card-body">
                            <span class="font-bold text-lg">8 Items</span>
                            <span class="text-info">Subtotal: $999</span>
                            <div class="card-actions">
                                <button class="btn btn-primary btn-block">View cart</button>
                            </div>
                        </div>
                    </div>
                </div>
                @if (auth()->guard('customer')->check())
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-sm md:btn-md btn-circle avatar hover:bg-primary/80">
                            <div class="w-10 rounded-full">
                                <img alt="Tailwind CSS Navbar component" src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.jpg" />
                            </div>
                        </div>
                        <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                            <li><a href="">Profile</a></li>
                            <li><a>Settings</a></li>
                            <li> <livewire:button.logout-button /></li>
                        </ul>
                    </div>
                @else
                    <a href="/login" class="btn btn-ghost btn-circle btn-sm md:btn-md inline-flex hover:bg-primary/80 hover:text-white" wire:navigate title="Login">
                        <x-tabler-user />
                    </a>
                @endif

                <div class="lg:hidden">
                    <label for="mobile-menu" class="btn btn-ghost btn-circle btn-sm md:btn-md hover:bg-primary/80 hover:text-white">
                        <x-tabler-menu class="" />
                    </label>
                </div>
            </div>
        </nav>
        {{-- end head --}}
        {{-- start input in mobile --}}
        <div class="w-full px-4 py-2 lg:hidden">
            <x-navbar-search :mobile="true" />
        </div>
        {{-- end input in mobile --}}
        {{-- start menu --}}

    </header>
    <input type="checkbox" id="mobile-menu" class="drawer-toggle">
    <div class="drawer-side lg:hidden fixed inset-0 z-[60] h-full"> <!-- Ensuring full height and z-index -->
        <label for="mobile-menu" class="drawer-overlay"></label>
        <ul class="menu p-4 w-80 bg-base-100 h-full"> <!-- Ensuring full height for the menu -->
            <li><a href="/">{{ __('Home') }}</a></li>
            <li>
                <details>
                    <summary>{{ __('Categories') }}</summary>
                    <ul>
                        @foreach ($categories as $category)
                            <li><a href="/products?categories={{ $category->slug }}">{{ $category->name }}</a></li>
                        @endforeach
                    </ul>
                </details>
            </li>
            <li><a href="/about">About</a></li>
            <li><a href="/contact">Contact</a></li>
        </ul>
    </div>


</div>
