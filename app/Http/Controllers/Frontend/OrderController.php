<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\PaymentGateway;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Transaction::with([
            'products' => function($query) {
                $query->select('id', 'transaction_id', 'product_id', 'quantity', 'price', 'discount', 'description');
            },
            'products.product' => function($query) {
                $query->select('id', 'uuid', 'name', 'slug');
            },
            'products.product.media'
        ])
        ->where('customer_id', Auth::guard('customer')->id())
        ->orderBy('created_at', 'desc')
        ->get(['id', 'uuid', 'customer_id', 'status', 'shipping_cost', 'cod_fee', 'created_at', 'timelimit']);

        return Inertia::render('Orders/Index', [
            'orders' => OrderResource::collection($orders)
        ]);
    }

    public function show(Transaction $transaction)
    {
        // Ensure user owns the transaction
        if ($transaction->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        $transaction->load([
            'address' => function($query) {
                $query->select('id', 'name', 'phone', 'address', 'province_id', 'district_id', 'sub_district_id', 'postal_code');
            },
            'address.province:id,name',
            'address.district:id,name',
            'address.subDistrict:id,name',
            'products' => function($query) {
                $query->select('id', 'transaction_id', 'product_id', 'quantity', 'price', 'discount', 'description');
            },
            'products.product' => function($query) {
                $query->select('id', 'uuid', 'name', 'slug', 'description');
            },
            'products.product.media',
            'products.product.category:id,name'
        ]);

        return Inertia::render('Orders/Show', [
            'order' => OrderResource::make($transaction),
        ]);
    }

    public function pay(Transaction $transaction, \App\Services\PaymentGatewayService $paymentGatewayService)
    {
        if ($transaction->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        if ($transaction->status !== 'unpaid') {
            return response()->json(['error' => 'Order is already paid or cancelled.'], 400);
        }

        try {
            $paymentResponse = $paymentGatewayService->createPayment($transaction);

            if (!$paymentResponse->success) {
                return response()->json(['error' => $paymentResponse->errorMessage], 400);
            }

            return response()->json([
                'success' => true,
                'payment' => [
                    'provider' => $paymentGatewayService->getActiveGatewayAlias(),
                    'payment_url' => $paymentResponse->paymentUrl,
                    'snap_token' => $paymentResponse->metadata['snap_token'] ?? null,
                    'client_key' => $paymentResponse->metadata['client_key'] ?? null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to initiate payment: ' . $e->getMessage()], 500);
        }
    }

}
