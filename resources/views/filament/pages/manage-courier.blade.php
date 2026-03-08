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
            {{ __('Toogle status to activate or deactivate courier in checkout.') }}
        </x-slot>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 mt-4">
            @foreach ($this->getCouriers() as $courier)
                <div @class([
                    'flex items-center p-3 rounded-xl border transition-all duration-300 shadow-sm hover:shadow-md h-full',
                    'bg-success-50/50 border-success-200 dark:bg-success-500/10 dark:border-success-500/20' =>
                        $courier->is_active,
                    'bg-danger-50/50 border-danger-200 dark:bg-danger-500/10 dark:border-danger-500/20' => !$courier->is_active,
                ])>
                    <div class="flex-shrink-0 mr-3">
                        <div
                            class="w-14 h-14 flex items-center justify-center rounded-lg bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 overflow-hidden p-1 shadow-inner">
                            @if ($courier->logo)
                                <img src="{{ $courier->logo_url }}" alt="{{ $courier->name }}"
                                    class="max-w-full max-h-full object-contain">
                            @else
                                <x-heroicon-o-truck class="w-8 h-8 text-gray-400" />
                            @endif
                        </div>
                    </div>

                    <div class="flex-grow min-w-0">
                        <h3 @class([
                            'text-lg font-bold truncate',
                            'text-success-800 dark:text-success-400' => $courier->is_active,
                            'text-danger-800 dark:text-danger-400' => !$courier->is_active,
                        ])>
                            {{ $courier->name }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1">
                            {{ $courier->fullname }}
                        </p>
                    </div>

                    <div class="ml-4 flex-shrink-0">
                        <button wire:click="toggleStatus({{ $courier->id }})" @class([
                            'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2',
                            'bg-primary-600' => $courier->is_active,
                            'bg-gray-200 dark:bg-gray-700' => !$courier->is_active,
                        ])>
                            <span @class([
                                'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                'translate-x-5' => $courier->is_active,
                                'translate-x-0' => !$courier->is_active,
                            ])></span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-panels::page>
