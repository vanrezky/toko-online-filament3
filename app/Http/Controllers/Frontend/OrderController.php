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
        $orders = Transaction::with(['products.product.media', 'address.province', 'address.district', 'address.subDistrict'])
            ->where('customer_id', Auth::guard('customer')->id())
            ->orderBy('created_at', 'desc')
            ->get();

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
            'address.province',
            'address.district',
            'address.subDistrict',
            'products.product.media',
            'products.product.category',
            'products.productVariant.variantAttributes.productAttributeOption'
        ]);

        // Mock payment methods for now, or fetch from DB if integrated
        $paymentMethods = [
            ['id' => 'bank_transfer', 'name' => 'Bank Transfer'],
            ['id' => 'credit_card', 'name' => 'Credit Card'],
            ['id' => 'paypal', 'name' => 'PayPal'],
        ];

        return Inertia::render('Orders/Show', [
            'order' => OrderResource::make($transaction),
            'paymentMethods' => $paymentMethods,
        ]);
    }
    public function changePaymentMethod(Request $request, Transaction $transaction)
    {
        if ($transaction->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        if ($transaction->status !== 'unpaid') {
            return redirect()->back()->with('error', 'Payment method cannot be changed for this order status.');
        }

        $request->validate([
            'payment_method' => 'required'
        ]);

        $transaction->update([
            'payment_method' => $request->payment_method
        ]);

        return redirect()->back()->with('success', 'Payment method updated successfully.');
    }
}
