<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\Slider;
use Livewire\Component;

class HomePage extends Component
{
    public function render()
    {
        return view('livewire.home-page', [
            'sliders' => Slider::active()->get(),
            'flashSales' => Product::inRandomOrder()->take(7)->get(),
            'bestSelling' => Product::inRandomOrder()->take(5)->get(),
            'products' => Product::inRandomOrder()->take(10)->get(),
            'categories' => Category::homepage()->get()
        ]);
    }
}
