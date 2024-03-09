<x-filament-panels::page>
    <div class="flex flex-wrap gap-6">
        <div class="w-full md:w-1/2 lg:w-1/3">
            <div class="bg-white border rounded-xl shadow-md p-4">
                <img src="{{ $customer->first_name }}" alt="Card 1" class="w-full rounded-full">
                <h2 class="text-xl font-bold mt-4">Card 1 Title</h2>
                <p class="text-gray-700">Card 1 content. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.</p>
            </div>
        </div>
        <div class="w-full md:w-1/2 lg:w-1/3">
            <div class="bg-white border rounded-xl shadow-md p-4">
                <img src="image2.jpg" alt="Card 2" class="w-full rounded">
                <h2 class="text-xl font-bold mt-4">Card 2 Title</h2>
                <p class="text-gray-700">Card 2 content. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.</p>
            </div>
        </div>
    </div>

    {{-- <x-filament-panels::form>
        {{ $this->form }}
    </x-filament-panels::form> --}}

    <x-filament-panels::form wire:submit="update">
        {{ $this->form }}
        <x-filament-panels::form.actions :actions="$this->getFormActions()" />
    </x-filament-panels::form>
</x-filament-panels::page>
