@props(['active' => false, 'category'])


@php
    $claseses = $active ?? false ? 'bg-primary text-white' : 'bg-background3 hover:bg-primary hover:stroke-primary hover:text-white';
    $clasesesIcon = $active ?? false ? 'stroke-white' : 'stroke-primary';
@endphp

<a href="/products?category={{ $category->slug }}" wire:key="{{ $category->slug }}" wire:navigate
    class="group inline-flex items-center gap-2 px-[14px] py-[9px] rounded-2xl
        transition duration-300 font-light {{ $claseses }} shadow-sm">{{ $category->name }}

</a>
