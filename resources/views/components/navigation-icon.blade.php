<div class="flex items-center order-2 space-x-1 sm:space-x-2">
    <x-navbar-search />
    <div class="dropdown dropdown-end">
        <div tabindex="0" role="button" class="btn btn-ghost btn-sm md:btn-md btn-circle hover:bg-gray-50 ">
            <div class="indicator">
                <x-tabler-shopping-cart />
                <span class="badge badge-sm bg-secondary2 indicator-item text-white ">0</span>
            </div>
        </div>
        <div tabindex="0" class="mt-3 z-[1] card card-compact dropdown-content w-52 bg-base-100 shadow">
            <div class="card-body">
                <span class="font-bold text-lg">8 Items</span>
                <span class="text-info">Subtotal: $999</span>
                <div class="card-actions">
                    <button class="btn btn-error btn-block">View cart</button>
                </div>
            </div>
        </div>
    </div>
    @if (auth()->guard('customer')->check())
        <div class="dropdown dropdown-end">
            <div tabindex="0" role="button" class="btn btn-ghost btn-sm md:btn-md btn-circle avatar hover:bg-gray-50">
                <div class="w-10 rounded-full">
                    <img alt="Profile picture" src="{{ auth()->guard('customer')->user()->profile_photo_url }}" />
                </div>
            </div>
            <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                <li><a href="/profile" wire:navigate>Profile</a></li>
                <li><a>Settings</a></li>
                <li> <livewire:button.logout-button /></li>
            </ul>
        </div>
    @else
        <a href="/login" class="btn btn-ghost btn-circle btn-sm md:btn-md inline-flex hover:bg-gray-50 " wire:navigate title="Login">
            <x-tabler-user />
        </a>
    @endif
    <div class="lg:hidden">
        <label for="mobile-menu" class="btn btn-ghost btn-circle btn-sm md:btn-md hover:bg-gray-50 ">
            <x-tabler-menu-2 class="" />
        </label>
    </div>
</div>
