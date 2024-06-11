<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;

class ProductsPage extends Component
{

    public $products;

    public function mount()
    {
        $this->products = Product::take(16)->get();
    }

    #[Title('Products')]
    public function render()
    {
        return view('livewire.products-page');
    }
}
