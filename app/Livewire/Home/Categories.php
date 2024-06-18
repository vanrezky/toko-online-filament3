<?php

namespace App\Livewire\Home;

use Livewire\Component;

class Categories extends Component
{
    public $categories;
    public $sliderkey;

    public function render()
    {
        return view('livewire.home.categories');
    }


    public function placeholder()
    {

        return view('livewire.home.products-placeholder');
    }
}
