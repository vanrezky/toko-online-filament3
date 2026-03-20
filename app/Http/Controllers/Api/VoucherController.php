<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Voucher;
use App\Services\VoucherCookieService;
use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    protected VoucherService $voucherService;
    protected VoucherCookieService $cookieService;

    public function __construct(VoucherService $voucherService, VoucherCookieService $cookieService)
    {
        $this->voucherService = $voucherService;
        $this->cookieService = $cookieService;
    }

    public function index(Request $request): JsonResponse
    {
        $type = $request->get('type');
        $vouchers = $this->voucherService->getPublicVouchers($type);

        return response()->json([
            'success' => true,
            'data' => $vouchers->map(fn($v) => $this->formatVoucher($v)),
        ]);
    }

    public function validateCode(string $code, Request $request): JsonResponse
    {
        $customer = $this->getAuthenticatedCustomer();
        $cart = $this->getActiveCart($customer);

        $result = $this->voucherService->validateVoucher($code, $cart, $customer);

        if (!$result->success) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => $result->errorCode,
                    'message' => $result->errorMessage,
                ],
            ], 400);
        }

        $discountAmount = $this->voucherService->calculateDiscountAmount($result->voucher, $cart);

        return response()->json([
            'success' => true,
            'data' => [
                'voucher' => $this->formatVoucher($result->voucher),
                'discount_amount' => $discountAmount,
                'formatted_discount' => 'Rp ' . number_format($discountAmount, 0, ',', '.'),
            ],
        ]);
    }

    public function validateCookie(Request $request): JsonResponse
    {
        $customer = $this->getAuthenticatedCustomer();
        $cart = $this->getActiveCart($customer);
        $vouchers = $this->cookieService->get();

        $results = $this->voucherService->validateFromCookie($vouchers, $cart, $customer);

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    public function apply(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $customer = $this->getAuthenticatedCustomer();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Silakan login untuk menggunakan voucher',
                ],
            ], 401);
        }

        $cart = $this->getActiveCart($customer);

        if (!$cart) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NO_CART',
                    'message' => 'Keranjang tidak ditemukan',
                ],
            ], 400);
        }

        $result = $this->voucherService->validateVoucher($request->code, $cart, $customer);

        if (!$result->success) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => $result->errorCode,
                    'message' => $result->errorMessage,
                ],
            ], 400);
        }

        $voucher = $result->voucher;
        $type = $voucher->is_shipping ? 'shipping' : 'product';

        $result = $this->cookieService->saveVoucher($type, [
            'code' => $voucher->code,
            'name' => $voucher->name,
            'type' => $type,
        ]);

        $validatedVouchers = $this->voucherService->validateFromCookie($result['vouchers'], $cart, $customer);

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil dipilih',
            'data' => [
                'vouchers' => $validatedVouchers,
                'pending' => $result['vouchers'],
            ],
        ])->withCookie($result['cookie']);
    }

    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:shipping,product',
        ]);

        $customer = $this->getAuthenticatedCustomer();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Silakan login untuk menghapus voucher',
                ],
            ], 401);
        }

        $result = $this->cookieService->removeVoucher($request->type);

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil dihapus',
            'data' => [
                'pending' => $result['vouchers'],
            ],
        ])->withCookie($result['cookie']);
    }

    public function available(Request $request): JsonResponse
    {
        $customer = $this->getAuthenticatedCustomer();
        $cart = $this->getActiveCart($customer);

        $vouchers = $this->voucherService->getPublicVouchers();

        $availableVouchers = $vouchers->filter(function ($voucher) use ($cart, $customer) {
            $result = $this->voucherService->validateVoucher($voucher->code, $cart, $customer);
            return $result->success;
        });

        return response()->json([
            'success' => true,
            'data' => $availableVouchers->map(fn($v) => $this->formatVoucher($v)),
        ]);
    }

    protected function getAuthenticatedCustomer(): ?Customer
    {
        return Auth::guard('customer')->user();
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

    protected function formatVoucher(Voucher $voucher): array
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
