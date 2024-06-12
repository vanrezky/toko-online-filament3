<div class="mx-auto w-full px-4 lg:px-[120px] bg-background1">
    <div class="flex items-center justify-between gap-x-5 w-full py-2">
        <p class="text-sm font-light text-text hover:text-neutral-500">
            {{ settings('site_tag_line') }}
        </p>
        <div class="flex items-center gap-x-2 sm:gap-x-4">
            <x-topbar-item>
                <x-tabler-map-pin class="stroke-primary size-4" />
                <span class="hidden sm:block"> Deliver to <strong>42365</strong></span>
            </x-topbar-item>
            <div class="h-6 border-l border-border"></div> <!-- Separator -->
            <x-topbar-item>
                <x-tabler-truck-delivery class="stroke-primary size-4" />
                <span class="hidden sm:block">Track your order</span>
            </x-topbar-item>
            <div class="h-6 border-l border-border"></div> <!-- Separator -->
            <x-topbar-item>
                <x-tabler-discount class="stroke-primary size-4" />
                <span class="hidden sm:block"> All offers</span>
            </x-topbar-item>
        </div>
    </div>
</div>
