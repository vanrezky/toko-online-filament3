<?php

namespace App\Livewire\Home;

use Livewire\Component;

class Products extends Component
{
    public $products;
    public $blockid = '';


    public function render()
    {
        return view('livewire.home.products');
    }

    public function placeholder()
    {
        return view('livewire.home.products-placeholder');
    }
}
