<?php

namespace App\Livewire\Partials;

use App\Models\Category;
use Livewire\Component;

class Header extends Component
{
    public function render()
    {
        $categories =  Category::active()->featured()->take(8)->get();
        return view('livewire.partials.header', compact('categories'));
    }
}
