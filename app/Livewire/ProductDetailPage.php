<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;

class ProductDetailPage extends Component
{
    public $product;

    public function mount(Product $product)
    {
        $this->product = $product;
    }
    #[Title('Product Detail Page')]
    public function render()
    {
        return view('livewire.product-detail-page');
    }
}
