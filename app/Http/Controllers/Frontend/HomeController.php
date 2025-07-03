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
        $flashSales = Flashsale::active()->with('products.product.media')->first();
        $products = Product::with('media')->inRandomOrder()->take(10)->get();
        $featuredCategories = Category::withCount(['products'])->active()->featured()->get();


        return Inertia::render('Home/Index', [
            'featuredCategories' => CategoryResource::collection($featuredCategories),
            'sliders' => SliderResource::collection($sliders),
            'flashsales' => $flashSales ?  FlashsaleResource::make($flashSales) : [],
            'bestSelling' => Product::inRandomOrder()->take(5)->get(),
            'products' => ProductSimpleResource::collection($products),
        ]);
    }
}
