<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\CartStatus;
use App\Http\Controllers\Controller;
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
            $cart = Cart::with([
                'items.product.media',
                'items.productVariant.variantAttributes.productAttribute',
                'items.productVariant.variantAttributes.productAttributeOption',
            ])
                ->active()
                ->where('customer_id', Auth::guard('customer')->id())
                ->first();

            if ($cart) {
                $cart = $this->formatCart($cart);
            }
        }

        return Inertia::render("Cart/Index", [
            'cart' => $cart
        ]);
    }

    protected function formatCart(Cart $cart): array
    {
        return [
            'id' => $cart->id,
            'uuid' => $cart->uuid,
            'subtotal' => $cart->subtotal,
            'items' => $cart->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'original_price' => $item->original_price,
                    'discount' => $item->discount,
                    'product' => $item->product ? [
                        'id' => $item->product->id,
                        'uuid' => $item->product->uuid,
                        'name' => $item->product->name,
                        'slug' => $item->product->slug,
                        'thumbnail' => $item->product->getFirstMediaUrl('thumb') ?: $item->product->getFirstMediaUrl(),
                    ] : null,
                    'product_variant' => $item->productVariant ? [
                        'id' => $item->productVariant->id,
                        'variant_name' => $item->productVariant->variant_name,
                    ] : null,
                ];
            }),
        ];
    }

    public function store(Request $request)
    {
        if (!Auth::guard('customer')->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Please login to add items to cart.',
                    'redirect' => route('frontend.login'),
                ], 401);
            }
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
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Please choose the variant product.'], 422);
            }
            return redirect()->back()->with('error', 'Please choose the variant product.');
        }

        $variant = null;
        if ($request->product_variant_id) {
            $variant = ProductVariant::where('uuid', $request->product_variant_id)->first();
        }

        $priceInfo = $product->calculatePrice($request->quantity, $variant);
        $price = $priceInfo['price'];
        $discount = $priceInfo['discount'];


        $cart = Cart::firstOrCreate(
            ['customer_id' => Auth::guard('customer')->id(), 'status' => CartStatus::Active]
        );

        $item = $cart->items()
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variant?->id)
            ->first();

        if ($item) {
            $newQuantity = $item->quantity + $request->quantity;
            $priceInfo = $product->calculatePrice($newQuantity, $variant);
            $item->update([
                'quantity' => $newQuantity,
                'price' => $priceInfo['price'],
                'discount' => $priceInfo['discount'],
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'quantity' => $request->quantity,
                'price' => $price,
                'discount' => $discount,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item added to cart successfully.',
                'cart_count' => $cart->items()->sum('quantity'),
            ]);
        }

        return redirect()->back()->with('success', 'Item added to bag successfully.');
    }

    public function update(Request $request, CartItem $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $priceInfo = $item->product->calculatePrice($request->quantity, $item->productVariant);

        $item->update([
            'quantity' => $request->quantity,
            'price' => $priceInfo['price'],
            'discount' => $priceInfo['discount'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Cart updated.']);
        }

        return redirect()->back()->with('success', 'Cart updated successfully.');
    }

    public function destroy(Request $request, CartItem $item)
    {
        $item->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Item removed.']);
        }

        return redirect()->back()->with('success', 'Item removed from bag.');
    }
}
