<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Str;

class ProductItem extends Component
{
    /**
     * Create a new component instance.
     */

    public $product;
    public $saveprice;
    public $isSalePrice;

    public function __construct($product, $saveprice = false)
    {
        $this->product = $product;
        $this->saveprice = $saveprice;
        $this->isSalePrice = $product->sale_price > 0;
        $this->product->name_short = Str::limit($this->product->name, 24, '..');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-item');
    }
}
