<div class="text-center">
    <div class="flex justify-center items-center size-12 bg-black/80 outline outline-offset-0 outline-8 outline-primary/20 rounded-full mx-auto ">
        {{ $slot }}
    </div>
    <div class="mt-3">
        <h3 class="text-lg font-semibold text-gray-800">{{ $title }}</h3>
        <p class="mt-1 text-black ">{{ $subtitle }}</p>
    </div>
</div>
