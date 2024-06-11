@props(['label' => null, 'parentClass' => null])

@php
    $parentClass = $parentClass ?? 'mb-4';
@endphp

@if ($label)
    <div class="{{ $parentClass }}">
        <label for="{{ $attributes['name'] }}" class="block text-gray-700 mb-2">{{ $label }}</label>
@endif

<input {{ $attributes->merge(['class' => 'form-primary block w-full text-base font-light border rounded-lg px-4 py-2 disabled:opacity-50 disabled:pointer-events-none']) }}>
<div>
    @error($attributes['name'])
        <span class="text-red-500 text-sm ">{{ $message }}</span>
    @enderror
</div>
@if ($label)
    </div>
@endif
