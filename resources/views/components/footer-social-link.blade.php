<a
    {{ $attributes->merge([
        'class' =>
            'inline-flex items-center justify-center w-10 h-10 text-sm font-semibold text-white border border-transparent rounded-lg gap-x-2 hover:bg-white/10 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:ring-1 focus:ring-gray-600',
    ]) }}>
    {{ $slot }}
</a>
