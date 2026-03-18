<?php

namespace App\Filament\Resources\TemplateSectionResource\Pages;

use App\Filament\Resources\TemplateSectionResource;
use App\Models\Template;
use App\Models\TemplateSection;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Livewire\Attributes\On;

class SortTemplateSections extends Page
{
    protected static string $resource = TemplateSectionResource::class;

    protected static string $view = 'filament.resources.template-section.pages.sort-template-sections';

    public Template $templateRecord;

    public array $sections = [];

    public function mount(string $template): void
    {
        $this->templateRecord = Template::where('uuid', $template)
            ->orWhere('id', $template)
            ->firstOrFail();

        $this->loadSections();
    }

    protected function loadSections(): void
    {
        $this->sections = $this->templateRecord
            ->sections()
            ->get()
            ->map(fn (TemplateSection $section) => [
                'id'             => $section->id,
                'uuid'           => $section->uuid,
                'name'           => $section->name,
                'type'           => $section->type,
                'type_label'     => TemplateSection::types()[$section->type] ?? $section->type,
                'description'    => $section->description,
                'icon'           => $section->icon,
                'is_active'      => $section->is_active,
                'order_priority' => $section->order_priority,
                'fields_count'   => $section->fields()->count(),
            ])
            ->toArray();
    }

    /**
     * Called from Alpine / SortableJS via $wire.updateOrder(orderedIds).
     */
    public function updateOrder(array $orderedIds): void
    {
        foreach ($orderedIds as $position => $id) {
            TemplateSection::where('id', $id)
                ->where('template_id', $this->templateRecord->id)
                ->update(['order_priority' => $position + 1]);
        }

        $this->loadSections();

        Notification::make()
            ->title('Order saved successfully!')
            ->success()
            ->send();
    }

    /**
     * Toggle section active state directly from sort page.
     */
    public function toggleActive(int $id): void
    {
        $section = TemplateSection::find($id);

        if ($section && $section->template_id === $this->templateRecord->id) {
            $section->update(['is_active' => ! $section->is_active]);
            $this->loadSections();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back_to_template')
                ->label('Back to Template')
                ->icon('heroicon-o-arrow-left')
                ->url(fn () => TemplateSectionResource::getUrl('index'))
                ->color('gray'),
        ];
    }

    public function getTitle(): string
    {
        return "Sort Sections — {$this->templateRecord->name}";
    }
}
