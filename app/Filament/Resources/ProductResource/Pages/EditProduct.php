<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductVariant;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filement\Forms;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\ViewAction::make(),
            Actions\Action::make('back')->label('Back')->color('warning')->url($this->getResource()::getUrl('index')),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // ambil data product variant attributes
        $productVariants = ProductVariant::where('product_id', $data['id'])->with('attributes')->get();
        $attributes = $productVariants->pluck('attributes')
            ->flatten(1)
            ->groupBy('attribute_name')
            ->map(fn($items) => $items->pluck('attribute_value')->unique()->values()->toArray());

        // Pastikan kunci ada sebelum mengaksesnya untuk mencegah error
        $data['variants'] = $attributes->has($data['variant']) ? $attributes[$data['variant']] : [];
        $data['sub_variants'] = $attributes->has($data['sub_variant']) ? $attributes[$data['sub_variant']] : [];

        $data['product_variants'] = $productVariants->map(function ($var) use ($data) {
            $variantName = explode('-', $var['variant_name']);
            $var['variant'] = trim($variantName[0] ?? '');
            $var['sub_variant'] = trim($variantName[1] ?? '');
            return $var;
        })->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Simpan product_variants ke session
        session()->put('product_variants', $data['product_variants'] ?? []);
        // Hindari penyimpanan otomatis oleh Filament
        unset($data['product_variants']);
        return $data;
    }

    protected function afterSave()
    {
        $product = $this->record;
        $productVariants = session()->pull('product_variants', []);

        // delete unuserd variants
        $product->productVariants()->whereNot('id', array_column($productVariants, 'id'))->delete();

        foreach ($productVariants as $variantData) {
            $variant = $product->productVariants()->updateOrCreate(
                ['id' => $variantData['id'] ?? null],  // Update jika ID ada, jika tidak buat baru
                [
                    'variant_name' => $variantData['variant_name'],
                    'sku' => $variantData['sku'],
                    'price' => $variantData['price'],
                    'stock' => $variantData['stock'],
                    'image' => $variantData['image'],
                ]
            );

            // Simpan attribute untuk variant
            $variant->attributes()->updateOrCreate(
                [
                    'attribute_name' => $this->record->variant,
                ],
                [
                    'attribute_value' => $variantData['variant'],
                ]
            );

            // Simpan attribute untuk sub-variant jika ada
            if (!empty($variantData['sub_variant'])) {
                $variant->attributes()->updateOrCreate(
                    [
                        'attribute_name' => $this->record->sub_variant,
                    ],
                    [
                        'attribute_value' => $variantData['sub_variant'],
                    ]
                );
            }
        }
    }
}
