<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\CartStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CartController extends Controller
{
    public function __invoke(Request $request)
    {
        $cart = null;
        if (Auth::guard('customer')->check()) {
            $cart = Cart::with(['items.product.media', 'items.productVariant.variantAttributes.productAttribute', 'items.productVariant.variantAttributes.productAttributeOption'])
                ->active()
                ->where('customer_id', Auth::guard('customer')->id())
                ->first();

            if ($cart) $cart = CartResource::make($cart);
        }

        return Inertia::render("Cart/Index", [
            'cart' => $cart
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::guard('customer')->check()) {
            // Save current URL (product page) as intended destination after login
            // Since this is a POST request, we usually want to redirect back to the product detail page
            session()->put('url.intended', url()->previous());
            return redirect()->route('frontend.login')->with('error', 'Please login to add items to cart.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,uuid',
            'product_variant_id' => 'nullable|exists:product_variants,uuid',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::withCount('productVariants')->where('uuid', $request->product_id)->first();
        $price = $product->price;
        $discount = 0;

        if (!$request->product_variant_id && $product->product_variants_count > 0) {
            return redirect()->back()->with('errors', 'Please choose the variant product.');
        }

        $variant = null;
        if ($request->product_variant_id) {
            $variant = ProductVariant::where('uuid', $request->product_variant_id)->first();
            $price = $variant->price;
        } else if ($product->sale_price) {
            $price = $product->sale_price;
            $discount = $product->price - $product->sale_price;
        }


        $cart = Cart::firstOrCreate(
            ['customer_id' => Auth::guard('customer')->id(), 'status' => CartStatus::Active]
        );

        $item = $cart->items()
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variant?->id)
            ->first();

        if ($item) {
            $item->increment('quantity', $request->quantity);
            // Optionally update price if it changed, but usually we keep the price at the time of adding
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'quantity' => $request->quantity,
                'price' => $price,
                'discount' => $discount,
            ]);
        }

        return redirect()->back()->with('success', 'Item added to bag successfully.');
    }

    public function update(Request $request, CartItem $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item->update(['quantity' => $request->quantity]);

        return redirect()->back()->with('success', 'Cart updated successfully.');
    }

    public function destroy(CartItem $item)
    {
        $item->delete();

        return redirect()->back()->with('success', 'Item removed from bag.');
    }
}
