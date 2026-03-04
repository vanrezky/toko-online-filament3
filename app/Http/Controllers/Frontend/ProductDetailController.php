<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductDetailController extends Controller
{
    public function __invoke(Request $request,  Product $product)

    {
        $product->loadMissing([
            'category',
            'productVariants.variantAttributes' => fn($Q) => $Q->with([
                'productAttribute',
                'productAttributeOption'
            ]),
            'warehouse',
            'faqs',
            'meta'
        ]);
        $product = ProductResource::make($product);

        return Inertia::render('ProductDetail/Index', [
            'product' => $product
        ]);
    }
}
