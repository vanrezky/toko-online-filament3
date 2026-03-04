<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\FlashsaleResource;
use App\Http\Resources\ProductSimpleResource;
use App\Http\Resources\SliderResource;
use App\Models\Category;
use App\Models\Flashsale;
use App\Models\Product;
use App\Models\Slider;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $sliders = Slider::active()->with('media')->get();

        $flashSales = Flashsale::active()->with([
            'products' => fn($Q) => $Q->limit(4),
            'products.product.media',
            'products.product.category'
        ])->first();

        $newArrivals = Product::active()->with('media', 'category')->latest()->limit(4)->get();

        $featuredCategories = Category::active()
            ->featured()
            ->with(['products' => fn($q) => $q->active()->with('media')->limit(6)])
            ->get();

        return Inertia::render('Home/Index', [
            'sliders' => SliderResource::collection($sliders),
            'flashsales' => $flashSales ? FlashsaleResource::make($flashSales) : null,
            'newArrivals' => ProductSimpleResource::collection($newArrivals),
            'featuredCategories' => CategoryResource::collection($featuredCategories),
        ]);
    }
}
