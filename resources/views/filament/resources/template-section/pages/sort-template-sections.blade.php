<x-filament-panels::page>
    {{-- Load SortableJS from CDN --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    @endpush

    <div
        class="space-y-4"
        x-data="{
            sortable: null,
            init() {
                const el = this.$refs.sortableList;
                this.sortable = Sortable.create(el, {
                    animation: 200,
                    handle: '.drag-handle',
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    onEnd: () => {
                        const orderedIds = [...el.querySelectorAll('[data-id]')]
                            .map(item => parseInt(item.dataset.id));
                        $wire.updateOrder(orderedIds);
                    }
                });
            }
        }"
    >
        {{-- Header Info --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        Drag to reorder sections
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Template: <span class="font-medium text-primary-600 dark:text-primary-400">{{ $this->templateRecord->name }}</span>
                        &bull; {{ count($sections) }} section(s)
                    </p>
                </div>

                <a
                    href="{{ \App\Filament\Resources\TemplateSectionResource::getUrl('create') }}?template_id={{ $this->templateRecord->id }}"
                    class="fi-btn fi-btn-size-md inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
                >
                    <x-heroicon-o-plus class="h-4 w-4" />
                    Add Section
                </a>
            </div>
        </div>

        @if (count($sections) === 0)
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-10 text-center">
                <x-heroicon-o-squares-2x2 class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" />
                <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">No sections yet. Add your first section above.</p>
            </div>
        @else
            {{-- Sortable List --}}
            <ul
                x-ref="sortableList"
                class="space-y-2"
            >
                @foreach ($sections as $section)
                    <li
                        data-id="{{ $section['id'] }}"
                        wire:key="section-{{ $section['id'] }}"
                        class="group fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 transition-shadow duration-150"
                    >
                        <div class="flex items-center gap-4 p-4">

                            {{-- Drag Handle --}}
                            <div class="drag-handle cursor-grab active:cursor-grabbing flex-shrink-0 text-gray-400 hover:text-gray-600 dark:text-gray-600 dark:hover:text-gray-400 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5" />
                                </svg>
                            </div>

                            {{-- Order Badge --}}
                            <span class="flex-shrink-0 inline-flex h-7 w-7 items-center justify-center rounded-full bg-primary-50 text-xs font-bold text-primary-700 dark:bg-primary-950 dark:text-primary-300 ring-1 ring-primary-200 dark:ring-primary-800">
                                {{ $section['order_priority'] }}
                            </span>

                            {{-- Type Badge --}}
                            <span class="flex-shrink-0 inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-800 px-2.5 py-0.5 text-xs font-medium text-gray-700 dark:text-gray-300 ring-1 ring-inset ring-gray-300/50 dark:ring-gray-700">
                                {{ $section['type_label'] }}
                            </span>

                            {{-- Section Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $section['name'] }}
                                </p>
                                @if ($section['description'])
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                                        {{ $section['description'] }}
                                    </p>
                                @endif
                            </div>

                            {{-- Fields Count --}}
                            <span class="flex-shrink-0 hidden sm:inline-flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                <x-heroicon-o-list-bullet class="h-3.5 w-3.5" />
                                {{ $section['fields_count'] }} field(s)
                            </span>

                            {{-- Active Toggle --}}
                            <button
                                wire:click="toggleActive({{ $section['id'] }})"
                                type="button"
                                class="flex-shrink-0 relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2 {{ $section['is_active'] ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700' }}"
                                title="{{ $section['is_active'] ? 'Deactivate' : 'Activate' }}"
                            >
                                <span class="sr-only">Toggle active</span>
                                <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $section['is_active'] ? 'translate-x-4' : 'translate-x-0' }}"></span>
                            </button>

                            {{-- Actions --}}
                            <div class="flex-shrink-0 flex items-center gap-2">
                                <a
                                    href="{{ \App\Filament\Resources\TemplateSectionResource::getUrl('edit', ['record' => $section['uuid']]) }}"
                                    class="inline-flex items-center gap-1 rounded-md bg-gray-100 dark:bg-gray-800 px-2.5 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
                                    title="Edit Section"
                                >
                                    <x-heroicon-o-pencil-square class="h-3.5 w-3.5" />
                                    Edit
                                </a>
                            </div>

                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        {{-- Save hint --}}
        @if (count($sections) > 1)
            <p class="text-center text-xs text-gray-400 dark:text-gray-600">
                <x-heroicon-o-information-circle class="inline h-3.5 w-3.5 mr-0.5 -mt-0.5" />
                Order is saved automatically after each drag.
            </p>
        @endif
    </div>

    <style>
        .sortable-ghost {
            opacity: 0.4;
            background: #e0e7ff;
            border-radius: 0.75rem;
        }
        .sortable-chosen {
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15), 0 4px 6px -2px rgba(0,0,0,0.05);
        }
        .sortable-drag {
            opacity: 0.9;
        }
        .dark .sortable-ghost {
            background: #1e1b4b;
        }
    </style>
</x-filament-panels::page>
