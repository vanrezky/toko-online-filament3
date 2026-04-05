<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\CartStatus;
use App\Enums\CourierCode;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Jobs\SendPaymentRequestNotification;
use App\Models\Cart;
use App\Models\CustomerAddress;
use App\Models\Transaction;
use App\Models\Warehouse;
use App\Services\ApicoidOngkirService;
use App\Services\CacheService;
use App\Services\PaymentGatewayService;
use App\Services\VoucherCookieService;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    protected VoucherCookieService $cookieService;

    protected VoucherService $voucherService;

    public function __construct(VoucherCookieService $cookieService, VoucherService $voucherService)
    {
        $this->cookieService = $cookieService;
        $this->voucherService = $voucherService;
    }

    public function __invoke(Request $request, PaymentGatewayService $paymentGatewayService)
    {
        $customer = Auth::guard('customer')->user();

        $cart = Cart::with(['items.product.media', 'items.product.warehouse', 'items.productVariant.variantAttributes.productAttribute', 'items.productVariant.variantAttributes.productAttributeOption'])
            ->active()
            ->where('customer_id', $customer->id)
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('frontend.cart')->with('error', 'Your cart is empty.');
        }

        $addresses = CustomerAddress::with(['province', 'district', 'subDistrict', 'village'])
            ->where('customer_id', $customer->id)
            ->orderBy('is_featured', 'desc')
            ->get();

        $pendingVouchers = $this->cookieService->get();
        $validatedVouchers = $this->voucherService->validateFromCookie($pendingVouchers, $cart, $customer);

        return Inertia::render('Checkout/Index', [
            'cart' => CartResource::make($cart),
            'addresses' => \App\Http\Resources\AddressResource::collection($addresses),
            'pendingVouchers' => $pendingVouchers,
            'validatedVouchers' => $validatedVouchers,
            'activeGateway' => $paymentGatewayService->getActiveGatewayAlias(),
        ]);
    }

    public function getShippingCosts(Request $request, ApicoidOngkirService $ongkirService, \App\Settings\CourierSettings $courierSettings)
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

        if (! $address->village) {
            return response()->json(['error' => 'Selected address does not have village information.'], 422);
        }

        $cartHash = md5($cart->items->sortBy('id')->map(function ($item) {
            return $item->product_id.'-'.($item->product_variant_id ?? '0').'-'.$item->quantity.'-'.$item->price;
        })->implode('|'));

        $cacheKey = "shipping_costs_{$customer->id}_{$address->id}_{$cartHash}";

        $shippingResults = CacheService::remember($cacheKey, 1800, function () use ($cart, $address, $ongkirService, $courierSettings) {
            $warehouseGroups = $cart->items->groupBy(function ($item) {
                return $item->product->warehouse_id ?: 0;
            });

            $activeCouriers = \App\Models\Courier::active()->get();
            $isPickupActive = $activeCouriers->contains('code', CourierCode::PICKUP->value);
            $isKurirTokoActive = $activeCouriers->contains('code', CourierCode::KURIR_TOKO->value);

            $pickupOption = $isPickupActive ? [
                'courier_code' => CourierCode::PICKUP->value,
                'courier_name' => 'Pick Up',
                'price' => 0,
                'estimation' => 'Hari yang sama',
            ] : null;

            $kurirTokoOption = $isKurirTokoActive ? [
                'courier_code' => CourierCode::KURIR_TOKO->value,
                'courier_name' => 'Kurir Toko',
                'price' => $courierSettings->kurir_toko_price,
                'estimation' => '1-2 Hari',
            ] : null;

            $results = [];

            foreach ($warehouseGroups as $warehouseId => $items) {
                $warehouse = Warehouse::with('village')->find($warehouseId);
                if (! $warehouse || ! $warehouse->village) {
                    continue;
                }

                $totalWeight = $items->sum(fn ($item) => ($item->productVariant?->weight ?: $item->product->weight) * $item->quantity);

                $costs = $ongkirService->getShippingCost(
                    $warehouse->village->apicoid_code,
                    $address->village->apicoid_code,
                    $totalWeight
                );

                $options = [];
                if (isset($costs['status']) && $costs['status'] === 'success') {
                    $options = $costs['result'];
                }

                if ($pickupOption) {
                    $options[] = $pickupOption;
                }

                if ($kurirTokoOption) {
                    $options[] = $kurirTokoOption;
                }

                if (! empty($options)) {
                    $results[] = [
                        'warehouse_id' => $warehouseId,
                        'warehouse_name' => $warehouse->name,
                        'weight' => $totalWeight,
                        'options' => $options,
                    ];
                }
            }

            return $results;
        });

        return response()->json($shippingResults);
    }

    public function store(Request $request, PaymentGatewayService $paymentGatewayService)
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
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Your cart is empty.'], 400);
            }

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

        $firstItem = $cart->items->first();
        $fromDistrictId = $firstItem->product->warehouse?->district_id ?: 1;

        $pendingVouchers = $this->cookieService->get();
        $validatedVouchers = $this->voucherService->validateFromCookie($pendingVouchers, $cart, $customer);

        return DB::transaction(function () use ($request, $customer, $cart, $totalShippingCost, $totalWeight, $address, $fromDistrictId, $shippingDetails, $validatedVouchers, $paymentGatewayService) {
            $transaction = Transaction::create([
                'customer_id' => $customer->id,
                'customer_address_id' => $address->id,
                'weight' => $totalWeight,
                'shipping_cost' => $totalShippingCost,
                'courier_id' => 1,
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

            foreach (['shipping', 'product'] as $type) {
                $voucherData = $validatedVouchers[$type] ?? null;
                if ($voucherData && $voucherData['valid'] ?? false) {
                    $voucher = $this->voucherService->getVoucherByCode($voucherData['code']);
                    if ($voucher) {
                        $transaction->vouchers()->create([
                            'voucher_code' => $voucherData['code'],
                            'voucher_name' => $voucherData['name'],
                            'voucher_type' => $type,
                            'discount_type' => $voucherData['discount_type'],
                            'discount_value' => $voucherData['discount_value'],
                            'discount_amount' => $voucherData['discount_amount'],
                        ]);
                        $this->voucherService->trackUsage($voucher);
                    }
                }
            }

            $this->cookieService->clear();
            $cart->update(['status' => CartStatus::Checked_out]);

            $paymentResponse = $paymentGatewayService->createPayment($transaction);

            // Send payment request notification
            if ($paymentResponse->paymentUrl) {
                SendPaymentRequestNotification::dispatch($transaction, $paymentResponse->paymentUrl)
                    ->onQueue('default');
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'transaction_uuid' => $transaction->uuid,
                    'payment' => [
                        'provider' => $paymentGatewayService->getActiveGatewayAlias(),
                        'payment_url' => $paymentResponse->paymentUrl,
                        'snap_token' => $paymentResponse->metadata['snap_token'] ?? null,
                        'client_key' => $paymentResponse->metadata['client_key'] ?? null,
                    ],
                ]);
            }

            return redirect()->route('frontend.orders.show', $transaction->uuid)->with('success', 'Order placed successfully!');
        });
    }
}
