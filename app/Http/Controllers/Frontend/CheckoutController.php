<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\CartStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CustomerAddress;
use App\Models\Transaction;
use App\Models\TransactionShippingDetail;
use App\Models\Warehouse;
use App\Services\ApicoidOngkirService;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function __invoke(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $cart = Cart::with(['items.product.media', 'items.product.warehouse', 'items.productVariant.variantAttributes.productAttribute', 'items.productVariant.variantAttributes.productAttributeOption'])
            ->active()
            ->where('customer_id', $customer->id)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('frontend.cart')->with('error', 'Your cart is empty.');
        }

        $addresses = CustomerAddress::with(['province', 'district', 'subDistrict', 'village'])
            ->where('customer_id', $customer->id)
            ->orderBy('is_featured', 'desc')
            ->get();

        return Inertia::render('Checkout/Index', [
            'cart' => CartResource::make($cart),
            'addresses' => $addresses,
        ]);
    }

    public function getShippingCosts(Request $request, ApicoidOngkirService $ongkirService)
    {
        $request->validate([
            'address_id' => 'required|exists:customer_addresses,id',
        ]);

        $customer = Auth::guard('customer')->user();
        $cart = Cart::with(['items.product.warehouse', 'items.productVariant'])
            ->active()
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        $address = CustomerAddress::with('village')->findOrFail($request->address_id);

        if (!$address->village) {
            return response()->json(['error' => 'Selected address does not have village information.'], 422);
        }

        // Create a unique hash for the current cart state to use in cache key
        // This ensures cache is busted if products, quantities, or prices change
        $cartHash = md5($cart->items->sortBy('id')->map(function ($item) {
            return $item->product_id . '-' . ($item->product_variant_id ?? '0') . '-' . $item->quantity . '-' . $item->price;
        })->implode('|'));

        $cacheKey = "shipping_costs_{$customer->id}_{$address->id}_{$cartHash}";

        $shippingResults = CacheService::remember($cacheKey, 1800, function () use ($cart, $address, $ongkirService) {
            $warehouseGroups = $cart->items->groupBy(function ($item) {
                return $item->product->warehouse_id ?: 0;
            });

            $results = [];

            foreach ($warehouseGroups as $warehouseId => $items) {
                $warehouse = Warehouse::with('village')->find($warehouseId);
                if (!$warehouse || !$warehouse->village) {
                    continue;
                }

                $totalWeight = $items->sum(fn($item) => ($item->productVariant?->weight ?: $item->product->weight) * $item->quantity);

                $costs = $ongkirService->getShippingCost(
                    $warehouse->village->apicoid_code,
                    $address->village->apicoid_code,
                    $totalWeight
                );

                if (isset($costs['status']) && $costs['status'] === 'success') {
                    $results[] = [
                        'warehouse_id' => $warehouseId,
                        'warehouse_name' => $warehouse->name,
                        'weight' => $totalWeight,
                        'options' => $costs['result']
                    ];
                }
            }

            return $results;
        });

        return response()->json($shippingResults);
    }

    public function store(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:customer_addresses,id',
            'shipping_methods' => 'required|array',
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

        $totalShippingCost = 0;
        $totalWeight = 0;
        $shippingDetails = [];

        foreach ($request->shipping_methods as $warehouseId => $method) {
            $totalShippingCost += $method['price'];
            $totalWeight += $method['weight'];
            $shippingDetails[] = [
                'warehouse_id' => $warehouseId,
                'courier_code' => $method['courier_code'],
                'courier_name' => $method['courier_name'],
                'price' => $method['price'],
                'weight' => $method['weight'],
                'estimation' => $method['estimation'] ?? null,
            ];
        }

        // Find a from_district_id from first item's warehouse if available
        $firstItem = $cart->items->first();
        $fromDistrictId = $firstItem->product->warehouse?->district_id ?: 1;

        return DB::transaction(function () use ($request, $customer, $cart, $totalShippingCost, $totalWeight, $address, $fromDistrictId, $shippingDetails) {
            $transaction = Transaction::create([
                'customer_id' => $customer->id,
                'customer_address_id' => $address->id,
                'weight' => $totalWeight,
                'shipping_cost' => $totalShippingCost,
                'courier_id' => 1, // Default or mock
                'from_district_id' => $fromDistrictId,
                'to_district_id' => $address->district_id,
                'payment_method' => $request->payment_method,
                'status' => 'unpaid',
                'notes' => $request->notes,
                'timelimit' => \Illuminate\Support\Carbon::now()->addDay(),
            ]);

            foreach ($shippingDetails as $detail) {
                $transaction->shippingDetails()->create($detail);
            }

            foreach ($cart->items as $item) {
                $transaction->products()->create([
                    'customer_id' => $customer->id,
                    'is_digital' => false,
                    'product_id' => $item->product_id,
                    'warehouse_id' => $item->product->warehouse_id ?: 1,
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
