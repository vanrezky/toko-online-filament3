<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Customer;
use App\Services\VoucherCookieService;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Inertia\Inertia;

class VoucherController extends Controller
{
    protected VoucherService $voucherService;
    protected VoucherCookieService $cookieService;

    public function __construct(VoucherService $voucherService, VoucherCookieService $cookieService)
    {
        $this->voucherService = $voucherService;
        $this->cookieService = $cookieService;
    }

    public function index(Request $request)
    {
        $type = $request->get('type');
        $vouchers = $this->voucherService->getPublicVouchers($type);

        $pendingVouchers = $this->cookieService->get();

        return Inertia::render('Voucher/Index', [
            'vouchers' => $vouchers->map(fn($v) => $this->formatVoucher($v)),
            'pendingShippingVoucher' => $pendingVouchers['shipping'],
            'pendingProductVoucher' => $pendingVouchers['product'],
        ]);
    }

    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'redirect' => 'nullable|string',
        ]);

        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            $redirect = $request->get('redirect', route('frontend.checkout'));
            return redirect()->route('frontend.login', ['redirect' => $redirect, 'voucher' => $request->code]);
        }

        $cart = $this->getActiveCart($customer);

        if (!$cart) {
            return redirect()->back()->with('error', 'Keranjang tidak ditemukan');
        }

        $result = $this->voucherService->validateVoucher($request->code, $cart, $customer);

        if (!$result->success) {
            return redirect()->back()->with('error', $result->errorMessage);
        }

        $voucher = $result->voucher;
        $type = $voucher->is_shipping ? 'shipping' : 'product';

        $this->cookieService->saveVoucher($type, [
            'code' => $voucher->code,
            'name' => $voucher->name,
            'type' => $type,
        ]);

        $redirect = $request->get('redirect', route('frontend.checkout'));

        return redirect($redirect)->with('success', 'Voucher "' . $voucher->code . '" berhasil dipilih!');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'type' => 'required|in:shipping,product',
        ]);

        $this->cookieService->removeVoucher($request->type);

        return redirect()->back()->with('success', 'Voucher berhasil dihapus');
    }

    protected function getActiveCart(?Customer $customer): ?Cart
    {
        if (!$customer) {
            return null;
        }

        return Cart::with(['items.product.media'])
            ->active()
            ->where('customer_id', $customer->id)
            ->first();
    }

    protected function formatVoucher($voucher): array
    {
        return [
            'id' => $voucher->id,
            'uuid' => $voucher->uuid,
            'name' => $voucher->name,
            'description' => $voucher->description,
            'code' => $voucher->code,
            'type' => $voucher->voucher_type->value,
            'type_label' => $voucher->voucher_type->getLabel(),
            'discount_type' => $voucher->discount_type->value,
            'discount_type_label' => $voucher->discount_type->getLabel(),
            'discount' => $voucher->discount,
            'formatted_discount' => $voucher->formatted_discount,
            'discount_min' => $voucher->discount_min,
            'discount_max' => $voucher->discount_max,
            'min_purchase_formatted' => $voucher->min_purchase_formatted,
            'is_shipping' => $voucher->is_shipping,
            'is_product' => $voucher->is_product,
            'is_expiring_soon' => $voucher->is_expiring_soon,
            'remaining_days' => $voucher->remaining_days,
            'remaining_hours' => $voucher->remaining_hours,
            'usage_count' => $voucher->usage_count,
            'max_user_used' => $voucher->max_user_used,
            'usage_percentage' => $voucher->usage_percentage,
            'is_fully_used' => $voucher->is_fully_used,
            'start_at' => $voucher->start_at,
            'end_at' => $voucher->end_at,
            'validity_period' => $voucher->validity_period,
            'image' => $voucher->getFirstMediaUrl() ?: null,
            'image_thumb' => $voucher->getFirstMediaUrl('thumb') ?: null,
        ];
    }
}
