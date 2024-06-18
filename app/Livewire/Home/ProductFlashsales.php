<?php

namespace App\Livewire\Home;

use Livewire\Component;

class ProductFlashsales extends Component
{

    public $products;
    public $sliderkey;
    public function render()
    {
        return view('livewire.home.product-flashsales');
    }

    public function placeholder()
    {
        return view('livewire.home.products-placeholder');
    }
}
