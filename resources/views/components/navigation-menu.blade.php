@props(['mobile' => false, 'categories'])


@if (!$mobile)
    <div>
        <div class="hidden lg:flex justify-between items-center w-full px-4 py-2">
            <ul class="flex space-x-10">
                <li><a href="/" class="text-text2 leading-6 underline-offset-4 hover:underline" wire:navigate>{{ __('Home') }}</a></li>
                <li><a href="/contact" class="text-text2 leading-6 underline-offset-4 hover:underline" wire:navigate>{{ __('Contact') }}</a></li>
                <li><a href="/about" class="text-text2 leading-6 underline-offset-4 hover:underline" wire:navigate>{{ __('About') }}</a></li>
                <li><a href="/blog" class="text-text2 leading-6 underline-offset-4 hover:underline" wire:navigate>{{ __('Blogs') }}</a></li>
                {{-- <li class="dropdown dropdown-hover">
            <a tabindex="0" class="text-text2 hover:underline">{{ __('Categories') }}</a>
            <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52 mt-2">
                @foreach ($categories as $category)
                    <li><a href="/products?categories={{ $category->slug }}" wire:navigate>{{ $category->name }}</a></li>
                @endforeach
            </ul>
        </li> --}}
            </ul>
        </div>
    </div>
@else
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
@endif
