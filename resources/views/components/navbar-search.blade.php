@props(['mobile' => false])

@if ($mobile)
@else
    <div class="relative w-full hidden md:block">
        <form action="">
            <input type="text" class="text-xs font-sm peer py-2 px-3 pe-11  w-[240px] h-[38px] bg-secondary border-transparent rounded-md disabled:opacity-50 disabled:pointer-events-none focus:outline-black"
                placeholder="{{ __('What are you looking for?') }}">
            <div class="absolute inset-y-0 flex items-center pointer-events-none end-0 pe-4 peer-disabled:opacity-50 peer-disabled:pointer-events-none">
                <x-tabler-search class="size-6 stroke-black" />
            </div>
        </form>
    </div>
@endif
