<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\CartStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CustomerAddress;
use App\Models\Transaction;
use App\Models\TransactionProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function __invoke(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $cart = Cart::with(['items.product.media', 'items.productVariant.variantAttributes.productAttribute', 'items.productVariant.variantAttributes.productAttributeOption'])
            ->active()
            ->where('customer_id', $customer->id)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('frontend.cart')->with('error', 'Your cart is empty.');
        }

        $addresses = CustomerAddress::with(['province', 'district', 'subDistrict'])
            ->where('customer_id', $customer->id)
            ->orderBy('is_featured', 'desc')
            ->get();

        $shippingMethods = [
            ['id' => 'standard', 'name' => 'Standard Shipping (3-5 days)', 'price' => 15000],
            ['id' => 'express', 'name' => 'Express Shipping (1-2 days)', 'price' => 35000],
        ];

        return Inertia::render('Checkout/Index', [
            'cart' => CartResource::make($cart),
            'addresses' => $addresses,
            'shippingMethods' => $shippingMethods,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:customer_addresses,id',
            'shipping_method' => 'required',
            'payment_method' => 'required',
            'notes' => 'nullable|string',
        ]);

        $customer = Auth::guard('customer')->user();
        $cart = Cart::with(['items.product.warehouse', 'items.productVariant.variantAttributes.productAttributeOption'])
            ->active()
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return redirect()->route('frontend.cart')->with('error', 'Your cart is empty.');
        }

        $address = CustomerAddress::findOrFail($request->address_id);

        $shippingCost = $request->shipping_method === 'express' ? 35000 : 15000;
        $totalWeight = $cart->items->sum(fn($item) => ($item->productVariant?->weight ?: $item->product->weight) * $item->quantity);

        // Find a from_district_id from first item's warehouse if available
        $firstItem = $cart->items->first();
        $fromDistrictId = $firstItem->product->warehouse?->subDistrict?->district_id ?: 1; // Default to 1 if not available

        return DB::transaction(function () use ($request, $customer, $cart, $shippingCost, $totalWeight, $address, $fromDistrictId) {
            $transaction = Transaction::create([
                'customer_id' => $customer->id,
                'customer_address_id' => $address->id,
                'weight' => $totalWeight,
                'shipping_cost' => $shippingCost,
                'courier_id' => 1, // Mock courier ID
                'from_district_id' => $fromDistrictId,
                'to_district_id' => $address->district_id,
                'payment_method' => $request->payment_method,
                'status' => 'unpaid',
                'notes' => $request->notes,
                'timelimit' => \Illuminate\Support\Carbon::now()->addDay(),
            ]);

            foreach ($cart->items as $item) {
                $transaction->products()->create([
                    'customer_id' => $customer->id,
                    'is_digital' => false,
                    'product_id' => $item->product_id,
                    'warehouse_id' => $item->product->warehouse_id ?: 1, // Mock warehouse ID if not set
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'discount' => $item->discount,
                    'description' => $item->productVariant?->variant_name,
                ]);
            }

            // Deactivate cart
            $cart->update(['status' => CartStatus::Checked_out]);

            return redirect()->route('frontend.orders.show', $transaction->uuid)->with('success', 'Order placed successfully!');
        });
    }
}
