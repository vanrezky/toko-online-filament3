<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;



    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function afterCreate()
    {
        $product = $this->record;

        $productVariants = session()->pull('product_variants', []);

        foreach ($productVariants as $variantData) {
            $variant = $product->productVariants()->create([
                'variant_name' => $variantData['variant_name'],
                'sku' => $variantData['sku'],
                'price' => $variantData['price'],
                'stock' => $variantData['stock'],
                'image' => $variantData['image'],
            ]);

            // Simpan attribute untuk variant
            $variant->attributes()->create([
                'attribute_name' => $this->record->variant,
                'attribute_value' => $variantData['variant'],
            ]);

            // Simpan attribute untuk sub-variant jika ada
            if (!empty($variantData['sub_variant'])) {
                $variant->attributes()->create([
                    'attribute_name' => $this->record->sub_variant,
                    'attribute_value' => $variantData['sub_variant'],
                ]);
            }
        }
    }
}
