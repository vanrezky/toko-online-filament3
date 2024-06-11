<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

class CategoriesPage extends Component
{

    #[Title('Categories')]
    public function render()
    {
        return view('livewire.categories-page');
    }
}
