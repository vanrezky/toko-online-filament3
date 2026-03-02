<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\FlashsaleResource;
use App\Models\Flashsale;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FlashsaleController extends Controller
{
    public function __invoke(Request $request)
    {
        $flashSale = Flashsale::active()->with([
            'products.product.media',
            'products.product.category'
        ])->first();

        return Inertia::render('Flashsale/Index', [
            'flashSale' => $flashSale ? FlashsaleResource::make($flashSale) : null,
        ]);
    }
}
