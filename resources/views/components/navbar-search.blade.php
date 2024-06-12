@props(['mobile' => false])

@php
    $claseses = $mobile ? 'w-full h-[48px]' : 'lg:w-[250px] xl:w-[350px] h-[48px]';
@endphp

<div class="relative w-full">
    <input type="text" class="w-full text-sm font-light peer py-3 px-4 ps-11 {{ $claseses }} bg-background3 border-transparent rounded-md disabled:opacity-50 disabled:pointer-events-none" placeholder="Search essentials, groceries and more...">
    <div class="absolute inset-y-0 flex items-center pointer-events-none start-0 ps-4 peer-disabled:opacity-50 peer-disabled:pointer-events-none">
        <x-tabler-search class="size-6 stroke-primary" />
    </div>
</div>
