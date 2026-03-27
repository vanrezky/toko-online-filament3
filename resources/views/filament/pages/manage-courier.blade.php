<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <div class="flex justify-start">
            <x-filament::button type="submit">
                {{ __('Save Settings') }}
            </x-filament::button>
        </div>
    </x-filament-panels::form>

    <x-filament::section>
        <x-slot name="heading">
            {{ __('Available Couriers') }}
        </x-slot>
        <x-slot name="description">
            {{ __('Toggle status to activate or deactivate courier in checkout.') }}
        </x-slot>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($this->getCouriers() as $courier)
                <div class="fi-card flex flex-col rounded-xl border bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900"
                    @if ($courier->is_active)
                        style="border-color: rgb(34 197 94 / 0.5); background-color: rgb(34 197 94 / 0.05);"
                    @else
                        style="border-color: rgb(239 68 68 / 0.5); background-color: rgb(239 68 68 / 0.05);"
                    @endif
                >
                    <div class="flex items-center gap-3">
                        <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg border border-gray-100 bg-white p-1 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            @if ($courier->logo)
                                <img src="{{ $courier->logo_url }}" alt="{{ $courier->name }}"class="max-h-full max-w-full object-contain">
                            @else
                                <x-heroicon-o-truck class="h-8 w-8 text-gray-400" />
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <h3 class="truncate text-base font-semibold"style="{{ $courier->is_active ? 'color: rgb(22 163 74);' : 'color: rgb(220 38 38);' }}">
                                {{ $courier->name }}
                            </h3>
                            <p class="truncate text-sm text-gray-500 dark:text-gray-400">
                                {{ $courier->fullname }}
                            </p>
                        </div>

                        <button
                            wire:click="toggleStatus({{ $courier->id }})"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                            @if ($courier->is_active)
                                style="background-color: rgb(34 197 94);"
                            @else
                                style="background-color: rgb(156 163 175);"
                            @endif
                        >
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                @if ($courier->is_active)
                                    style="transform: translateX(1.25rem);"
                                @else
                                    style="transform: translateX(0);"
                                @endif
                            ></span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-panels::page>