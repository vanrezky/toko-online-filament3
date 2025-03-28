<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\SliderResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Slider;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $sliders = Slider::active()->with('media')->get();

        return Inertia::render('Home/Index', [
            'sliders' => SliderResource::collection($sliders),
            'flashSales' => Product::inRandomOrder()->take(7)->get(),
            'bestSelling' => Product::inRandomOrder()->take(5)->get(),
            'products' => Product::inRandomOrder()->take(10)->get(),
        ]);
    }
}
