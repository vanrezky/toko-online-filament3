<div class="flex items-center justify-between">
    <a class="flex items-center flex-none text-2xl font-semibold text-black" href="/" wire:navigate aria-label="{{ settings('site_name') }}">
        @if (!empty(settings('logo')))
            <img src="{{ settings('logo') }}" alt="{{ settings('site_name') }}" class="max-w-28 sm:max-w-36   h-auto">
        @else
            {{ settings('site_name') }}
        @endif
    </a>
</div>
