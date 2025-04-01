<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function __invoke()
    {
        return Inertia::render("Cart/Index");
    }
}
