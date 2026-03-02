<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __invoke(Request $request)
    {
        $cart = null;
        if (Auth::guard('customer')->check()) {
            $cart = Cart::with(['items.product.media', 'items.productVariant'])
                ->where('customer_id', Auth::guard('customer')->id())
                ->where('status', 'active')
                ->first();
        }

        return Inertia::render("Cart/Index", [
            'cart' => $cart
        ]);
    }
}
